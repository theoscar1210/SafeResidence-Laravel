<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Comunicados dirigidos a este usuario (todos, su rol, o específicamente)
        $role = strtolower($user->getRoleNames()->first() ?? '');

        $announcements = Announcement::whereIn('target', ['all', $role])
            ->orWhereHas('readers', fn ($q) => $q->where('user_id', $user->id))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($a) => [
                'id'         => $a->id,
                'title'      => $a->title,
                'body'       => $a->body,
                'created_at' => $a->created_at->format('d/m/Y H:i'),
                'read_at'    => $a->readers()->where('user_id', $user->id)->first()?->pivot?->read_at,
            ]);

        return Inertia::render('Announcements/Index', compact('announcements'));
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $a = Announcement::findOrFail($id);
        $a->readers()->syncWithoutDetaching([
            $user->id => ['read_at' => now()],
        ]);

        return response()->json(['ok' => true]);
    }
}
