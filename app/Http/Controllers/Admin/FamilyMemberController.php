<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FamilyMemberController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'cedula' => 'required|string|max:20|unique:family_members',
            'phone' => 'nullable|string|max:15',
            'relationship' => 'required|in:esposo,esposa,hijo,hija,padre,madre,hermano,hermana,otro',
        ]);

        FamilyMember::create($data);

        return back()->with('success', 'Familiar registrado correctamente.');
    }

    public function update(Request $request, FamilyMember $familyMember): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'cedula' => "required|string|max:20|unique:family_members,cedula,{$familyMember->id}",
            'phone' => 'nullable|string|max:15',
            'relationship' => 'required|in:esposo,esposa,hijo,hija,padre,madre,hermano,hermana,otro',
        ]);

        $familyMember->update($data);

        return back()->with('success', 'Familiar actualizado.');
    }

    public function destroy(FamilyMember $familyMember): RedirectResponse
    {
        $familyMember->delete();

        return back()->with('success', 'Familiar eliminado.');
    }
}
