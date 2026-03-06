<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /** Build full image URL using request host (works with https://api.e3bd.com). */
    private function withFullUrls(array $data): array
    {
        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        if (!empty($data['image']) && !str_starts_with($data['image'], 'http')) {
            $path = str_starts_with($data['image'], 'storage/') ? $data['image'] : 'storage/' . $data['image'];
            $data['image'] = $baseUrl . '/' . ltrim($path, '/');
        }
        if (!empty($data['images']) && is_array($data['images'])) {
            $data['images'] = array_map(function ($img) use ($baseUrl) {
                if (is_string($img) && !str_starts_with($img, 'http')) {
                    $path = str_starts_with($img, 'storage/') ? $img : 'storage/' . $img;
                    return $baseUrl . '/' . ltrim($path, '/');
                }
                return $img;
            }, $data['images']);
        }
        return $data;
    }

    public function indexPublic()
    {
        $projects = Project::where('is_active', true)->get()->map(fn($project) => $this->withFullUrls($project->toArray()));
        return response()->json(['data' => $projects]);
    }

    public function index()
    {
        $projects = Project::all()->map(fn($project) => $this->withFullUrls($project->toArray()));
        return response()->json(['data' => $projects]);
    }

    public function showSlug($slug)
    {
        $project = Project::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return response()->json(['data' => $this->withFullUrls($project->toArray())]);
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
        return response()->json(['data' => $this->withFullUrls($project->toArray())]);
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
