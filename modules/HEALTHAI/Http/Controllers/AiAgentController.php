<?php

namespace Modules\HEALTHAI\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\HEALTHAI\Services\ExcelService;
use Modules\WEBSITE\Services\DoctorService;
use Modules\WEBSITE\Services\DepartmentService;

class AiAgentController extends Controller
{
    protected $ollamaUrl = 'http://192.168.10.200:11434'; 
    protected $model = 'gemma4:31b-cloud';

    public function handleRequest(Request $request)
    {
        $userMessage = $request->input('message');

        if (empty($userMessage)) {
            return response()->json(['reply' => 'বিনীতভাবে জানাচ্ছি, আপনার প্রশ্নটি আমি বুঝতে পারিনি।']);
        }

        $ollamaEndpoint = rtrim($this->ollamaUrl, '/') . '/api/chat';

        // 💡 অটোমেশন: ডাটাবেজ থেকে ডিপার্টমেন্টের নামগুলো নিয়ে রাউটারকে জানিয়ে দেওয়া হচ্ছে
        $departmentService = app(DepartmentService::class);
        $availableDepartments = $departmentService->getAllDepartmentsForAI()->pluck('name')->implode(', ');

        $routingPrompt = "
        You are an intelligent routing assistant for AMZ Hospital. 
        
        AVAILABLE DEPARTMENTS IN OUR DATABASE: [{$availableDepartments}]
        
        Identify the correct action for the user's message from these choices:
        1. 'EXCEL' - For test prices, lab fees, or investigations.
        2. 'DATABASE_DOCTOR' - For doctor availability, schedules, or finding a doctor.
        3. 'DATABASE_DEPARTMENT' - For hospital departments or wards.
        4. 'GENERAL' - For location, hotline, map, ambulance, ICU/NICU, bed availability, cafe, or greetings.

        CRITICAL RULES FOR 'search_keyword':
        - If intent is DOCTOR or DEPARTMENT: You MUST output the EXACT department name from the 'AVAILABLE DEPARTMENTS' list above that best matches the user's need.
        - If user describes a symptom (e.g., 'hare betha', 'fever', 'pregnant'), map it to the correct department from the list.
        - If they ask for ALL departments, output 'all'.
        - If intent is EXCEL, output the exact test name.
        
        Respond ONLY with a valid JSON object. 
        Format: {\"intent\": \"EXCEL|DATABASE_DOCTOR|DATABASE_DEPARTMENT|GENERAL\", \"search_keyword\": \"CLEAN_KEYWORD\"}
        
        User message: '{$userMessage}'
        ";

        try {
            $routerResponse = Http::timeout(60)->post($ollamaEndpoint, [
                'model' => $this->model,
                'messages' => [['role' => 'user', 'content' => $routingPrompt]],
                'stream' => false,
                'format' => 'json'
            ]);

            // 💡 JSON ক্লিনআপ (মার্কডাউন থাকলে রিমুভ করবে)
            $rawText = $routerResponse->json()['message']['content'] ?? '{}';
            $cleanJson = preg_replace('/```(?:json)?\n?(.*?)\n?```/s', '$1', $rawText);
            $route = json_decode(trim($cleanJson), true);

            $intent = $route['intent'] ?? 'GENERAL';
            $keyword = $route['search_keyword'] ?? '';

            // 💡 পিএইচপি (PHP) হার্ড-ফলব্যাক (আগের সেই মাস্টারস্ট্রোক!)
            if ($intent === 'GENERAL' || empty($intent)) {
                $msgLower = strtolower($userMessage);
                if (preg_match('/(bivag|department|ward|বিভাগ|ডিপার্টমেন্ট)/i', $msgLower)) {
                    $intent = 'DATABASE_DEPARTMENT';
                    $keyword = 'all'; 
                } elseif (preg_match('/(doctor|daktar|ডাক্তার)/i', $msgLower)) {
                    $intent = 'DATABASE_DOCTOR';
                    $keyword = 'all';
                } elseif (preg_match('/(test|price|mulla|tk|taka|খরচ|মূল্য|টেস্ট)/i', $msgLower)) {
                    $intent = 'EXCEL';
                }
            }

            Log::info("User: {$userMessage} | Intent: {$intent} | Keyword: {$keyword}");

            return $this->executeAgentAction($intent, $keyword, $userMessage);

        } catch (\Exception $e) {
            Log::error('AI Agent Routing Exception: ' . $e->getMessage());
            return response()->json(['reply' => 'সিস্টেমে একটি অভ্যন্তরীণ ত্রুটি ঘটেছে। দয়া করে আবার চেষ্টা করুন।']);
        }
    }

   protected function executeAgentAction($intent, $keyword, $userMessage)
    {
        $contextData = [];

        // 💡 আগের সেই ডিরেক্ট টেস্ট লিস্ট মেসেজ
        if ($intent === 'EXCEL' && (empty($keyword) || in_array(strtolower(trim($keyword)), ['test', 'list', 'all', 'everything', 'shob'])) ) {
            return response()->json([
                'reply' => 'আমাদের AMZ হাসপাতালে প্যাথলজি, বায়োকেমিস্ট্রি, ইমেজিং, ডিএনএ প্রোফাইলিং সহ সব ধরণের আধুনিক টেস্টের সুব্যবস্থা রয়েছে। আমাদের টেস্টের তালিকা অত্যন্ত দীর্ঘ। আপনার সুনির্দিষ্টভাবে কোন টেস্ট বা পরীক্ষার মূল্য জানার প্রয়োজন, দয়া করে টেস্টের নামটি (যেমন: CBC, Lipid Profile, USG) আমাদের বলুন। আমি এখনই সেটির মূল্য এবং রুম নম্বর জানিয়ে দিচ্ছি।'
            ]);
        }

        if ($intent === 'EXCEL') {
            $excelService = new ExcelService();
            $contextData = $excelService->searchTestPrice($keyword);
            
            if (empty($contextData)) {
                $contextData[] = ['Notice' => 'টেস্টের মূল্য ডাটাবেজে পাওয়া যায়নি। বিস্তারিত জানতে হটলাইনে কল করুন।'];
            }
        } 
        elseif ($intent === 'DATABASE_DOCTOR') {
            $doctorService = app(DoctorService::class);
            $cleanKeyword = strtolower(trim(str_replace(['"', "'"], '', $keyword)));
            $allDoctors = $doctorService->getAllDoctors();
            
            if ($cleanKeyword === 'all' || empty($cleanKeyword) || in_array($cleanKeyword, ['doctor', 'daktar', 'list'])) {
                $doctors = $allDoctors;
            } else {
                $doctors = $allDoctors->filter(function ($doctor) use ($cleanKeyword) {
                    return stripos($doctor->name, $cleanKeyword) !== false || 
                           stripos($doctor->designation ?? '', $cleanKeyword) !== false ||
                           ($doctor->department && stripos($doctor->department->name, $cleanKeyword) !== false);
                });
            }

            if ($doctors->isEmpty()) {
                $contextData[] = ['Notice' => "বর্তমানে '{$keyword}' বিভাগের নির্দিষ্ট কোনো ডাক্তারের শিডিউল সিস্টেমে নেই। অনুগ্রহ করে হেল্পলাইনে যোগাযোগ করুন।"];
            } else {
                foreach ($doctors as $doctor) {
                    $schedule = [];
                    foreach ($doctor->timetables ?? [] as $slot) {
                        $schedule[] = "{$slot->day}: {$slot->start_time} - {$slot->end_time}";
                    }
                    $contextData[] = [
                        'Type' => 'Doctor',
                        'Name' => $doctor->name,
                        'Designation' => $doctor->designation ?? 'Consultant',
                        'Department' => $doctor->department->name ?? 'General',
                        'Schedule' => !empty($schedule) ? implode(', ', $schedule) : 'শিডিউল জানতে কল করুন'
                    ];
                }
            }
        }
       elseif ($intent === 'DATABASE_DEPARTMENT') {
            $departmentService = app(DepartmentService::class);
            $cleanKeyword = strtolower(trim(str_ireplace(['department', 'bivag', 'ডিপার্টমেন্ট', 'বিভাগ', '"', "'", '?'], '', $keyword)));

            $departmentsPaginator = $departmentService->getAllDepartmentsForAI(); 
            $departments = is_object($departmentsPaginator) && method_exists($departmentsPaginator, 'items') 
                            ? $departmentsPaginator->items() 
                            : $departmentsPaginator;

            $isGeneralQuery = empty($cleanKeyword) || in_array($cleanKeyword, ['list', 'all', 'shob', 'ki ki', 'sob', 'everything']);

            foreach ($departments as $dept) {
                if ($isGeneralQuery || stripos($dept->name, $cleanKeyword) !== false) {
                    $contextData[] = ['Type' => 'Department', 'Name' => $dept->name];
                }
            }

            // 💡 ফলব্যাক: কিছু না পেলেও সব ডাটা দিয়ে দেবে
            if (empty($contextData)) {
                foreach ($departments as $dept) {
                    $contextData[] = ['Type' => 'Department', 'Name' => $dept->name];
                }
            }
        }

        return $this->generateFinalReply($userMessage, $contextData, $intent);
    }

    protected function generateFinalReply($userMessage, $contextData, $intent)
    {
        // 💡 হাসপাতালের সাধারণ তথ্যের একটি স্থায়ী ডাটাবেস
        $hospitalProfile = "
        AMZ HOSPITAL STATIC INFORMATION:
        - Location/Address: [আপনার হাসপাতালের ঠিকানা দিন, যেমন: Badda, Dhaka]
        - Google Map: [ম্যাপ লিঙ্ক দিন অথবা বলুন 'গুগল ম্যাপে AMZ Hospital সার্চ করুন']
        - Hotline/Phone: [আপনার হটলাইন নম্বর দিন, যেমন: 10699]
        - Website: [ওয়েবসাইটের লিঙ্ক দিন]
        - Ambulance: Yes, 24/7 available. Contact hotline.
        - ICU / NICU / CCU: Yes, fully equipped ICU and NICU are available.
        - Cafeteria/Cafe: Yes, we have a cafeteria for patients and visitors.
        - Bed Availability: Real-time bed status cannot be checked by AI. MUST ask the user to call the hotline.
        ";

        $finalPrompt = "
        You are the professional, polite, and helpful AI Assistant of AMZ Hospital. 
        User Query: '{$userMessage}'
        System Data: " . json_encode($contextData) . "
        
        {$hospitalProfile}

        STRICT RULES FOR RESPONSE GENERATION:
        1. DO NOT say 'Welcome to AMZ Hospital' or 'নমস্কার' in every reply. Keep it natural.
        2. Keep a warm, polite, professional tone in Bangla.
        3. If the user asks about Location, Hotline, Ambulance, ICU, NICU, Cafe, or Map, use the 'AMZ HOSPITAL STATIC INFORMATION' to answer confidently. Do NOT say data is unavailable.
        4. If the user asks about 'Bed Khali ase?' (Bed Availability): Tell them politely that AI cannot check real-time bed status and advise them to call the hotline. DO NOT list all departments.
        5. Formats based on System Data:
           - Tests/Cost: Use Markdown Bullet Points (* 📝 **Test Name:** ৳১,০০০).
           - Doctor: Format as: * 🩺 **[Doctor Name]** - [Department]\n  *Designation:* [Designation]\n  *Schedule:* [Schedule].
           - Department: Format as: * 🏢 **[Department Name]**.
        6. Do not generate fake doctors or fake prices.
        ";
        
        $ollamaEndpoint = rtrim($this->ollamaUrl, '/') . '/api/chat';

        try {
            $response = Http::timeout(60)->post($ollamaEndpoint, [
                'model' => $this->model,
                'messages' => [['role' => 'user', 'content' => $finalPrompt]],
                'stream' => false
            ]);

            $resData = $response->json();
            return response()->json(['reply' => $resData['message']['content'] ?? 'দুঃখিত, কোনো উত্তর তৈরি করা যায়নি।']);

        } catch (\Exception $e) {
            Log::error('Final Reply Exception: ' . $e->getMessage());
            return response()->json(['reply' => 'উত্তর তৈরি করার সময় একটি সমস্যা হয়েছে।']);
        }
    }

    public function index()
    {
        return Inertia::render('HEALTHAI::ChatAgent');
    }
}