<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function indexPublic()
    {
        return response()->json(['data' => TeamMember::where('is_active', true)->orderBy('order_index')->get()]);
    }

    public function index()
    {
        return response()->json(['data' => TeamMember::orderBy('order_index')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'image' => 'nullable',
            'bio' => 'nullable|string',
            'email' => 'nullable|email',
            'facebook' => 'nullable|string',
            'twitter' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'instagram' => 'nullable|string',
            'order_index' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('team', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $member = TeamMember::create($validated);
        return response()->json(['data' => $member], 201);
    }

    public function show(TeamMember $team)
    {
        return response()->json(['data' => $team]);
    }

    public function update(Request $request, TeamMember $team)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'image' => 'nullable',
            'bio' => 'nullable|string',
            'email' => 'nullable|email',
            'facebook' => 'nullable|string',
            'twitter' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'instagram' => 'nullable|string',
            'order_index' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($team->image) {
                $old = str_replace('storage/', '', $team->image);
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('image')->store('team', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $team->update($validated);
        return response()->json(['data' => $team]);
    }

    public function destroy(TeamMember $team)
    {
        if ($team->image) {
             $old = str_replace('storage/', '', $team->image);
             Storage::disk('public')->delete($old);
        }
        $team->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
