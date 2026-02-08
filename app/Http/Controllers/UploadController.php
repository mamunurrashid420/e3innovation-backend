<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'File not found'], 400);
        }

        $folder = $request->input('folder', 'uploads');
        $path = $request->file('file')->store($folder, 'public');

        return response()->json([
            'data' => [
                'url' => asset('storage/' . $path),
                'path' => 'storage/' . $path
            ]
        ]);
    }
}
