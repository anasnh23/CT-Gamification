<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge Question</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon-ctg.png') }}">
</head>

<body class="min-h-screen bg-gradient-to-br from-[#240812] via-[#451127] to-[#66203d] text-white">
    @php
        $correctCount = $question->type === 'multiple_choice' ? $question->answers->where('is_correct', true)->count() : 0;
        $isMultipleAnswer = $question->type === 'multiple_choice' && $correctCount > 1;
    @endphp

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-[#fff8f8] text-slate-900 rounded-3xl shadow-xl border border-rose-200/40 overflow-hidden">
            <div class="bg-[#4a1327] text-white px-6 py-5 flex items-center justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-300">Belajar Challenge</p>
                    <h1 class="text-2xl font-bold mt-1">Kerjakan soal dan pahami langkahnya</h1>
                </div>
                <button onclick="openExitModal()"
                    class="rounded-full border border-slate-600 px-4 py-2 text-sm hover:bg-slate-800 transition">
                    Keluar
                </button>
            </div>

            <div class="px-6 py-5 border-b border-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="rounded-2xl bg-rose-50 border border-rose-200 px-4 py-3">
                        <p class="text-xs uppercase tracking-wider text-rose-500">Lives</p>
                        <p id="lives-count" class="text-xl font-bold text-rose-700">...</p>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between text-sm text-slate-500 mb-2">
                            <span>Progress</span>
                            <span>{{ round($progress) }}%</span>
                        </div>
                        <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-3 bg-gradient-to-r from-emerald-400 to-sky-500 rounded-full"
                                style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>

                @if ($question->description)
                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                        <p class="text-sm font-semibold text-slate-500 mb-1">Konteks Soal</p>
                        <p class="leading-7 text-slate-700">{!! nl2br(e($question->description)) !!}</p>
                    </div>
                @endif
            </div>

            <div class="px-6 py-6 space-y-6">
                @if ($question->question_image)
                    <div class="flex justify-center">
                        <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question Image"
                            class="max-h-72 rounded-2xl shadow-md border border-slate-200">
                    </div>
                @endif

                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-amber-600 font-semibold">Tantangan</p>
                    <h2 class="text-2xl font-bold leading-tight mt-2">{!! nl2br(e($question->question_text)) !!}</h2>
                </div>

                @if ($question->type === 'multiple_choice')
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach ($question->answers as $answer)
                            <button type="button"
                                class="answer-option rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left font-semibold hover:border-sky-400 hover:bg-sky-50 transition"
                                data-answer-id="{{ $answer->id }}"
                                @if (! $isMultipleAnswer) onclick="submitSingleAnswer({{ $answer->id }})" @else onclick="toggleMultiAnswer(this, {{ $answer->id }})" @endif>
                                @if ($answer->answer_image)
                                    <img src="{{ asset('storage/' . $answer->answer_image) }}" alt="Answer Image"
                                        class="max-h-24 mb-3 rounded-xl border border-slate-200">
                                @endif
                                <span>{{ $answer->answer }}</span>
                            </button>
                        @endforeach
                    </div>

                    @if ($isMultipleAnswer)
                        <button id="submit-multi-btn" type="button" onclick="submitMultiAnswer()"
                            class="hidden rounded-2xl bg-emerald-500 text-white px-5 py-3 font-semibold hover:bg-emerald-600 transition">
                            Submit Jawaban
                        </button>
                    @endif
                @endif

                @if ($question->type === 'true_false')
                    <div class="grid md:grid-cols-2 gap-4">
                        <button type="button" onclick="submitSingleAnswer('true')"
                            class="answer-option rounded-2xl bg-emerald-50 border border-emerald-200 p-5 text-left font-semibold hover:bg-emerald-100 transition">
                            True
                        </button>
                        <button type="button" onclick="submitSingleAnswer('false')"
                            class="answer-option rounded-2xl bg-rose-50 border border-rose-200 p-5 text-left font-semibold hover:bg-rose-100 transition">
                            False
                        </button>
                    </div>
                @endif

                @if ($question->type === 'essay')
                    <div class="space-y-4">
                        <textarea id="essay-answer" rows="6"
                            class="w-full rounded-2xl border border-slate-300 p-4 focus:border-sky-500 focus:ring-sky-500"
                            placeholder="Tulis jawabanmu di sini..."></textarea>
                        <button type="button" onclick="submitEssayAnswer()"
                            class="rounded-2xl bg-sky-600 text-white px-5 py-3 font-semibold hover:bg-sky-700 transition">
                            Submit Jawaban
                        </button>
                    </div>
                @endif

                <div id="result-box" class="hidden rounded-2xl p-4"></div>

                <div id="help-actions" class="hidden">
                    <button type="button" onclick="requestHelp()"
                        class="rounded-2xl bg-amber-500 text-white px-5 py-3 font-semibold hover:bg-amber-600 transition">
                        Lihat Bantuan Cara Mengerjakan
                    </button>
                </div>

                <div id="help-box" class="hidden rounded-2xl bg-amber-50 border border-amber-200 p-5">
                    <p class="text-sm uppercase tracking-[0.25em] text-amber-600 font-semibold mb-2">Bantuan</p>
                    <div id="help-text" class="leading-7 text-slate-700"></div>
                </div>

                <div class="pt-2">
                    <button id="next-btn" type="button" onclick="nextQuestion()"
                        class="hidden rounded-2xl bg-slate-900 text-white px-5 py-3 font-semibold hover:bg-slate-800 transition">
                        Lanjut Soal Berikutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="exit-modal" class="hidden fixed inset-0 bg-slate-950/70 px-4">
        <div class="min-h-screen flex items-center justify-center">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-slate-900">Keluar dari challenge?</h3>
                <p class="text-slate-600 mt-2">Progress attempt yang sedang berjalan akan dihapus.</p>
                <div class="flex gap-3 mt-6">
                    <button onclick="closeExitModal()"
                        class="flex-1 rounded-2xl border border-slate-300 px-4 py-3 font-semibold text-slate-700">
                        Batal
                    </button>
                    <button onclick="confirmExit()"
                        class="flex-1 rounded-2xl bg-rose-600 text-white px-4 py-3 font-semibold">
                        Ya, keluar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedMultiAnswers = [];
        let answerLocked = false;
        let helpOpened = false;

        function updateLives() {
            $.get("{{ route('student.check.lives') }}", function(response) {
                $("#lives-count").text(response.lives);
            });
        }

        function lockAnswers() {
            answerLocked = true;
            $(".answer-option").prop("disabled", true).addClass("opacity-60 cursor-not-allowed");
            $("#essay-answer").prop("disabled", true);
            $("#submit-multi-btn").prop("disabled", true).addClass("opacity-60 cursor-not-allowed");
        }

        function unlockAnswers() {
            answerLocked = false;
            $(".answer-option").prop("disabled", false).removeClass("opacity-60 cursor-not-allowed");
            $("#essay-answer").prop("disabled", false);
            $("#submit-multi-btn").prop("disabled", false).removeClass("opacity-60 cursor-not-allowed");
        }

        function handleAnswerResponse(data) {
            const resultBox = $("#result-box");
            resultBox.removeClass(
                    "hidden bg-emerald-50 border-emerald-200 text-emerald-700 bg-rose-50 border-rose-200 text-rose-700 bg-amber-50 border-amber-200 text-amber-700")
                .addClass(data.is_correct ? "bg-emerald-50 border border-emerald-200 text-emerald-700" :
                    "bg-rose-50 border border-rose-200 text-rose-700");

            resultBox.html(data.is_correct ?
                "<strong>Jawaban benar.</strong> Lanjutkan ke soal berikutnya." :
                (data.has_help ?
                    "<strong>Jawaban belum tepat.</strong> Buka bantuan lalu coba jawab lagi." :
                    "<strong>Jawaban belum tepat.</strong> Lanjutkan ke soal berikutnya."));

            if (!data.is_correct && data.has_help) {
                $("#help-actions").removeClass("hidden");
                $("#next-btn").addClass("hidden");
                unlockAnswers();
            } else {
                $("#help-actions").addClass("hidden");
                lockAnswers();
                $("#next-btn").removeClass("hidden");
            }

            updateLives();

            if (data.lives !== undefined && data.lives <= 0) {
                setTimeout(() => {
                    window.location.href = "{{ route('student.mission.index') }}";
                }, 1500);
            }
        }

        function submitSingleAnswer(answerId) {
            if (answerLocked) {
                return;
            }

            $(".answer-option").removeClass("ring-2 ring-sky-500");
            $(`.answer-option[data-answer-id="${answerId}"]`).addClass("ring-2 ring-sky-500");

            $.post("{{ route('student.question.check') }}", {
                _token: "{{ csrf_token() }}",
                question_id: {{ $question->id }},
                selected_answer: answerId
            }, handleAnswerResponse);
        }

        function toggleMultiAnswer(button, answerId) {
            if (answerLocked) {
                return;
            }

            $(button).toggleClass("ring-2 ring-sky-500 bg-sky-50");

            const index = selectedMultiAnswers.indexOf(answerId);
            if (index > -1) {
                selectedMultiAnswers.splice(index, 1);
            } else {
                selectedMultiAnswers.push(answerId);
            }

            if (selectedMultiAnswers.length > 0) {
                $("#submit-multi-btn").removeClass("hidden");
            } else {
                $("#submit-multi-btn").addClass("hidden");
            }
        }

        function submitMultiAnswer() {
            if (answerLocked || selectedMultiAnswers.length === 0) {
                return;
            }

            $.post("{{ route('student.question.checkMultiple') }}", {
                _token: "{{ csrf_token() }}",
                question_id: {{ $question->id }},
                selected_answers: selectedMultiAnswers
            }, handleAnswerResponse);
        }

        function submitEssayAnswer() {
            if (answerLocked) {
                return;
            }

            const answer = $("#essay-answer").val().trim();
            if (!answer) {
                alert("Masukkan jawaban terlebih dahulu.");
                return;
            }

            $.post("{{ route('student.check.essay') }}", {
                _token: "{{ csrf_token() }}",
                question_id: {{ $question->id }},
                answer: answer
            }, handleAnswerResponse);
        }

        function requestHelp() {
            $.post("{{ route('student.question.help') }}", {
                _token: "{{ csrf_token() }}",
                question_id: {{ $question->id }}
            }, function(data) {
                helpOpened = true;
                $("#help-text").html((data.help_text || "").replace(/\n/g, "<br>"));
                $("#help-box").removeClass("hidden");
                $("#help-actions").addClass("hidden");
                $("#result-box").removeClass("hidden")
                    .removeClass("bg-rose-50 border-rose-200 text-rose-700")
                    .addClass("bg-amber-50 border border-amber-200 text-amber-700")
                    .html("<strong>Bantuan dibuka.</strong> Sekarang coba jawab lagi dengan langkah yang sudah dipahami.");
                unlockAnswers();
            });
        }

        function nextQuestion() {
            window.location.href = "{{ route('student.next.question', ['challenge_id' => $question->challenge_id]) }}";
        }

        function openExitModal() {
            $("#exit-modal").removeClass("hidden");
        }

        function closeExitModal() {
            $("#exit-modal").addClass("hidden");
        }

        function confirmExit() {
            $.post("{{ route('student.question.exit') }}", {
                _token: "{{ csrf_token() }}",
                challenge_id: "{{ $question->challenge_id }}"
            }, function() {
                window.location.href = "{{ route('student.mission.index') }}";
            });
        }

        $(document).ready(function() {
            updateLives();
        });
    </script>
</body>

</html>
