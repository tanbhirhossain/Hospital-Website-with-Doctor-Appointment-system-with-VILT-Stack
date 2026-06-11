<?php

namespace Modules\HEALTHAI\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\DOCTOR\Models\Doctor;
use Modules\HEALTHAI\Services\ExcelService;

class AiAgentController extends Controller
{
    // ওল্ল্যামা লোকাল প্রক্সি পোর্ট এবং আপনার লিস্টের সঠিক মডেল নেম
    protected $ollamaUrl = 'http://192.168.10.200:11434'; 
    protected $model = 'gemma4:31b-cloud';

    public function handleRequest(Request $request)
    {
        $userMessage = $request->input('message');

        if (empty($userMessage)) {
            return response()->json(['reply' => 'বিনীতভাবে জানাচ্ছি, আপনার প্রশ্নটি আমি বুঝতে পারিনি।']);
        }

        // ওল্ল্যামার এন্ডপয়েন্ট (চ্যাট এপিআই)
        $ollamaEndpoint = rtrim($this->ollamaUrl, '/') . '/api/chat';

        // ১. ওল্ল্যামাকে দিয়ে রোগীর মনের উদ্দেশ্য (Intent) এবং ক্লিন কিওয়ার্ড বের করা
        $routingPrompt = "
        You are an intelligent routing assistant for AMZ Hospital. 
        Analyze the user's input and determine their intent. The input can be in Bangla, English, or Banglish and Islamic word will use.

        Identify the correct action from these choices:
        1. 'EXCEL' - If they ask about test prices, lab fees, or medical investigations (e.g., 'CBC hoy kina', 'ECG er charge koto', 'রক্ত পরীক্ষা', 'scanning cost').
        2. 'DATABASE_DOCTOR' - If they ask about doctor availability, schedules, or finding a doctor for a symptom (e.g., 'হাড়ের ডাক্তার', 'skin specialist', 'পায়ে ব্যথা').
        3. 'GENERAL' - For greetings, hospital address, contact, or generic chit-chat (e.g., 'hi', 'hello', 'ধন্যবাদ').

        The 'search_keyword' MUST be a clean single English acronym or main word extracted from the request to search in our files (e.g., if user says 'CBC hoy kina' or 'রক্ত পরীক্ষা', search_keyword MUST be 'CBC'. If they say 'ECG koto', it must be 'ECG').

        Respond ONLY with a valid JSON object. Do not include markdown code block or any extra text.
        Format: {\"intent\": \"EXCEL|DATABASE_DOCTOR|GENERAL\", \"search_keyword\": \"CLEAN_KEYWORD\"}
        
        User message: '{$userMessage}'
        ";

        try {
            $routerResponse = Http::timeout(60)->post($ollamaEndpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $routingPrompt]
                ],
                'stream' => false,
                'format' => 'json'
            ]);

            if ($routerResponse->failed()) {
                Log::error('Ollama Router Connection Failed: ' . $routerResponse->body());
                return response()->json(['reply' => 'দুঃখিত, ওল্ল্যামা ক্লাউড রাউটার এই মুহূর্তে রেসপন্স করছে না।']);
            }

            $responseBody = $routerResponse->json();
            $rawText = $responseBody['message']['content'] ?? null;

            // জেসন পার্স করা
            $route = json_decode($rawText, true);
            $intent = $route['intent'] ?? 'GENERAL';
            $keyword = $route['search_keyword'] ?? '';

            // ২. ইন্টেন্ট অনুযায়ী ব্যাকএন্ড ডেটা প্রসেস করা
            return $this->executeAgentAction($intent, $keyword, $userMessage);

        } catch (\Exception $e) {
            Log::error('AI Agent Routing Exception: ' . $e->getMessage());
            return response()->json(['reply' => 'সিস্টেমে একটি অভ্যন্তরীণ ত্রুটি ঘটেছে। দয়া করে আবার চেষ্টা করুন।']);
        }
    }

   protected function executeAgentAction($intent, $keyword, $userMessage)
    {
        $contextData = [];

        // 💡 ফিক্স ১: ইউজার যদি জেনেরিক প্রশ্ন করে "কী কী টেস্ট হয়?" যার কোনো নির্দিষ্ট কিওয়ার্ড নেই
        if ($intent === 'EXCEL' && (empty($keyword) || in_array(strtolower(trim($keyword)), ['test', 'list', 'all', 'everything', 'shob'])) ) {
            return response()->json([
                'reply' => 'আমাদের AMZ হাসপাতালে প্যাথলজি, বায়োকেমিস্ট্রি, ইমেজিং, ডিএনএ প্রোফাইলিং সহ সব ধরণের আধুনিক টেস্টের সুব্যবস্থা রয়েছে। আমাদের টেস্টের তালিকা অত্যন্ত দীর্ঘ। আপনার সুনির্দিষ্টভাবে কোন টেস্ট বা পরীক্ষার মূল্য জানার প্রয়োজন, দয়া করে টেস্টের নামটি (যেমন: CBC, Lipid Profile, USG) আমাদের বলুন। আমি এখনই সেটির মূল্য এবং রুম নম্বর জানিয়ে দিচ্ছি।'
            ]);
        }

        if ($intent === 'EXCEL') {
            $excelService = new ExcelService();
            $contextData = $excelService->searchTestPrice($keyword);
        } 
        elseif ($intent === 'DATABASE_DOCTOR') {
            $contextData = ["info" => "ডাক্তার খোঁজার ডেটাবেজ এখনো প্রস্তুত নয়।"];
        }

        return $this->generateFinalReply($userMessage, $contextData);
    }

    protected function generateFinalReply($userMessage, $contextData)
    {
        // 💡 ফিক্স ২ ও ৩: পারসোনা কনসিস্টেন্সি এবং স্বাগতম লুপ বন্ধ করার প্রম্পট গাইডলাইন
    $finalPrompt = "
        You are the professional, polite, and neutral AI Assistant of AMZ Hospital. 
        User Query: '{$userMessage}'
        System Data: " . json_encode($contextData) . "

        STRICT RULES FOR RESPONSE GENERATION & FORMATTING:
        1. DO NOT say 'Welcome to AMZ Hospital' or 'নমস্কার' or 'আসসালামু আলাইকুম' in every reply.
        2. Keep a warm, polite, professional, and religiously NEUTRAL tone in Bangla.
        3. If multiple tests or data are found in System Data, you MUST format them using clear Markdown Bullet Points or Markdown Tables with proper bolding. Do not write everything in a single dense paragraph.
        
        Example Layout for Multiple Tests:
        আপনার অনুসন্ধান অনুযায়ী আমাদের এখানে **[키워ড]** সম্পর্কিত নিম্নলিখিত টেস্টগুলো উপলব্ধ আছে:

        * 📝 **Test Name 1:** ৳১,০০০
        * 📝 **Test Name 2:** ৳৮০০
        
        আপনার যদি আরও তথ্যের প্রয়োজন হয় বা অ্যাপয়েন্টমেন্ট বুক করতে চান, তবে আমাদের জানাতে পারেন। সুস্থ থাকুন।

        4. Use the Currency Symbol '৳' or 'টাকা' cleanly.
        5. If System Data is empty, politely state that this specific test info is not in the system.
        ";
            $ollamaEndpoint = rtrim($this->ollamaUrl, '/') . '/api/chat';

            try {
                $response = Http::timeout(60)->post($ollamaEndpoint, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $finalPrompt]
                    ],
                    'stream' => false
                ]);

                if ($response->failed()) {
                    return response()->json(['reply' => 'দুঃখিত, ফাইনাল রেসপন্স জেনারেট করার সময় ক্লাউড সার্ভারে সমস্যা হয়েছে।']);
                }

                $resData = $response->json();
                $replyText = $resData['message']['content'] ?? 'দুঃখিত, কোনো উত্তর তৈরি করা যায়নি।';

                return response()->json(['reply' => $replyText]);

            } catch (\Exception $e) {
                Log::error('Final Reply Exception: ' . $e->getMessage());
                return response()->json(['reply' => 'উত্তর তৈরি করার সময় একটি সমস্যা হয়েছে।']);
            }
        }
        public function index(){
            return Inertia::render('HEALTHAI::ChatAgent');
        }
    }