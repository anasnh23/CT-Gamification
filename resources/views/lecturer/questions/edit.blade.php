@extends('lecturer.layouts.app')

@section('content')
    <div class="question-form-page">
        <section class="question-form-hero">
            <p class="question-form-kicker">Perbarui Soal</p>
            <h1 class="question-form-title">Edit soal untuk challenge</h1>
            <p class="question-form-copy">
                Perbaiki teks pertanyaan, bantuan, pembahasan, skor, atau jawaban agar kualitas belajar mahasiswa tetap terjaga.
            </p>
        </section>

        @if ($errors->any())
            <div class="question-form-alert">
                <strong>Data belum bisa diperbarui.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="question-form" action="{{ route('lecturer.questions.update', $question->id) }}" method="POST"
            enctype="multipart/form-data" class="question-form-card bg-white">
            @csrf
            @method('PUT')

            <div class="question-form-grid two">
                <div>
                    <label for="challenge_id" class="question-form-label">Challenge</label>
                    <select name="challenge_id" id="challenge_id" class="question-form-input" required>
                        @foreach ($challenges as $challenge)
                            <option value="{{ $challenge->id }}" {{ (string) old('challenge_id', $question->challenge_id) === (string) $challenge->id ? 'selected' : '' }}>
                                {{ $challenge->section?->order ?? '-' }}. {{ $challenge->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type_display" class="question-form-label">Tipe Soal</label>
                    <select name="type_display" id="type_display" class="question-form-input" disabled>
                        <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                        <option value="true_false" {{ $question->type == 'true_false' ? 'selected' : '' }}>Benar / Salah</option>
                        <option value="essay" {{ $question->type == 'essay' ? 'selected' : '' }}>Esai</option>
                    </select>
                    <input type="hidden" name="type" value="{{ $question->type }}">
                </div>
            </div>

            <div class="question-form-grid one">
                <div>
                    <label for="description" class="question-form-label">Deskripsi / Konteks</label>
                    <textarea name="description" id="description" rows="4" class="question-form-input">{{ old('description', $question->description) }}</textarea>
                </div>

                <div>
                    <label for="question_text" class="question-form-label">Teks Pertanyaan</label>
                    <textarea name="question_text" id="question_text" rows="4" class="question-form-input" required>{{ old('question_text', $question->question_text) }}</textarea>
                </div>
            </div>

            <div class="question-form-grid two">
                <div>
                    <label for="help_text" class="question-form-label">Bantuan Saat Salah</label>
                    <textarea name="help_text" id="help_text" rows="4" class="question-form-input"
                        placeholder="Tuliskan petunjuk langkah pengerjaan tanpa langsung memberi jawaban.">{{ old('help_text', $question->help_text) }}</textarea>
                </div>

                <div>
                    <label for="explanation_text" class="question-form-label">Pembahasan Akhir</label>
                    <textarea name="explanation_text" id="explanation_text" rows="4" class="question-form-input"
                        placeholder="Tuliskan pembahasan yang akan muncul saat review.">{{ old('explanation_text', $question->explanation_text) }}</textarea>
                </div>
            </div>

            <div class="question-form-grid two">
                <div>
                    <label class="question-form-label">Gambar Saat Ini</label>
                    <div class="question-image-box">
                        @if ($question->question_image)
                            <div id="question-image-container" class="question-image-wrap">
                                <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question Image" class="question-current-image">
                                <button type="button" onclick="deleteQuestionImage()" class="question-mini-danger">Hapus</button>
                            </div>
                            <input type="hidden" name="delete_question_image" id="delete-question-image" value="0">
                        @else
                            <div id="question-image-container" class="question-image-empty">Belum ada gambar.</div>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="question_image" class="question-form-label">Upload Gambar Baru</label>
                    <input type="file" name="question_image" id="question_image" class="question-form-input file-input">
                </div>
            </div>

            <div class="question-form-grid two">
                <div>
                    <label for="score" class="question-form-label">Score</label>
                    <input type="number" name="score" id="score" class="question-form-input" value="{{ old('score', $question->score) }}" required>
                </div>

                <div>
                    <label for="exp" class="question-form-label">EXP</label>
                    <input type="number" name="exp" id="exp" class="question-form-input" value="{{ old('exp', $question->exp) }}" required>
                </div>
            </div>

            <div class="question-answer-panel">
                <div class="question-answer-head">
                    <div>
                        <p class="question-answer-kicker">Jawaban</p>
                        <h3 class="question-answer-title">Kelola kunci jawaban</h3>
                    </div>
                    @if ($question->type === 'multiple_choice')
                        <button type="button" id="add-answer-btn" onclick="addAnswer()" class="question-form-btn accent">Tambah Jawaban</button>
                    @endif
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
                        <tbody id="answers-body">
                            @if ($question->type === 'multiple_choice')
                                @foreach ($question->answers as $index => $answer)
                                    <tr class="answer-row">
                                        <td><input type="text" name="answers[]" value="{{ $answer->answer }}" required></td>
                                        <td>
                                            <div class="question-answer-image-stack" id="answer-image-container-{{ $index }}">
                                                @if ($answer->answer_image)
                                                    <img src="{{ asset('storage/' . $answer->answer_image) }}" alt="Answer Image" class="question-answer-image">
                                                @else
                                                    <span class="question-image-empty compact">Belum ada gambar.</span>
                                                @endif
                                                <input type="file" name="answer_images[]">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="is_correct[]" value="{{ $answer->is_correct ? '1' : '0' }}" class="is-correct-hidden">
                                            <input type="checkbox" name="is_correct[{{ $index }}]" value="1" class="correct-checkbox" {{ $answer->is_correct ? 'checked' : '' }} onclick="toggleCorrectAnswer(this)">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="question-form-btn" style="background:#ef4444;padding:10px 14px;" onclick="removeAnswer(this)">Hapus</button>
                                        </td>
                                        <input type="hidden" name="old_answer_images[{{ $index }}]" value="{{ $answer->answer_image }}">
                                    </tr>
                                @endforeach
                            @elseif ($question->type === 'true_false')
                                <tr>
                                    <td class="font-semibold">True</td>
                                    <td class="text-slate-500">-</td>
                                    <td class="text-center">
                                        <input type="radio" name="correct_answer" value="true" {{ $question->answers->where('answer', 'True')->first()?->is_correct ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center text-slate-500">-</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">False</td>
                                    <td class="text-slate-500">-</td>
                                    <td class="text-center">
                                        <input type="radio" name="correct_answer" value="false" {{ $question->answers->where('answer', 'False')->first()?->is_correct ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center text-slate-500">-</td>
                                </tr>
                            @elseif ($question->type === 'essay')
                                <tr>
                                    <td colspan="2">
                                        <textarea name="answers[]" rows="4" class="question-form-input" required>{{ $question->answers->first()->answer ?? '' }}</textarea>
                                    </td>
                                    <td class="text-center text-slate-500">Jawaban utama</td>
                                    <td class="text-center text-slate-500">-</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="question-form-actions">
                <a href="{{ route('lecturer.questions.index') }}" class="question-form-btn neutral">Kembali</a>
                <button type="submit" class="question-form-btn primary">Update Soal</button>
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
        .question-image-box { min-height: 140px; padding: 16px; border: 1px dashed #e9bfd0; border-radius: 18px; background: #fff7fa; }
        .question-image-wrap { display: flex; flex-direction: column; align-items: flex-start; gap: 12px; }
        .question-current-image, .question-answer-image { width: 120px; height: 120px; object-fit: cover; border-radius: 18px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.14); }
        .question-answer-image { width: 84px; height: 84px; border-radius: 16px; }
        .question-image-empty { color: #64748b; font-size: 14px; }
        .question-image-empty.compact { display: inline-block; }
        .question-mini-danger { border: 0; background: #ef4444; color: #fff; padding: 8px 12px; border-radius: 12px; font-weight: 700; cursor: pointer; }
        .question-answer-panel { margin-top: 10px; padding: 22px; border-radius: 24px; background: #fff7fa; border: 1px solid #f5c3d6; }
        .question-answer-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .question-answer-kicker { margin: 0; font-size: 12px; letter-spacing: 0.28em; text-transform: uppercase; color: #be185d; }
        .question-answer-title { margin: 8px 0 0; color: #1f2937; font-size: 24px; font-weight: 700; }
        .question-answer-table { width: 100%; border-collapse: collapse; }
        .question-answer-table th, .question-answer-table td { padding: 12px; border-bottom: 1px solid #f3d8e4; color: #334155; vertical-align: top; }
        .question-answer-table thead th { background: #fde8f1; color: #9f1d4f; font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; }
        .question-answer-image-stack { display: flex; flex-direction: column; gap: 10px; }
        .question-answer-image-stack input[type="file"], .answer-row input[type="text"] { width: 100%; padding: 12px 14px; border-radius: 14px; border: 1px solid #e9bfd0; box-sizing: border-box; color: #1f2937 !important; background: #fff !important; }
        .question-form-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 24px; }
        .question-form-btn { display: inline-flex; align-items: center; justify-content: center; padding: 14px 18px; border-radius: 16px; text-decoration: none; font-weight: 700; border: 0; cursor: pointer; color: #fff; }
        .question-form-btn.neutral { background: #64748b; }
        .question-form-btn.primary { background: linear-gradient(90deg, #c0265f, #ec4899); }
        .question-form-btn.accent { background: #0ea5e9; }
        @media (max-width: 768px) {
            .question-form-grid.two { grid-template-columns: 1fr; }
            .question-form-title { font-size: 32px; }
            .question-answer-head, .question-form-actions { flex-direction: column; align-items: stretch; }
        }
    </style>

    <script>
        function addAnswer() {
            const newRow = `<tr class="answer-row"><td><input type="text" name="answers[]" required></td><td><div class="question-answer-image-stack"><span class="question-image-empty compact">Belum ada gambar.</span><input type="file" name="answer_images[]"></div></td><td class="text-center"><input type="hidden" name="is_correct[]" value="0" class="is-correct-hidden"><input type="checkbox" class="correct-checkbox" onclick="toggleCorrectAnswer(this)"></td><td class="text-center"><button type="button" class="question-form-btn" style="background:#ef4444;padding:10px 14px;" onclick="removeAnswer(this)">Hapus</button></td></tr>`;
            document.getElementById('answers-body').insertAdjacentHTML('beforeend', newRow);
        }

        function toggleCorrectAnswer(checkbox) {
            const hiddenInput = checkbox.closest('tr').querySelector('.is-correct-hidden');
            if (hiddenInput) hiddenInput.value = checkbox.checked ? '1' : '0';
        }

        function removeAnswer(button) { button.closest('tr').remove(); }

        function deleteQuestionImage() {
            document.getElementById('question-image-container').innerHTML = "<div class='question-image-empty'>Belum ada gambar.</div>";
            let deleteInput = document.getElementById('delete-question-image');
            if (!deleteInput) {
                deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_question_image';
                deleteInput.id = 'delete-question-image';
                document.getElementById('question-form').appendChild(deleteInput);
            }
            deleteInput.value = '1';
        }
    </script>
@endsection
