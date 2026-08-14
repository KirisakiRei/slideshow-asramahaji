<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Photo::where('type', 'video');

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->input('search') . '%');
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $videos = $query->latest()->paginate(12)->withQueryString();

        return view('admin.videos.index', compact('videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:mp4,mkv,mov', 'max:204800'],
            'title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'file.required' => 'File video wajib diunggah.',
            'file.mimes' => 'File harus berformat: MP4, MKV, atau MOV.',
            'file.max' => 'Ukuran file tidak boleh melebihi 200MB.',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $uniqueFilename = uniqid() . '_' . $originalName;

        try {
            $path = $file->storeAs('videos', $uniqueFilename, 'public');
            if ($path === false) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Gagal mengunggah video.'], 500);
                }
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah video.');
            }
        } catch (\Exception $e) {
            Log::error('Video upload failed: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Gagal mengunggah video.'], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengunggah video.');
        }

        $title = $request->input('title');
        if (empty($title)) {
            $title = pathinfo($originalName, PATHINFO_FILENAME);
            $title = mb_substr($title, 0, 255);
        }

        $video = Photo::create([
            'title' => $title,
            'file_path' => 'videos/' . $uniqueFilename,
            'type' => 'video',
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Video berhasil diunggah.',
                'media' => $this->mediaPayload($video),
            ]);
        }

        return redirect()->route('videos.index')->with('success', 'Video berhasil diunggah.');
    }

    public function edit(Photo $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Photo $video)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255', 'regex:/\S/'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $video->update([
            'title' => $request->input('title'),
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : false,
        ]);

        return redirect()->route('videos.index')->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy(Photo $video)
    {
        if (Storage::disk('public')->exists($video->file_path)) {
            Storage::disk('public')->delete($video->file_path);
        } else {
            Log::warning("Video file not found: {$video->file_path}");
        }

        $video->delete();

        return redirect()->route('videos.index')->with('success', 'Video berhasil dihapus.');
    }

    private function mediaPayload(Photo $video): array
    {
        return [
            'id' => $video->id,
            'title' => $video->title,
            'type' => $video->type,
            'url' => asset('storage/' . $video->file_path),
            'edit_url' => route('videos.edit', $video),
            'delete_url' => route('videos.destroy', $video),
            'is_active' => $video->is_active,
        ];
    }
}
