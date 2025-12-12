@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-3xl">

    <div class="bg-gradient-to-br from-white to-orange-50 rounded-3xl p-8 shadow-2xl border-2 border-orange-100 space-y-8">

        <!-- Title -->
        <div class="text-center border-b-2 border-orange-200 pb-6">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent mb-2">🎤 Record Task</h2>
            <p class="text-base text-gray-600 font-medium">Convert your speech into text in real-time</p>
        </div>

        <!-- Recognition Language -->
        <div class="space-y-3">
            <label class="block font-bold text-gray-700 flex items-center gap-2">
                <i data-lucide="languages" class="w-5 h-5 text-orange-600"></i>
                Recognition Language
            </label>
            <select id="recognitionLang"
                class="px-4 py-3 border-2 border-orange-200 rounded-xl w-full bg-white shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-semibold hover:border-orange-300">
                <option value="en-US">English (US)</option>
                <option value="en-GB">English (UK)</option>
                <option value="tl-PH">Tagalog (tl-PH)</option>
                <option value="fil-PH">Filipino (fil-PH)</option>
            </select>
            <p class="text-xs text-gray-500 font-medium">If one locale fails, try another.</p>
        </div>

        <!-- Recording Controls -->
        <div class="flex flex-wrap gap-3">
            <button id="startBtn"
                class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                <i data-lucide="mic" class="w-5 h-5"></i>
                Start
            </button>
            <button id="stopBtn"
                class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                <i data-lucide="square" class="w-5 h-5"></i>
                Stop
            </button>
            <button id="clearBtn"
                class="px-6 py-3 bg-gradient-to-r from-gray-300 to-gray-400 hover:from-gray-400 hover:to-gray-500 text-gray-800 font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
                Clear
            </button>
        </div>

        <!-- Transcript Box -->
        <div class="space-y-3">
            <label class="block font-bold text-gray-700 flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-orange-600"></i>
                Transcript
            </label>
            <textarea id="transcript" rows="6"
                class="w-full p-4 border-2 border-orange-200 rounded-xl bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 shadow-md hover:border-orange-300 font-medium resize-none"
                placeholder="Your transcribed text will appear here..."></textarea>
        </div>

        <!-- Audio Level Meter -->
        <div class="space-y-3">
            <label class="block font-bold text-gray-700 flex items-center gap-2">
                <i data-lucide="volume-2" class="w-5 h-5 text-orange-600"></i>
                Recording Level
            </label>
            <canvas id="levelMeter" width="600" height="40"
                class="w-full rounded-xl bg-gradient-to-r from-orange-50 to-orange-100 border-2 border-orange-200 shadow-md"></canvas>
        </div>

        <!-- Translation -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <label class="block font-bold text-gray-700 flex items-center gap-2">
                    <i data-lucide="globe" class="w-5 h-5 text-orange-600"></i>
                    Translate To
                </label>
                <select id="translateTo"
                    class="px-4 py-3 border-2 border-orange-200 rounded-xl w-full bg-white shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-semibold hover:border-orange-300">
                    <option value="en">English</option>
                    <option value="tl">Filipino / Tagalog</option>
                </select>
            </div>
            <div class="space-y-3">
                <label class="block font-bold text-gray-700 flex items-center gap-2">
                    <i data-lucide="globe" class="w-5 h-5 text-orange-600"></i>
                    Translate From
                </label>
                <select id="translateFrom"
                    class="px-4 py-3 border-2 border-orange-200 rounded-xl w-full bg-white shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-semibold hover:border-orange-300">
                    <option value="en">English</option>
                    <option value="tl">Filipino / Tagalog</option>
                </select>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-3 pt-4">
            <button id="translateBtn"
                class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                <i data-lucide="translate" class="w-5 h-5"></i>
                Translate
            </button>

            <button id="saveBtn"
                class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                Save as Task
            </button>

            <a href="{{ route('dashboard') }}"
                class="px-6 py-3 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                Cancel
            </a>
        </div>

        <!-- Status + Debug -->
        <div class="bg-gradient-to-br from-gray-50 to-orange-50 rounded-xl p-5 border-2 border-orange-100">
            <div id="status" class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-orange-600"></i>
                <span id="statusText">Ready</span>
            </div>
            <pre id="debug" class="text-xs bg-white p-4 rounded-lg border-2 border-orange-100 h-32 overflow-auto shadow-inner font-mono text-gray-700"></pre>
        </div>

    </div>
</div>
@endsection

    @section('scripts')
    <script>
    (() => {
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const clearBtn = document.getElementById('clearBtn');
        const translateBtn = document.getElementById('translateBtn');
        const saveBtn = document.getElementById('saveBtn');
        const recognitionLang = document.getElementById('recognitionLang');
        const transcriptEl = document.getElementById('transcript');
        const statusEl = document.getElementById('status');
        const levelCanvas = document.getElementById('levelMeter');
        const debugEl = document.getElementById('debug');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // STATE - Declare ALL variables FIRST, before any use
        let finalText = '';
        let userStopped = false;
        let audioStream = null;
        let audioContext = null;
        let analyser = null;
        let meterAnimation = null;

        // Web Speech API setup
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        let recognition = null;
        function dbg(msg, obj = null) {
            try {
                const now = new Date().toLocaleTimeString();
                const line = `[${now}] ${msg}` + (obj ? ' ' + JSON.stringify(obj) : '') + '\n';
                if (debugEl) debugEl.textContent = line + debugEl.textContent;
            } catch (e) { /* ignore */ }
            console.debug('[speech-debug]', msg, obj || '');
        }

        if (!SpeechRecognition) {
            statusEl.textContent = 'This browser does not support the Web Speech API. Try Chrome or Edge.';
            dbg('No Web Speech API available in this browser');
            startBtn.disabled = true;
            stopBtn.disabled = true;
        } else {
            dbg('Web Speech API available');
            recognition = new SpeechRecognition();
            recognition.continuous = true;
            recognition.interimResults = true;

            recognition.onstart = () => {
                statusEl.textContent = 'Listening...';
                startBtn.disabled = true;
                stopBtn.disabled = false;
            };

            recognition.onerror = (evt) => {
                console.error('Recognition error', evt);
                dbg('recognition.onerror', { error: evt.error });
                
                // Determine user-friendly error message
                let errorMsg = 'Recognition error: ';
                switch (evt.error) {
                    case 'no-speech':
                        errorMsg = 'No speech detected. Please speak clearly and try again.';
                        break;
                    case 'audio-capture':
                        errorMsg = 'Microphone not found or not accessible. Check your device.';
                        break;
                    case 'not-allowed':
                        errorMsg = 'Microphone access denied. Please allow permissions in your browser.';
                        break;
                    case 'network':
                        errorMsg = 'Network error. Please check your internet connection.';
                        break;
                    case 'aborted':
                        errorMsg = 'Recognition was stopped.';
                        break;
                    case 'service-not-allowed':
                        errorMsg = 'Speech recognition service is not available. Try HTTPS.';
                        break;
                    case 'bad-grammar':
                        errorMsg = 'Grammar error. Please try a different language.';
                        break;
                    case 'language-not-supported':
                        errorMsg = 'Selected language is not supported. Try another locale.';
                        break;
                    default:
                        errorMsg = 'Recognition error: ' + (evt.error || 'unknown');
                }
                
                statusEl.textContent = errorMsg;
                dbg('Error message shown: ' + errorMsg);
            };

            recognition.onend = () => {
                statusEl.textContent = 'Stopped.';
                startBtn.disabled = false;
                stopBtn.disabled = true;
                dbg('Recognition onend - userStopped: ' + userStopped);
                // Do NOT auto-restart - user must click Start again
            };

            recognition.onresult = (event) => {
                dbg('onresult: resultIndex=' + event.resultIndex + ' resultsLength=' + event.results.length);
                let interim = '';
                let final = '';

                // Process all results from resultIndex onwards
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const result = event.results[i];
                    if (!result || !result[0]) {
                        dbg('WARNING: result[' + i + '] is invalid');
                        continue;
                    }
                    
                    const isFinal = result.isFinal;
                    const transcript = result[0].transcript || '';
                    dbg('result[' + i + '] isFinal=' + isFinal + ' text="' + transcript + '"');
                    
                    if (isFinal) {
                        final += transcript + ' ';
                    } else {
                        interim += transcript;
                    }
                }

                // Update finalText with new final results
                if (final) {
                    final = final.trim();
                    finalText = finalText ? (finalText + ' ' + final) : final;
                    dbg('Updated finalText: "' + finalText + '"');
                }

                // CRITICAL: Update textarea value
                if (transcriptEl) {
                    const displayText = finalText + (interim ? ' ' + interim : '');
                    transcriptEl.value = displayText;
                    dbg('TEXTAREA UPDATED: "' + displayText + '"');
                    dbg('textarea.value length=' + transcriptEl.value.length);
                } else {
                    dbg('ERROR: transcriptEl is null!');
                }

                // Update status
                if (interim) {
                    statusEl.textContent = 'Speaking: ' + interim;
                } else {
                    statusEl.textContent = 'Listening...';
                }
            };
        }

        // Audio level meter using getUserMedia + AnalyserNode
        // NOTE: This might conflict with recognition's microphone access!
        // Try without meter first - if needed, refactor to share audio stream

        async function startMeter() {
            try {
                if (!audioStream) {
                    audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                }
                if (!audioContext) {
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                const source = audioContext.createMediaStreamSource(audioStream);
                analyser = audioContext.createAnalyser();
                analyser.fftSize = 256;
                source.connect(analyser);
                drawMeter();
                dbg('startMeter: audio access granted');
            } catch (e) {
                console.warn('Audio meter unavailable', e);
                dbg('startMeter failed: ' + e.message);
            }
        }

        function stopMeter() {
            if (meterAnimation) cancelAnimationFrame(meterAnimation);
            const ctx = levelCanvas.getContext('2d');
            ctx.clearRect(0, 0, levelCanvas.width, levelCanvas.height);
            try {
                if (audioStream) {
                    audioStream.getTracks().forEach(t => t.stop());
                    audioStream = null;
                }
                if (audioContext) {
                    audioContext.close();
                    audioContext = null;
                }
                analyser = null;
                dbg('stopMeter: audio tracks stopped');
            } catch (e) {
                console.warn('Error stopping audio stream', e);
                dbg('stopMeter error: ' + e.message);
            }
        }

        function drawMeter() {
            if (!analyser) return;
            const ctx = levelCanvas.getContext('2d');
            const bufferLength = analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);
            analyser.getByteTimeDomainData(dataArray);
            
            let sum = 0;
            for (let i = 0; i < bufferLength; i++) {
                const v = (dataArray[i] - 128) / 128;
                sum += v * v;
            }
            const rms = Math.sqrt(sum / bufferLength);
            const pct = Math.min(1, rms * 5);

            ctx.clearRect(0, 0, levelCanvas.width, levelCanvas.height);
            ctx.fillStyle = '#e6f4ff';
            ctx.fillRect(0, 0, levelCanvas.width, levelCanvas.height);
            ctx.fillStyle = '#60a5fa';
            ctx.fillRect(0, 0, levelCanvas.width * pct, levelCanvas.height);

            meterAnimation = requestAnimationFrame(drawMeter);
        }

        // Button handlers
        // ensure proper initial button state
        stopBtn.disabled = true;

        startBtn.addEventListener('click', async () => {
            userStopped = false;
            finalText = '';
            transcriptEl.value = '';
            statusEl.textContent = '';
            const lang = recognitionLang.value || 'en-US';
            dbg('START CLICKED - lang=' + lang + ', recognitionExists=' + (!!recognition));
            if (recognition) {
                recognition.lang = lang;
                try {
                    recognition.start();
                    dbg('recognition.start() called successfully');
                } catch (e) {
                    console.error('recognition.start() failed', e);
                    statusEl.textContent = 'Error: ' + (e.message || e);
                    dbg('ERROR: recognition.start() failed: ' + e.message);
                    return;
                }
            } else {
                statusEl.textContent = 'Speech Recognition not available';
                dbg('ERROR: recognition object is null!');
            }
            // Start the meter to show audio waves
            try {
                await startMeter();
            } catch (e) {
                console.warn('startMeter failed', e);
                dbg('startMeter failed: ' + e.message);
            }
        });

        stopBtn.addEventListener('click', () => {
            userStopped = true;
            dbg('STOP CLICKED - calling recognition.abort()');
            if (recognition) {
                try {
                    recognition.abort();
                    statusEl.textContent = 'Stopped.';
                    dbg('recognition.abort() called successfully');
                } catch (e) {
                    console.error('recognition.abort() failed', e);
                    dbg('ERROR: recognition.abort() failed: ' + e.message);
                }
            }
            // Stop the meter
            stopMeter();
        });

        clearBtn.addEventListener('click', () => {
            transcriptEl.value = '';
            statusEl.textContent = '';
        });

        translateBtn.addEventListener('click', async () => {
            const text = transcriptEl.value.trim();
            if (!text) return statusEl.textContent = 'Nothing to translate.';
            const to = document.getElementById('translateTo').value;
            const from = document.getElementById('translateFrom').value;
            statusEl.textContent = 'Translating...';
            try {
                const res = await fetch("{{ route('speech.translate') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ text, to, from })
                });
                let j = null;
                try { j = await res.json(); } catch (e) {
                    const txt = await res.text().catch(()=>null);
                    dbg('translate: invalid JSON', { status: res.status, text: txt });
                    statusEl.textContent = 'Translation failed (invalid response).';
                    return;
                }
                dbg('translate response', j);
                if (j.translated) {
                    transcriptEl.value = j.translated;
                    statusEl.textContent = 'Translation complete.';
                } else {
                    statusEl.textContent = 'Translation failed.';
                    console.warn(j);
                }
            } catch (e) {
                statusEl.textContent = 'Translation error.';
                console.error(e);
            }
        });

        saveBtn.addEventListener('click', (e) => {
            const text = transcriptEl.value.trim();
            if (!text) return statusEl.textContent = 'Nothing to save.';
            // Store the transcribed text in sessionStorage so the Create Task page can load it for editing
            try {
                sessionStorage.setItem('speechText', text);
                sessionStorage.setItem('speechLanguage', recognitionLang.value || 'en-US');
                dbg('Saved transcript to sessionStorage, redirecting to task create');
                // Redirect to the task creation page so user can edit before saving
                window.location.href = "{{ route('tasks.create') }}";
            } catch (err) {
                dbg('sessionStorage set failed', { message: err.message });
                statusEl.textContent = 'Unable to prepare task for editing.';
            }
        });

    })();
    </script>
    @endsection