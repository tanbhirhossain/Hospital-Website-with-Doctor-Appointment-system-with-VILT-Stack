<?php

namespace Modules\HEALTHAI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\HEALTHAI\Enums\AgentIntent;
use Modules\HEALTHAI\Services\Agent\AgentDataResolverFactory;
use Modules\HEALTHAI\Services\Agent\AiAgentService;

class AiAgentController extends Controller
{
    public function __construct(
        private readonly AiAgentService          $agentService,
        private readonly AgentDataResolverFactory $resolverFactory,
    ) {}

    public function handleRequest(Request $request): JsonResponse
    {
        $request->validate([
            'message'           => ['required', 'string', 'max:2000'],
            'history'           => ['sometimes', 'array', 'max:60'],
            'history.*.role'    => ['required_with:history', 'in:user,assistant,system'],
            'history.*.content' => ['required_with:history', 'string', 'max:8000'],
        ]);

        $userMessage = trim($request->input('message'));
        $history     = $request->input('history', []);

        // Hard-cap at 40 turns (20 exchanges) to avoid token blowout
        if (count($history) > 40) {
            $history = array_slice($history, -40);
        }

        try {
            $result = $this->agentService->handle($userMessage, $history);

            $reply          = $result['reply'];
            $updatedHistory = $result['history'];

            // ── Attach doctor card data for the frontend ──────────────────
            // When the last resolved intent returned doctor records with
            // image_url, we embed a hidden JSON comment into the reply.
            // The Vue component strips it out and renders doctor cards.
            $doctorPayload = $this->extractDoctorPayload($updatedHistory);
            if ($doctorPayload !== null) {
                $reply .= '<!--DOCTORS:' . json_encode($doctorPayload, JSON_UNESCAPED_UNICODE) . '-->';
            }

            return response()->json([
                'reply'   => $reply,
                'history' => $updatedHistory,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'reply'   => 'দুঃখিত, সার্ভারে একটি সমস্যা হয়েছে। অনুগ্রহ করে পরে আবার চেষ্টা করুন।',
                'history' => $history,
            ], 500);
        }
    }

    /**
     * After a doctor query, the agent service stores the last assistant reply
     * in history. We look at the SECOND-TO-LAST history entry (the user message)
     * to decide if this was a doctor query.
     *
     * A simpler approach: we re-resolve the intent from the last user message
     * and check if it is DATABASE_DOCTOR — then pull the data.
     * But to avoid a second LLM call we instead scan the last assistant content
     * for the pattern "Type":"Doctor" which our DoctorResolver always sets.
     */
    private function extractDoctorPayload(array $history): ?array
    {
        // Find the last assistant turn
        $lastAssistant = null;
        foreach (array_reverse($history) as $turn) {
            if (($turn['role'] ?? '') === 'assistant') {
                $lastAssistant = $turn['content'];
                break;
            }
        }

        if ($lastAssistant === null) return null;

        // Check if the reply contains an embedded doctor comment already being built
        // That means this turn itself had doctor data — skip double-attach
        if (str_contains($lastAssistant, '<!--DOCTORS:')) return null;

        // We can't re-inspect resolved data here cleanly without another call.
        // Instead, the controller receives doctor data through a shared context key
        // that AiAgentService sets. We use a lightweight sidecar pattern:
        // AiAgentService will set a static property if doctor data was returned.
        $doctors = AiAgentService::getLastDoctorPayload();
        if (!empty($doctors)) {
            AiAgentService::clearLastDoctorPayload();
            return $doctors;
        }

        return null;
    }
    
    public function index(){
        return Inertia::render('HEALTHAI::ChatAgent');
    }
}
