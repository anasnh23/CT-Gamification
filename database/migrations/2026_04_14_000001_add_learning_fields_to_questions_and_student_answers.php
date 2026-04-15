<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->text('help_text')->nullable()->after('question_text');
            $table->text('explanation_text')->nullable()->after('help_text');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->foreignId('answer_id')->nullable()->after('attempt_number')->constrained('answers')->nullOnDelete();
            $table->text('answer_text')->nullable()->after('selected_answer');
            $table->boolean('used_help')->default(false)->after('is_correct');
            $table->timestamp('help_requested_at')->nullable()->after('used_help');
        });

        DB::table('student_answers')
            ->orderBy('result_id')
            ->get()
            ->each(function ($studentAnswer) {
                $selectedAnswer = $studentAnswer->selected_answer;

                if (is_numeric($selectedAnswer)) {
                    DB::table('student_answers')
                        ->where('result_id', $studentAnswer->result_id)
                        ->update([
                            'answer_id' => (int) $selectedAnswer,
                            'answer_text' => null,
                        ]);

                    return;
                }

                DB::table('student_answers')
                    ->where('result_id', $studentAnswer->result_id)
                    ->update([
                        'answer_text' => $selectedAnswer,
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('answer_id');
            $table->dropColumn(['answer_text', 'used_help', 'help_requested_at']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['help_text', 'explanation_text']);
        });
    }
};
