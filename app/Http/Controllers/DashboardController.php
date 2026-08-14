<?php

namespace App\Http\Controllers;

use App\Models\DisplaySlotGroup;
use App\Models\Photo;
use App\Models\PhotoGroup;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPhotos = Photo::where('type', 'photo')->count();
        $totalVideos = Photo::where('type', 'video')->count();
        $totalGroups = PhotoGroup::count();
        $activeGroups = PhotoGroup::where('is_active', true)->count();

        $recentPhotos = Photo::where('type', 'photo')->latest()->limit(6)->get();
        $recentGroups = PhotoGroup::withCount('items')->latest()->limit(4)->get();

        $setup = [
            'media' => ($totalPhotos + $totalVideos) > 0,
            'groups' => $activeGroups > 0,
            'mainSlot' => DisplaySlotGroup::orderedFor(DisplaySlotGroup::SLOT_MAIN)->isNotEmpty(),
        ];

        $today = Carbon::now()->locale('id')->translatedFormat('l, d F Y');

        return view('admin.dashboard', compact(
            'totalPhotos',
            'totalVideos',
            'totalGroups',
            'activeGroups',
            'recentPhotos',
            'recentGroups',
            'setup',
            'today'
        ));
    }
}
