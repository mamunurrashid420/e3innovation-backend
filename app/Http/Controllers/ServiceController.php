<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function indexPublic()
    {
        $services = Service::where('is_active', true)->get()->map(function($service) {
            $data = $service->toArray();
            if (!empty($data['image']) && !str_starts_with($data['image'], 'http')) {
                $imagePath = str_starts_with($data['image'], 'storage/')
                    ? $data['image']
                    : 'storage/' . $data['image'];
                $data['image'] = asset($imagePath);
            }
            return $data;
        });
        return response()->json(['data' => $services]);
    }

    public function index()
    {
        $services = Service::all()->map(function($service) {
            $data = $service->toArray();
            if (!empty($data['image']) && !str_starts_with($data['image'], 'http')) {
                $imagePath = str_starts_with($data['image'], 'storage/')
                    ? $data['image']
                    : 'storage/' . $data['image'];
                $data['image'] = asset($imagePath);
            }
            return $data;
        });
        return response()->json(['data' => $services]);
    }

    public function showSlug($slug)
    {
        return response()->json(['data' => Service::where('slug', $slug)->where('is_active', true)->firstOrFail()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'icon' => 'nullable|string',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $count = Service::where('slug', $validated['slug'])->count();
        if ($count > 0) {
             $validated['slug'] .= '-' . ($count + 1);
        }

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('services/icons', 'public');
            $validated['icon'] = 'storage/' . $path;
        }

        $service = Service::create($validated);
        return response()->json(['data' => $service], 201);
    }

    public function show(Service $service)
    {
        return response()->json(['data' => $service]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'icon' => 'nullable|string',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($request->title !== $service->title) {
             $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('icon')) {
            if ($service->icon) {
                $oldPath = str_replace('storage/', '', $service->icon);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('icon')->store('services/icons', 'public');
            $validated['icon'] = 'storage/' . $path;
        }

        $service->update($validated);
        return response()->json(['data' => $service]);
    }

    public function destroy(Service $service)
    {
        if ($service->icon) {
            $oldPath = str_replace('storage/', '', $service->icon);
            Storage::disk('public')->delete($oldPath);
        }
        $service->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
