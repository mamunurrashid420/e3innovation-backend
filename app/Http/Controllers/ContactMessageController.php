<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Service;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Slider;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        return response()->json(['data' => ContactMessage::orderBy('created_at', 'desc')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'nullable|string',
            'message' => 'required|string',
        ]);

        $contact = ContactMessage::create($validated);
        return response()->json(['data' => $contact, 'message' => 'Message sent successfully']);
    }

    public function markRead($id)
    {
        $contact = ContactMessage::findOrFail($id);
        $contact->update(['is_read' => true]);
        return response()->json(['data' => $contact]);
    }

    public function destroy($id)
    {
        ContactMessage::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function stats()
    {
        return response()->json(['data' => [
            'contacts' => ContactMessage::count(),
            'services' => Service::count(),
            'projects' => Project::count(),
            'team' => TeamMember::count(),
            'sliders' => Slider::count(),
        ]]);
    }
}
