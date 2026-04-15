<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use App\Models\Section;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::orderBy('order')->get();

        $sectionSearch = $request->input('section_id');

        $challenges = Challenge::with(['section'])->withCount('questions')
            ->when($sectionSearch, function ($query, $sectionSearch) {
                return $query->where('section_id', $sectionSearch);
            })
            ->orderBy('section_id')
            ->orderBy('id')
            ->paginate(10);

        return view('lecturer.challenges.index', compact('challenges', 'sections', 'sectionSearch'));
    }

    public function create(Request $request)
    {
        $sections = Section::orderBy('order')->get();

        $selectedSectionId = $request->input('section_id');

        return view('lecturer.challenges.create', compact('sections', 'selectedSectionId'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
        ]);

        Challenge::create([
            'section_id' => $request->section_id,
            'title' => trim($request->title),
            'total_exp' => 0,
            'total_score' => 0,
        ]);

        return redirect()->route('lecturer.challenges.index')->with('success', 'Challenge berhasil dibuat.');
    }


    public function show(Challenge $challenge)
    {
        return view('lecturer.challenges.show', compact('challenge'));
    }

    public function edit($id)
    {
        $challenge = Challenge::findOrFail($id);
        $sections = Section::orderBy('order', 'asc')->get(); // Ambil semua section untuk dropdown
        return view('lecturer.challenges.edit', compact('challenge', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
        ]);

        $challenge = Challenge::findOrFail($id);
        $challenge->update([
            'section_id' => $request->section_id,
            'title' => trim($request->title),
        ]);

        return redirect()->route('lecturer.challenges.index')->with('success', 'Challenge berhasil diperbarui.');
    }


    public function destroy($id)
    {
        try {
            $challenge = Challenge::findOrFail($id);
            $title = $challenge->title; // Simpan judul challenge sebelum dihapus
            $challenge->delete();

            return redirect()->route('lecturer.challenges.index')
                ->with('success', "Challenge {$title} berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->route('lecturer.challenges.index')
                ->with('error', 'Challenge tidak bisa dihapus. Pastikan tidak ada data soal atau hasil yang masih terhubung.');
        }
    }
}
