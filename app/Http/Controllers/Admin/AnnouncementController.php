<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    public function index(): Response
    {
        $announcements = Announcement::with('author')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($a) => [
                'id'         => $a->id,
                'title'      => $a->title,
                'body'       => $a->body,
                'target'     => $a->target,
                'send_push'  => $a->send_push,
                'author'     => $a->author?->full_name,
                'created_at' => $a->created_at->format('d/m/Y H:i'),
                'reads'      => $a->readers()->count(),
            ]);

        return Inertia::render('admin/Announcements/Index', compact('announcements'));
    }

    public function create(): Response
    {
        return Inertia::render('admin/Announcements/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'target'    => 'required|in:all,propietario,residente',
            'send_push' => 'boolean',
        ]);

        $announcement = Announcement::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        // Enviar notificación a los usuarios objetivo
        $query = User::whereHas('roles', fn ($q) => match ($data['target']) {
            'propietario' => $q->where('name', 'Propietario'),
            'residente'   => $q->where('name', 'Residente'),
            default       => $q->whereIn('name', ['Propietario', 'Residente']),
        });

        Notification::send($query->get(), new AnnouncementNotification($announcement));

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Comunicado enviado correctamente.');
    }
}
