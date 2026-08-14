<?php

namespace App\Http\Controllers;

use App\Models\RunningText;
use Illuminate\Http\Request;

class RunningTextController extends Controller
{
    public function index()
    {
        $runningTexts = RunningText::orderBy('sort_order')->orderByDesc('is_active')->get();

        return view('admin.running-texts.index', compact('runningTexts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:500'],
        ]);

        $maxOrder = RunningText::max('sort_order') ?? 0;

        RunningText::create([
            'text' => $data['text'],
            'is_active' => true,
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('running-texts.index')
            ->with('success', 'Running Text berhasil ditambahkan.');
    }

    public function update(Request $request, RunningText $runningText)
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:500'],
        ]);

        $runningText->update([
            'text' => $data['text'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('running-texts.index')
            ->with('success', 'Running Text berhasil diperbarui.');
    }

    public function destroy(RunningText $runningText)
    {
        $runningText->delete();

        return redirect()->route('running-texts.index')
            ->with('success', 'Running Text berhasil dihapus.');
    }

    public function toggleActive(RunningText $runningText)
    {
        $runningText->update(['is_active' => !$runningText->is_active]);

        $status = $runningText->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('running-texts.index')
            ->with('success', "Running Text \"{$runningText->text}\" berhasil {$status}.");
    }

    public function moveUp(RunningText $runningText)
    {
        $this->normalizeOrder();

        $ordered = RunningText::orderBy('sort_order')->get();
        $index = $ordered->search(fn ($t) => $t->id === $runningText->id);

        if ($index > 0) {
            $this->swap($ordered[$index], $ordered[$index - 1]);
        }

        return redirect()->route('running-texts.index');
    }

    public function moveDown(RunningText $runningText)
    {
        $this->normalizeOrder();

        $ordered = RunningText::orderBy('sort_order')->get();
        $index = $ordered->search(fn ($t) => $t->id === $runningText->id);

        if ($index < $ordered->count() - 1) {
            $this->swap($ordered[$index], $ordered[$index + 1]);
        }

        return redirect()->route('running-texts.index');
    }

    private function swap(RunningText $a, RunningText $b): void
    {
        $tmp = $a->sort_order;
        $a->update(['sort_order' => $b->sort_order]);
        $b->update(['sort_order' => $tmp]);
    }

    private function normalizeOrder(): void
    {
        foreach (RunningText::orderBy('sort_order')->get() as $i => $item) {
            if ($item->sort_order !== $i + 1) {
                $item->update(['sort_order' => $i + 1]);
            }
        }
    }
}
