<?php

namespace Modules\HEALTHAI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\HEALTHAI\Services\Agent\AiAgentService;

class AiAgentController extends Controller
{
    public function __construct(private readonly AiAgentService $agentService) {}

    public function handleRequest(Request $request): JsonResponse
    {
        $request->validate([
            'message'   => ['required', 'string', 'max:2000'],
            'history'   => ['sometimes', 'array'],
            'history.*.role'    => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string'],
        ]);

        $userMessage = trim($request->input('message'));
        $history     = $request->input('history', []);

        // Hard-cap history to last 30 turns (15 exchanges) to avoid token blowout
        if (count($history) > 30) {
            $history = array_slice($history, -30);
        }

        try {
            $result = $this->agentService->handle($userMessage, $history);

            return response()->json([
                'reply'   => $result['reply'],
                'history' => $result['history'],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'reply'   => 'দুঃখিত, সার্ভারে একটি সমস্যা হয়েছে। অনুগ্রহ করে পরে আবার চেষ্টা করুন।',
                'history' => $history,
            ], 500);
        }
    }

    public function index()
    {
        return Inertia::render('HEALTHAI::ChatAgent');
    }
}
