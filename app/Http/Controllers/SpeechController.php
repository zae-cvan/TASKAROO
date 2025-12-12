<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SpeechController extends Controller
{
    /**
     * Show the speech-to-text page with recent transcriptions
     */
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)->latest()->take(10)->get();
        return view('speech.index', compact('tasks'));
    }

    /**
     * Translate text via free MyMemory API (no key required)
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'to' => 'nullable|string',
            'from' => 'nullable|string'
        ]);

        $text = $request->input('text');
        $to = $request->input('to', 'en');
        $from = $request->input('from', 'auto');

        // Map language codes to MyMemory format (en, tl for Tagalog)
        // MyMemory requires 2-letter ISO codes or RFC3066 (e.g., zh-CN), does NOT accept 'auto'
        $mapCode = function ($c, $isSource = false) {
            if (!$c || $c === 'auto') {
                // For source language, default to 'en' if auto-detect requested
                return $isSource ? 'en' : 'en';
            }
            $cLower = strtolower($c);
            if (in_array($cLower, ['tl', 'fil', 'tl-ph', 'fil-ph'])) return 'tl';
            if (strpos($cLower, 'en') === 0) return 'en';
            return substr($cLower, 0, 2); // use first 2 chars (en, es, fr, etc.)
        };

        $source = $mapCode($from, true);  // true = is source language
        $target = $mapCode($to, false);

        try {
            // MyMemory is a free translation API with no API key needed.
            // Format: https://api.mymemory.translated.net/get?q=QUERY&langpair=SOURCE|TARGET
            $url = 'https://api.mymemory.translated.net/get';
            $params = [
                'q' => $text,
                'langpair' => "{$source}|{$target}"
            ];

            $res = Http::timeout(10)->get($url, $params);

            if ($res->successful()) {
                $body = $res->json();
                
                // MyMemory response structure: { responseData: { translatedText: "..." }, responseStatus: 200 }
                if (isset($body['responseData']['translatedText'])) {
                    $translated = $body['responseData']['translatedText'];
                    return response()->json(['translated' => $translated]);
                }

                Log::warning('MyMemory unexpected response', ['body' => $body]);
                return response()->json(['error' => 'unexpected_response'], 500);
            }

            Log::error('MyMemory request failed', ['status' => $res->status(), 'body' => $res->body()]);
            return response()->json(['error' => 'request_failed', 'status' => $res->status()], 500);
        } catch (\Exception $e) {
            Log::error('Translate exception: ' . $e->getMessage());
            return response()->json(['error' => 'exception', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Save transcribed text as a Task
     */
    public function save(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'language' => 'nullable|string'
        ]);

        $user = $request->user();

        $description = trim($request->input('text'));

        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'Voice Note',
            'description' => $description,
            'status' => 'active'
        ]);

        return response()->json(['success' => true, 'task_id' => $task->id, 'task' => $task]);
    }

    /**
     * Delete (soft-delete) a saved task
     */
    public function destroy(Request $request, $id)
    {
        $task = Task::find($id);
        if (! $task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        if ($task->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['success' => true]);
    }
}
