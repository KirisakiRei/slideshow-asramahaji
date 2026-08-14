<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupItemRequest;
use App\Http\Requests\UpdateGroupItemRequest;
use App\Models\Photo;
use App\Models\PhotoGroup;
use App\Models\PhotoGroupItem;

class GroupItemController extends Controller
{
    /**
     * Display photos in the group ordered by sort_order.
     */
    public function index(PhotoGroup $group)
    {
        $items = $group->items()->with('photo')->orderBy('sort_order')->get();

        // Media library: show ALL active media; mark which are already in this group
        $existingPhotoIds = $items->pluck('photo_id')->all();
        $allPhotos = Photo::where('is_active', true)->orderBy('title')->get();

        return view('admin.groups.items', compact('group', 'items', 'allPhotos', 'existingPhotoIds'));
    }

    /**
     * Add photos to the group (supports multiple photo_ids).
     */
    public function store(StoreGroupItemRequest $request, PhotoGroup $group)
    {
        $photoIds = $request->input('photo_ids', []);

        // Fallback to single photo_id if photo_ids not provided
        if (empty($photoIds) && $request->has('photo_id')) {
            $photoIds = [$request->input('photo_id')];
        }

        $maxSortOrder = $group->items()->max('sort_order') ?? 0;
        $added = 0;

        foreach ($photoIds as $photoId) {
            // Skip if already in group
            if ($group->items()->where('photo_id', $photoId)->exists()) {
                continue;
            }

            $maxSortOrder++;
            PhotoGroupItem::create([
                'photo_group_id' => $group->id,
                'photo_id' => $photoId,
                'sort_order' => $maxSortOrder,
                'is_active' => true,
            ]);
            $added++;
        }

        $message = $added > 1
            ? "{$added} media berhasil ditambahkan ke grup."
            : 'Media berhasil ditambahkan ke grup.';

        return redirect()->route('group-items.index', $group)
            ->with('success', $message);
    }

    /**
     * Update sort_order and/or is_active of a group item.
     */
    public function update(UpdateGroupItemRequest $request, PhotoGroup $group, PhotoGroupItem $item)
    {
        $data = [];

        // Only update sort_order if explicitly provided
        if ($request->has('sort_order')) {
            $data['sort_order'] = $request->input('sort_order');
        }

        // Only update is_active if explicitly provided
        if ($request->has('is_active')) {
            $data['is_active'] = (bool) $request->input('is_active');
        }

        if (!empty($data)) {
            $item->update($data);
        }

        return redirect()->route('group-items.index', $group)
            ->with('success', 'Item grup berhasil diperbarui.');
    }

    /**
     * Move an item up (swap with the item above it).
     */
    public function moveUp(PhotoGroup $group, PhotoGroupItem $item)
    {
        $items = $group->items()->orderBy('sort_order')->get();
        $currentIndex = $items->search(fn($i) => $i->id === $item->id);

        if ($currentIndex > 0) {
            $above = $items[$currentIndex - 1];
            $tempOrder = $item->sort_order;
            $item->update(['sort_order' => $above->sort_order]);
            $above->update(['sort_order' => $tempOrder]);
        }

        return redirect()->route('group-items.index', $group);
    }

    /**
     * Move an item down (swap with the item below it).
     */
    public function moveDown(PhotoGroup $group, PhotoGroupItem $item)
    {
        $items = $group->items()->orderBy('sort_order')->get();
        $currentIndex = $items->search(fn($i) => $i->id === $item->id);

        if ($currentIndex < $items->count() - 1) {
            $below = $items[$currentIndex + 1];
            $tempOrder = $item->sort_order;
            $item->update(['sort_order' => $below->sort_order]);
            $below->update(['sort_order' => $tempOrder]);
        }

        return redirect()->route('group-items.index', $group);
    }

    /**
     * Remove a photo from the group.
     */
    public function destroy(PhotoGroup $group, PhotoGroupItem $item)
    {
        $item->delete();

        return redirect()->route('group-items.index', $group)
            ->with('success', 'Media berhasil dihapus dari grup.');
    }
}
