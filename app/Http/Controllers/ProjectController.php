<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function indexPublic()
    {
        return response()->json(['data' => Project::where('is_active', true)->get()]);
    }

    public function index()
    {
        return response()->json(['data' => Project::all()]);
    }

    public function showSlug($slug)
    {
        return response()->json(['data' => Project::where('slug', $slug)->where('is_active', true)->firstOrFail()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'category' => 'nullable|string',
            'image' => 'required', // Allow string path or file
            'description' => 'required|string',
            'technologies' => 'nullable|array',
            'features' => 'nullable|array',
            'github_url' => 'nullable|string',
            'live_url' => 'nullable|string',
            'client' => 'nullable|string',
            'completion_date' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $count = Project::where('slug', $validated['slug'])->count();
        if ($count > 0) {
             $validated['slug'] .= '-' . ($count + 1);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $validated['image'] = 'storage/' . $path;
        }
        // If no file, $validated['image'] already contains the string path from request

        $project = Project::create($validated);
        return response()->json(['data' => $project], 201);
    }

    public function show(Project $project)
    {
        return response()->json(['data' => $project]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'category' => 'nullable|string',
            'image' => 'nullable', // Allow string or file
            'description' => 'required|string',
            'technologies' => 'nullable|array',
            'features' => 'nullable|array',
            'github_url' => 'nullable|string',
            'live_url' => 'nullable|string',
            'client' => 'nullable|string',
            'completion_date' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        if ($request->title !== $project->title) {
             $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('image')) {
            if ($project->image) {
                // $project->image might be full URL now due to mutator? No, DB stores relative.
                $old = str_replace('storage/', '', $project->image);
                // Check if old file exists before delete? 
                if (Storage::disk('public')->exists($old)) {
                     Storage::disk('public')->delete($old);
                }
            }
            $path = $request->file('image')->store('projects', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $project->update($validated);
        return response()->json(['data' => $project]);
    }

    public function destroy(Project $project)
    {
        if ($project->image) {
             $old = str_replace('storage/', '', $project->image);
             Storage::disk('public')->delete($old);
        }
        $project->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
