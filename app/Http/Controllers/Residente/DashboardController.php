<?php

namespace App\Http\Controllers\Residente;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Entry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $apartment = $user->property_number;

        $stats = [
            'inside_now' => $apartment ? Entry::active()->where('apartment', $apartment)->count() : 0,
            'entries_today' => $apartment
                ? Entry::where('apartment', $apartment)->whereDate('entry_at', today())->count()
                : 0,
        ];

        $unread = Announcement::where(fn ($q) => $q->where('target', 'all')->orWhere('target', 'residente'))
            ->whereDoesntHave('readers', fn ($q) => $q->where('user_id', $user->id)->whereNotNull('read_at'))
            ->count();

        return Inertia::render('residente/Dashboard', compact('stats', 'unread', 'apartment'));
    }
}
