<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function indexPublic()
    {
        return response()->json(['data' => Slider::where('is_active', true)->orderBy('order_index')->get()]);
    }

    public function index()
    {
        return response()->json(['data' => Slider::orderBy('order_index')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'image' => 'required', // Allow string path
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string',
            'button_link' => 'nullable|string',
            'order_index' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sliders', 'public');
            $validated['image'] = 'storage/' . $path;
        } 
        // Else rely on string path in $validated['image']

        $validated['order_index'] = $request->input('order_index', $request->input('order'));
        $slider = Slider::create($validated);
        return response()->json(['data' => $slider], 201);
    }

    public function show(Slider $slider)
    {
        return response()->json(['data' => $slider]);
    }

    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'image' => 'nullable',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string',
            'button_link' => 'nullable|string',
            'order_index' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($slider->image) {
                $old = str_replace('storage/', '', $slider->image);
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('image')->store('sliders', 'public');
            $validated['image'] = 'storage/' . $path;
        } else {
            unset($validated['image']);
        }

        if ($request->has('order_index') || $request->has('order')) {
            $validated['order_index'] = $request->input('order_index', $request->input('order'));
        }
        $slider->update($validated);
        return response()->json(['data' => $slider]);
    }

    public function destroy(Slider $slider)
    {
        if ($slider->image) {
            $old = str_replace('storage/', '', $slider->image);
            Storage::disk('public')->delete($old);
        }
        $slider->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function toggleStatus(Slider $slider)
    {
        $slider->update(['is_active' => !$slider->is_active]);
        return response()->json(['data' => $slider]);
    }

    public function reorder(Request $request)
    {
        $sliders = $request->input('sliders', []);
        foreach ($sliders as $item) {
            Slider::where('id', $item['id'])->update(['order_index' => $item['order']]);
        }
        return response()->json(['message' => 'Reordered']);
    }
}
