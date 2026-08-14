<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Photo;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    /**
     * Display a paginated grid of media with search and filter support.
     */
    public function index(Request $request)
    {
        $query = Photo::where('type', 'photo');

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%'.$request->input('search').'%');
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $photos = $query->latest()->paginate(12)->withQueryString();

        return view('admin.photos.index', compact('photos'));
    }

    /**
     * Show the upload form.
     */
    /**
     * Store a newly uploaded file (photo or video).
     */
    public function store(StorePhotoRequest $request)
    {
        $validated = $request->validated();
        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $uniqueFilename = uniqid().'_'.$originalName;

        // Determine type based on extension
        $videoExtensions = ['mp4', 'mkv', 'mov'];
        $type = in_array($extension, $videoExtensions) ? 'video' : 'photo';

        // Store in separate folders
        $folder = $type === 'video' ? 'videos' : 'photos';

        try {
            $path = $file->storeAs($folder, $uniqueFilename, 'public');

            if ($path === false) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Gagal mengunggah file.'], 500);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal mengunggah file. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            Log::error('File upload failed: '.$e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => 'Gagal mengunggah file.'], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengunggah file. Silakan coba lagi.');
        }

        // Determine title
        $title = $validated['title'] ?? null;
        if (empty($title)) {
            $title = pathinfo($originalName, PATHINFO_FILENAME);
            $title = mb_substr($title, 0, 255);
        }

        $isActive = $validated['is_active'] ?? true;

        $photo = Photo::create([
            'title' => $title,
            'file_path' => $folder.'/'.$uniqueFilename,
            'type' => $type,
            'is_active' => $isActive,
        ]);

        $successMsg = $type === 'video' ? 'Video berhasil diunggah.' : 'Foto berhasil diunggah.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'media' => $this->mediaPayload($photo),
            ]);
        }

        return redirect()->route('photos.index')
            ->with('success', $successMsg);
    }

    /**
     * Show the edit form.
     */
    public function edit(Photo $photo)
    {
        return view('admin.photos.edit', [
            'photo' => $photo,
            'cropTemplates' => Setting::cropTemplates(),
            'cropTemplateLabels' => [
                'main' => 'Slideshow Utama',
                'facilities' => 'Fasilitas',
                'next_event' => 'Event Selanjutnya',
            ],
        ]);
    }

    /**
     * Update the specified media.
     */
    public function update(UpdatePhotoRequest $request, Photo $photo)
    {
        $validated = $request->validated();

        $photo->update([
            'title' => $validated['title'],
            'is_active' => $request->has('is_active') ? (bool) $validated['is_active'] : false,
            'focus_x' => $validated['focus_x'] ?? $photo->focus_x ?? 50,
            'focus_y' => $validated['focus_y'] ?? $photo->focus_y ?? 50,
            'crop_zoom' => $validated['crop_zoom'] ?? $photo->crop_zoom ?? 100,
            'crop_data' => $this->normalizeCropData($validated['crop_data'] ?? null, $photo),
        ]);

        return redirect()->route('photos.index')
            ->with('success', 'Foto berhasil diperbarui.');
    }

    /**
     * Build a complete per-slot framing payload, filling unspecified slots
     * with the legacy single framing so every photo always has data for all slots.
     */
    private function normalizeCropData(?array $cropData, Photo $photo): ?array
    {
        if (! is_array($cropData)) {
            return null;
        }

        $legacy = [
            'fx' => max(0, min(100, (int) ($photo->focus_x ?? 50))),
            'fy' => max(0, min(100, (int) ($photo->focus_y ?? 50))),
            'zoom' => max(100, min(400, (int) ($photo->crop_zoom ?? 100))),
        ];

        $out = [];
        foreach (Photo::FRAMING_SLOTS as $slot) {
            $s = $cropData[$slot] ?? [];
            $out[$slot] = [
                'fx' => isset($s['fx']) ? max(0, min(100, (int) $s['fx'])) : $legacy['fx'],
                'fy' => isset($s['fy']) ? max(0, min(100, (int) $s['fy'])) : $legacy['fy'],
                'zoom' => isset($s['zoom']) ? max(100, min(400, (int) $s['zoom'])) : $legacy['zoom'],
            ];
        }

        return $out;
    }

    /**
     * Delete the specified media.
     */
    public function destroy(Photo $photo)
    {
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        } else {
            Log::warning("File not found on disk during deletion: {$photo->file_path}");
        }

        $photo->delete();

        return redirect()->route('photos.index')
            ->with('success', 'Foto berhasil dihapus.');
    }

    private function mediaPayload(Photo $photo): array
    {
        $routePrefix = $photo->isVideo() ? 'videos' : 'photos';

        return [
            'id' => $photo->id,
            'title' => $photo->title,
            'type' => $photo->type,
            'url' => asset('storage/'.$photo->file_path),
            'edit_url' => route($routePrefix.'.edit', $photo),
            'delete_url' => route($routePrefix.'.destroy', $photo),
            'is_active' => $photo->is_active,
        ];
    }
}
