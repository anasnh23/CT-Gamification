<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Challenge;
use App\Models\Question;
use App\Models\Section;
use Illuminate\Database\Seeder;

class BebrasQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $this->removeLegacySampleSection();

        $sections = [
            [
                'name' => 'Bebras',
                'order' => 1,
                'missions' => [
                    ['title' => 'Misi 1 - Pola dan Urutan', 'questions' => $this->sectionOneMissionOne()],
                    ['title' => 'Misi 2 - Instruksi dan Algoritma', 'questions' => $this->sectionOneMissionTwo()],
                    ['title' => 'Misi 3 - Logika dan Informasi', 'questions' => $this->sectionOneMissionThree()],
                ],
            ],
            [
                'name' => 'Bebras Lanjutan',
                'order' => 2,
                'missions' => [
                    ['title' => 'Misi 1 - Jejak dan Rute', 'questions' => $this->sectionTwoMissionOne()],
                    ['title' => 'Misi 2 - Data dan Simbol', 'questions' => $this->sectionTwoMissionTwo()],
                    ['title' => 'Misi 3 - Penyaringan Logika', 'questions' => $this->sectionTwoMissionThree()],
                ],
            ],
        ];

        $desiredSectionNames = collect($sections)->pluck('name')->all();

        Section::whereNotIn('name', $desiredSectionNames)->get()->each(function ($section) {
            $section->challenges()->each(function ($challenge) {
                $challenge->questions()->delete();
                $challenge->delete();
            });
            $section->delete();
        });

        foreach ($sections as $sectionData) {
            $section = Section::firstOrCreate(
                ['name' => $sectionData['name']],
                ['order' => $sectionData['order']]
            );

            $section->update(['order' => $sectionData['order']]);

            $desiredTitles = collect($sectionData['missions'])->pluck('title')->all();

            Challenge::where('section_id', $section->id)
                ->whereNotIn('title', $desiredTitles)
                ->get()
                ->each(function ($challenge) {
                    $challenge->questions()->delete();
                    $challenge->delete();
                });

            foreach ($sectionData['missions'] as $mission) {
                $challenge = Challenge::updateOrCreate(
                    ['section_id' => $section->id, 'title' => $mission['title']],
                    ['total_exp' => 0, 'total_score' => 0]
                );

                foreach ($mission['questions'] as $questionData) {
                    $question = Question::updateOrCreate(
                        ['challenge_id' => $challenge->id, 'question_text' => $questionData['question_text']],
                        collect($questionData)->except('answers')->toArray()
                    );

                    $question->answers()->delete();

                    foreach ($questionData['answers'] as $answerData) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer' => $answerData['answer'],
                            'is_correct' => $answerData['is_correct'],
                        ]);
                    }
                }

                $challenge->recalculateTotals();
            }
        }
    }

    protected function q(string $type, string $description, string $question, string $help, string $explanation, int $score, int $exp, array $answers): array
    {
        return [
            'type' => $type,
            'description' => $description,
            'question_text' => $question,
            'help_text' => $help,
            'explanation_text' => $explanation,
            'score' => $score,
            'exp' => $exp,
            'answers' => $answers,
        ];
    }

    protected function sectionOneMissionOne(): array
    {
        return [
            $this->q('multiple_choice', 'Empat beaver ingin menyeberangi jembatan sempit.', 'Beaver A 1 menit, B 2 menit, C 5 menit, D 10 menit. Pasangan mana yang paling tepat diseberangkan lebih dulu?', "Perhatikan dua beaver tercepat.\nMereka biasanya dipakai membawa obor bolak-balik.", 'Strategi efisien biasanya memakai dua beaver tercepat untuk membantu perpindahan obor, jadi pasangan awal paling masuk akal adalah A dan B.', 20, 10, [
                ['answer' => 'A dan B', 'is_correct' => true],
                ['answer' => 'A dan C', 'is_correct' => false],
                ['answer' => 'B dan D', 'is_correct' => false],
                ['answer' => 'C dan D', 'is_correct' => false],
            ]),
            $this->q('multiple_choice', 'Pola daun, bunga, daun, bunga, berulang terus.', 'Jika pola dimulai dari daun pada posisi ke-1, simbol apa yang muncul pada posisi ke-12?', "Cari panjang pola yang berulang.\nLalu cek posisi genap atau ganjil.", 'Pola panjangnya 2. Posisi genap selalu bunga, jadi posisi ke-12 adalah bunga.', 15, 10, [
                ['answer' => 'Daun', 'is_correct' => false],
                ['answer' => 'Bunga', 'is_correct' => true],
                ['answer' => 'Dua daun', 'is_correct' => false],
                ['answer' => 'Tidak bisa ditentukan', 'is_correct' => false],
            ]),
            $this->q('essay', 'Balok disusun dengan pola merah, biru, kuning berulang.', 'Balok ke-9 berwarna apa?', "Pola berulangnya 3 warna.\nKelompokkan posisi ke dalam kelipatan 3.", 'Urutan berulang tiap 3 balok. Posisi ke-9 jatuh pada warna ketiga, yaitu kuning.', 20, 12, [
                ['answer' => 'kuning', 'is_correct' => true],
            ]),
        ];
    }

    protected function sectionOneMissionTwo(): array
    {
        return [
            $this->q('multiple_choice', 'Seekor beaver robot bergerak di petak grid.', 'Instruksi robot: maju 2, kanan, maju 1, kiri, maju 2. Bentuk lintasannya seperti apa?', "Jalankan instruksi satu per satu.\nGambar perubahan arah setelah setiap belokan.", 'Robot bergerak ke atas, ke kanan, lalu ke atas lagi, sehingga lintasannya seperti huruf L.', 20, 10, [
                ['answer' => 'Garis lurus', 'is_correct' => false],
                ['answer' => 'Huruf L', 'is_correct' => true],
                ['answer' => 'Huruf U', 'is_correct' => false],
                ['answer' => 'Lingkaran', 'is_correct' => false],
            ]),
            $this->q('true_false', 'Masalah sering dipecah jadi langkah kecil.', 'Benar atau salah: memecah masalah besar membantu solusi lebih mudah diuji.', "Ingat konsep decomposition.\nMasalah besar dibuat menjadi beberapa bagian kecil.", 'Pernyataan ini benar karena decomposition membuat solusi lebih mudah dianalisis dan diuji.', 15, 8, [
                ['answer' => 'True', 'is_correct' => true],
                ['answer' => 'False', 'is_correct' => false],
            ]),
            $this->q('multiple_choice', 'Instruksi: ambil kartu paling atas, pindah ke bawah, lalu ambil kartu paling atas berikutnya.', 'Jika urutan awal A, B, C, D, kartu apa yang diambil pada langkah kedua?', "Simulasikan langkah pertama dulu.\nUrutan kartu berubah setelah kartu atas dipindah ke bawah.", 'A dipindah ke bawah sehingga urutan menjadi B, C, D, A. Jadi langkah kedua mengambil B.', 20, 12, [
                ['answer' => 'A', 'is_correct' => false],
                ['answer' => 'B', 'is_correct' => true],
                ['answer' => 'C', 'is_correct' => false],
                ['answer' => 'D', 'is_correct' => false],
            ]),
        ];
    }

    protected function sectionOneMissionThree(): array
    {
        return [
            $this->q('essay', 'Kue disusun pada kotak 3 x 3 dan hanya satu kotak kosong.', 'Berapa jumlah minimum baris yang perlu dicek untuk memastikan kotak kosong berada di baris terakhir?', "Jangan cek semua kotak.\nPikirkan informasi terbesar yang bisa menghilangkan kemungkinan lain.", 'Jika dua baris pertama penuh, maka kotak kosong pasti di baris terakhir. Jadi cukup cek 2 baris.', 25, 15, [
                ['answer' => '2', 'is_correct' => true],
            ]),
            $this->q('multiple_choice', 'Tiga kotak diberi label 1, 2, dan 3. Hanya satu kotak berisi hadiah.', 'Jika label 1 salah dan label 2 juga salah, hadiah paling logis ada di kotak mana?', "Gunakan eliminasi.\nKalau dua pilihan salah, cek yang tersisa.", 'Karena kotak 1 dan 2 salah, hadiah paling logis ada di kotak 3.', 20, 12, [
                ['answer' => 'Kotak 1', 'is_correct' => false],
                ['answer' => 'Kotak 2', 'is_correct' => false],
                ['answer' => 'Kotak 3', 'is_correct' => true],
                ['answer' => 'Tidak ada', 'is_correct' => false],
            ]),
            $this->q('multiple_choice', 'Pesan rahasia dibentuk dari pola titik dan garis.', 'Jika titik = 1 dan garis = 2, berapa nilai total pola titik-garis-garis-titik?', "Ubah simbol ke angka.\nLalu jumlahkan semuanya.", 'Pola itu bernilai 1 + 2 + 2 + 1 = 6.', 15, 10, [
                ['answer' => '4', 'is_correct' => false],
                ['answer' => '5', 'is_correct' => false],
                ['answer' => '6', 'is_correct' => true],
                ['answer' => '7', 'is_correct' => false],
            ]),
        ];
    }

    protected function sectionTwoMissionOne(): array
    {
        return [
            $this->q('multiple_choice', 'Beaver berjalan pada grid dan hanya boleh ke kanan atau ke atas.', 'Jika perlu 2 langkah ke kanan dan 2 ke atas, urutan mana yang valid?', "Selama jumlah geraknya pas, urutannya valid.\nPeriksa apakah ada langkah terlarang.", 'Urutan kanan-kanan-atas-atas valid karena memenuhi 2 langkah kanan dan 2 langkah atas.', 20, 12, [
                ['answer' => 'Kanan, kanan, atas, atas', 'is_correct' => true],
                ['answer' => 'Kanan, kiri, atas, atas', 'is_correct' => false],
                ['answer' => 'Atas, bawah, kanan, kanan', 'is_correct' => false],
                ['answer' => 'Kanan, kanan, kanan, atas', 'is_correct' => false],
            ]),
            $this->q('essay', 'Tujuan berada 4 petak lurus di depan tanpa hambatan.', 'Berapa langkah minimum untuk mencapainya?', "Karena tidak ada hambatan, pikirkan jalur terpendek.\nSetiap petak bernilai satu langkah.", 'Jika ada 4 petak di depan dan tidak ada hambatan, maka langkah minimum adalah 4.', 15, 10, [
                ['answer' => '4', 'is_correct' => true],
            ]),
            $this->q('true_false', 'Jika semua langkah punya biaya sama, jalur terpendek biasanya paling efisien.', 'Benar atau salah: jalur dengan langkah paling sedikit adalah pilihan paling efisien.', "Bandingkan jumlah langkah dengan biaya total.\nKalau biayanya sama, total biaya ikut jumlah langkah.", 'Pernyataan ini benar karena biaya total menjadi minimum saat jumlah langkah minimum.', 15, 8, [
                ['answer' => 'True', 'is_correct' => true],
                ['answer' => 'False', 'is_correct' => false],
            ]),
        ];
    }

    protected function sectionTwoMissionTwo(): array
    {
        return [
            $this->q('multiple_choice', 'Setiap simbol punya nilai: lingkaran = 2, segitiga = 3, persegi = 4.', 'Berapa total nilai lingkaran, segitiga, persegi?', "Ganti simbol jadi angka.\nSetelah itu jumlahkan.", 'Nilainya adalah 2 + 3 + 4 = 9.', 15, 10, [
                ['answer' => '7', 'is_correct' => false],
                ['answer' => '8', 'is_correct' => false],
                ['answer' => '9', 'is_correct' => true],
                ['answer' => '10', 'is_correct' => false],
            ]),
            $this->q('multiple_choice', 'Kode menggunakan aturan A = 1, B = 2, C = 3.', 'Berapa nilai total kode CAB?', "Ubah setiap huruf jadi angka.\nKemudian jumlahkan.", 'C = 3, A = 1, B = 2, jadi totalnya 6.', 20, 12, [
                ['answer' => '5', 'is_correct' => false],
                ['answer' => '6', 'is_correct' => true],
                ['answer' => '7', 'is_correct' => false],
                ['answer' => '8', 'is_correct' => false],
            ]),
            $this->q('essay', 'Data disimpan dalam kotak, masing-masing 5 benda per kotak.', 'Jika ada 15 benda, berapa kotak penuh yang terbentuk?', "Pikirkan pembagian ke dalam kelompok.\nSetiap kotak memuat 5 benda.", 'Lima belas benda dibagi 5 benda per kotak menghasilkan 3 kotak penuh.', 20, 12, [
                ['answer' => '3', 'is_correct' => true],
            ]),
        ];
    }

    protected function sectionTwoMissionThree(): array
    {
        return [
            $this->q('multiple_choice', 'Ada 4 kartu, hanya satu yang memenuhi aturan.', 'Jika kartu merah, bulat, dan besar ditolak, kartu mana yang paling mungkin dipilih?', "Gunakan eliminasi berdasarkan sifat yang ditolak.\nCari yang tidak punya ciri terlarang.", 'Jika merah, bulat, dan besar ditolak, maka kartu biru kecil menjadi pilihan yang paling logis.', 20, 12, [
                ['answer' => 'Kartu biru kecil', 'is_correct' => true],
                ['answer' => 'Kartu merah kecil', 'is_correct' => false],
                ['answer' => 'Kartu biru besar', 'is_correct' => false],
                ['answer' => 'Kartu bulat kecil', 'is_correct' => false],
            ]),
            $this->q('true_false', 'Filtering dipakai untuk menyaring data yang sesuai aturan.', 'Benar atau salah: filtering membantu mempersempit pilihan sebelum mengambil keputusan.', "Ingat tujuan filtering.\nFiltering membuang data yang tidak relevan.", 'Pernyataan ini benar karena filtering membantu fokus pada pilihan yang memenuhi syarat.', 15, 8, [
                ['answer' => 'True', 'is_correct' => true],
                ['answer' => 'False', 'is_correct' => false],
            ]),
        ];
    }

    protected function removeLegacySampleSection(): void
    {
        $legacySection = Section::where('name', 'manju')->first();

        if (! $legacySection) {
            return;
        }

        $legacySection->challenges()->each(function ($challenge) {
            $challenge->questions()->delete();
            $challenge->delete();
        });

        $legacySection->delete();
    }
}
