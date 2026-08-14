<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoGroupRequest;
use App\Http\Requests\UpdatePhotoGroupRequest;
use App\Models\PhotoGroup;
use Illuminate\Http\Request;

class PhotoGroupController extends Controller
{
    /**
     * Display a paginated list of photo groups with search and filter.
     */
    public function index(Request $request)
    {
        $query = PhotoGroup::withCount('items');

        // Search by name (case-insensitive partial match)
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Active groups first (ordered by sort_order), inactive pushed to the bottom
        $groups = $query
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new photo group.
     */
    public function create()
    {
        return view('admin.groups.create');
    }

    /**
     * Store a newly created photo group.
     */
    public function store(StorePhotoGroupRequest $request)
    {
        $data = $request->validated();

        // Handle checkbox for is_active: default to false if not present
        $data['is_active'] = $request->has('is_active') ? (bool) $data['is_active'] : false;

        PhotoGroup::create($data);

        return redirect()->route('photo-groups.index')
            ->with('success', 'Grup slideshow berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified photo group.
     */
    public function edit(PhotoGroup $group)
    {
        return view('admin.groups.edit', compact('group'));
    }

    /**
     * Update the specified photo group.
     */
    public function update(UpdatePhotoGroupRequest $request, PhotoGroup $group)
    {
        $data = $request->validated();

        // Handle checkbox for is_active: default to false if not present
        $data['is_active'] = $request->has('is_active') ? (bool) $data['is_active'] : false;

        $group->update($data);

        return redirect()->route('photo-groups.index')
            ->with('success', 'Grup slideshow berhasil diperbarui.');
    }

    /**
     * Remove the specified photo group (cascade deletes PhotoGroupItems via DB foreign key).
     */
    public function destroy(PhotoGroup $group)
    {
        $group->delete();

        return redirect()->route('photo-groups.index')
            ->with('success', 'Grup slideshow berhasil dihapus.');
    }

    /**
     * Toggle active status of a group.
     * Activated groups get the next sort_order; deactivated groups reset to 0.
     * Active group ordering is re-normalized afterwards.
     */
    public function toggleActive(PhotoGroup $group)
    {
        $newStatus = !$group->is_active;

        if ($newStatus) {
            // Activating: put at the end of active list
            $maxOrder = PhotoGroup::where('is_active', true)->max('sort_order') ?? 0;
            $group->update(['is_active' => true, 'sort_order' => $maxOrder + 1]);
        } else {
            // Deactivating: remove from ordering
            $group->update(['is_active' => false, 'sort_order' => 0]);
        }

        $this->normalizeActiveOrder();

        $status = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('photo-groups.index')
            ->with('success', "Grup \"{$group->name}\" berhasil {$status}.");
    }

    /**
     * Move a group up (swap with the active group above it).
     */
    public function moveUp(PhotoGroup $group)
    {
        if (!$group->is_active) {
            return redirect()->route('photo-groups.index');
        }

        $this->normalizeActiveOrder();

        $activeGroups = PhotoGroup::where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')->get();
        $index = $activeGroups->search(fn ($g) => $g->id === $group->id);

        if ($index > 0) {
            $above = $activeGroups[$index - 1];
            $current = $activeGroups[$index];
            $tmp = $current->sort_order;
            $current->update(['sort_order' => $above->sort_order]);
            $above->update(['sort_order' => $tmp]);
        }

        return redirect()->route('photo-groups.index');
    }

    /**
     * Move a group down (swap with the active group below it).
     */
    public function moveDown(PhotoGroup $group)
    {
        if (!$group->is_active) {
            return redirect()->route('photo-groups.index');
        }

        $this->normalizeActiveOrder();

        $activeGroups = PhotoGroup::where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')->get();
        $index = $activeGroups->search(fn ($g) => $g->id === $group->id);

        if ($index < $activeGroups->count() - 1) {
            $below = $activeGroups[$index + 1];
            $current = $activeGroups[$index];
            $tmp = $current->sort_order;
            $current->update(['sort_order' => $below->sort_order]);
            $below->update(['sort_order' => $tmp]);
        }

        return redirect()->route('photo-groups.index');
    }

    /**
     * Re-number active groups sequentially starting from 1.
     */
    private function normalizeActiveOrder(): void
    {
        $activeGroups = PhotoGroup::where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')->get();

        foreach ($activeGroups as $i => $g) {
            $expected = $i + 1;
            if ($g->sort_order !== $expected) {
                $g->update(['sort_order' => $expected]);
            }
        }
    }
}
