@extends('lecturer.layouts.app')

@section('content')
    <div class="question-form-page">
        <section class="question-form-hero">
            <p class="question-form-kicker">Questions</p>
            <h1 class="question-form-title">Tambah soal untuk challenge</h1>
        </section>

        @if ($errors->any())
            <div class="question-form-alert">
                <strong>Data belum bisa disimpan.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="question-form" action="{{ route('lecturer.questions.store') }}" method="POST" enctype="multipart/form-data"
            class="question-form-card bg-white">
            @csrf

            <div class="question-form-grid two">
                <div>
                    <label for="challenge_id" class="question-form-label">Challenge</label>
                    <select name="challenge_id" id="challenge_id" class="question-form-input" required>
                        <option value="">Pilih challenge</option>
                        @foreach ($challenges as $challenge)
                            <option value="{{ $challenge->id }}" {{ (string) old('challenge_id', $selectedChallengeId ?? '') === (string) $challenge->id ? 'selected' : '' }}>
                                {{ $challenge->section?->order ?? '-' }}. {{ $challenge->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="question-form-label">Tipe Soal</label>
                    <select name="type" id="type" class="question-form-input" required>
                        <option value="multiple_choice">Pilihan Ganda</option>
                        <option value="true_false">Benar / Salah</option>
                        <option value="essay">Esai</option>
                    </select>
                </div>
            </div>

            <div class="question-form-grid one">
                <div>
                    <label for="description" class="question-form-label">Deskripsi / Konteks</label>
                    <textarea name="description" id="description" rows="4" class="question-form-input">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="question_text" class="question-form-label">Teks Pertanyaan</label>
                    <textarea name="question_text" id="question_text" rows="4" class="question-form-input" required>{{ old('question_text') }}</textarea>
                </div>

                <div>
                    <label for="question_image" class="question-form-label">Gambar Soal (Opsional)</label>
                    <input type="file" name="question_image" id="question_image" class="question-form-input file-input">
                </div>
            </div>

            <div class="question-form-grid two">
                <div>
                    <label for="help_text" class="question-form-label">Bantuan Saat Salah</label>
                    <textarea name="help_text" id="help_text" rows="4" class="question-form-input"
                        placeholder="Tuliskan petunjuk langkah pengerjaan tanpa langsung memberi jawaban.">{{ old('help_text') }}</textarea>
                </div>

                <div>
                    <label for="explanation_text" class="question-form-label">Pembahasan Akhir</label>
                    <textarea name="explanation_text" id="explanation_text" rows="4" class="question-form-input"
                        placeholder="Tuliskan pembahasan yang akan muncul saat review.">{{ old('explanation_text') }}</textarea>
                </div>
            </div>

            <div class="question-form-grid two">
                <div>
                    <label for="score" class="question-form-label">Score</label>
                    <input type="number" name="score" id="score" class="question-form-input" value="{{ old('score') }}" required>
                </div>

                <div>
                    <label for="exp" class="question-form-label">EXP</label>
                    <input type="number" name="exp" id="exp" class="question-form-input" value="{{ old('exp') }}" required>
                </div>
            </div>

            <div class="question-answer-panel">
                <div class="question-answer-head">
                    <div>
                        <p class="question-answer-kicker">Jawaban</p>
                        <h3 class="question-answer-title">Atur kunci jawaban</h3>
                    </div>
                    <button type="button" id="add-answer-btn" onclick="addAnswer()" class="question-form-btn accent">Tambah Jawaban</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="question-answer-table">
                        <thead>
                            <tr>
                                <th>Jawaban</th>
                                <th>Gambar</th>
                                <th>Kunci</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="answers-body"></tbody>
                    </table>
                </div>
            </div>

            <div class="question-form-actions">
                <a href="{{ route('lecturer.questions.index') }}" class="question-form-btn neutral">Kembali</a>
                <button type="submit" id="submit-btn" class="question-form-btn primary">Simpan Soal</button>
            </div>
        </form>
    </div>

    <style>
        .question-form-page { max-width: 1100px; margin: 0 auto; }
        .question-form-hero { margin-bottom: 24px; padding: 28px; border-radius: 30px; border: 1px solid rgba(255, 228, 236, 0.14); background: rgba(74, 19, 39, 0.78); box-shadow: 0 20px 50px rgba(0, 0, 0, 0.22); }
        .question-form-kicker { margin: 0; font-size: 12px; letter-spacing: 0.34em; text-transform: uppercase; color: rgba(255, 228, 236, 0.75); }
        .question-form-title { margin: 12px 0 0; color: #fff; font-size: 40px; font-weight: 700; }
        .question-form-copy { margin: 14px 0 0; color: rgba(255, 240, 244, 0.76); line-height: 1.8; max-width: 760px; }
        .question-form-alert { margin-bottom: 16px; padding: 16px 18px; border-radius: 18px; background: rgba(254, 226, 226, 0.96); color: #991b1b; }
        .question-form-alert ul { margin: 10px 0 0 18px; }
        .question-form-card { padding: 24px; border-radius: 30px; }
        .question-form-grid { display: grid; gap: 20px; margin-bottom: 20px; }
        .question-form-grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .question-form-grid.one { grid-template-columns: 1fr; }
        .question-form-label { display: block; margin-bottom: 10px; color: #334155; font-weight: 700; }
        .question-form-input { width: 100%; padding: 14px 16px; border-radius: 16px; border: 1px solid #f0b6c9; box-sizing: border-box; color: #1f2937 !important; background: #fff !important; }
        .question-form-input::placeholder { color: #94a3b8; }
        .question-form-input option { color: #1f2937; background: #fff; }
        .file-input { padding: 12px; }
        .question-answer-panel { margin-top: 10px; padding: 22px; border-radius: 24px; background: #fff7fa; border: 1px solid #f5c3d6; }
        .question-answer-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .question-answer-kicker { margin: 0; font-size: 12px; letter-spacing: 0.28em; text-transform: uppercase; color: #be185d; }
        .question-answer-title { margin: 8px 0 0; color: #1f2937; font-size: 24px; font-weight: 700; }
        .question-answer-table { width: 100%; border-collapse: collapse; }
        .question-answer-table th, .question-answer-table td { padding: 12px; border-bottom: 1px solid #f3d8e4; color: #334155; vertical-align: top; }
        .question-answer-table thead th { background: #fde8f1; color: #9f1d4f; font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; }
        .question-form-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 24px; }
        .question-form-btn { display: inline-flex; align-items: center; justify-content: center; padding: 14px 18px; border-radius: 16px; text-decoration: none; font-weight: 700; border: 0; cursor: pointer; color: #fff; }
        .question-form-btn.neutral { background: #64748b; }
        .question-form-btn.primary { background: linear-gradient(90deg, #c0265f, #ec4899); }
        .question-form-btn.accent { background: #0ea5e9; }
        .answer-row input[type="text"], .answer-row input[type="file"], .answer-row textarea { width: 100%; padding: 12px 14px; border-radius: 14px; border: 1px solid #e9bfd0; box-sizing: border-box; color: #1f2937 !important; background: #fff !important; }
        @media (max-width: 768px) {
            .question-form-grid.two { grid-template-columns: 1fr; }
            .question-form-title { font-size: 32px; }
            .question-answer-head, .question-form-actions { flex-direction: column; align-items: stretch; }
        }
    </style>

    <script>
        const questionType = document.getElementById('type');
        const answersBody = document.getElementById('answers-body');
        const addAnswerBtn = document.getElementById('add-answer-btn');

        questionType.addEventListener('change', function() {
            if (this.value === 'essay') {
                resetToEssay();
            } else if (this.value === 'true_false') {
                resetToTrueFalse();
            } else {
                resetToMultipleChoice();
            }
        });

        function resetToEssay() {
            answersBody.innerHTML = `<tr><td><input type="text" name="answers[]" required></td><td class="text-slate-500">-</td><td class="text-center text-slate-500">Jawaban utama</td><td class="text-center text-slate-500">-</td></tr>`;
            addAnswerBtn.style.display = 'none';
        }

        function resetToTrueFalse() {
            answersBody.innerHTML = `
                <tr><td class="font-semibold">True</td><td class="text-slate-500">-</td><td class="text-center"><input type="radio" name="correct_answer" value="true" checked></td><td class="text-center text-slate-500">-</td></tr>
                <tr><td class="font-semibold">False</td><td class="text-slate-500">-</td><td class="text-center"><input type="radio" name="correct_answer" value="false"></td><td class="text-center text-slate-500">-</td></tr>`;
            addAnswerBtn.style.display = 'none';
        }

        function resetToMultipleChoice() {
            answersBody.innerHTML = `<tr class="answer-row"><td><input type="text" name="answers[]" required></td><td><input type="file" name="answer_images[]"></td><td class="text-center"><input type="hidden" name="is_correct[]" value="0" class="is-correct-hidden"><input type="checkbox" class="correct-checkbox" onclick="toggleCorrectAnswer(this)"></td><td class="text-center"><button type="button" class="question-form-btn" style="background:#ef4444;padding:10px 14px;" onclick="removeAnswer(this)">Hapus</button></td></tr>`;
            addAnswerBtn.style.display = 'inline-flex';
        }

        function addAnswer() {
            const newRow = `<tr class="answer-row"><td><input type="text" name="answers[]" required></td><td><input type="file" name="answer_images[]"></td><td class="text-center"><input type="hidden" name="is_correct[]" value="0" class="is-correct-hidden"><input type="checkbox" class="correct-checkbox" onclick="toggleCorrectAnswer(this)"></td><td class="text-center"><button type="button" class="question-form-btn" style="background:#ef4444;padding:10px 14px;" onclick="removeAnswer(this)">Hapus</button></td></tr>`;
            answersBody.insertAdjacentHTML('beforeend', newRow);
        }

        function removeAnswer(button) { button.closest('tr').remove(); }
        function toggleCorrectAnswer(checkbox) {
            const hiddenInput = checkbox.closest('tr').querySelector('.is-correct-hidden');
            hiddenInput.value = checkbox.checked ? '1' : '0';
        }

        resetToMultipleChoice();
    </script>
@endsection
