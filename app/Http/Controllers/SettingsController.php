<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Get all settings grouped by group.
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return response()->json(['data' => $settings]);
    }

    /**
     * Get settings by group.
     */
    public function getByGroup($group)
    {
        $settings = Setting::where('group', $group)->get()->pluck('value', 'key');
        return response()->json(['data' => $settings]);
    }

    /**
     * Update or create multiple settings.
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings');
        $group = $request->input('group', 'general');

        foreach ($settings as $key => $value) {
            Setting::set($key, $value, $group);
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    /**
     * Get public stats.
     */
    public function getStats()
    {
        $keys = [
            'stats_projects_completed',
            'stats_happy_clients',
            'stats_team_members',
            'stats_years_experience'
        ];

        $stats = Setting::whereIn('key', $keys)->get()->pluck('value', 'key');
        
        // Provide defaults if not set
        $defaults = [
            'stats_projects_completed' => '50+',
            'stats_happy_clients' => '30+',
            'stats_team_members' => '20+',
            'stats_years_experience' => '5+'
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($stats[$key])) {
                $stats[$key] = $value;
            }
        }

        return response()->json(['data' => $stats]);
    }

    /**
     * Get settings by group (Public).
     */
    public function getByGroupPublic($group)
    {
        // Only allow certain groups to be public
        $publicGroups = ['footer', 'social', 'stats', 'appearance'];
        
        if (!in_array($group, $publicGroups)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $settings = Setting::where('group', $group)->get()->pluck('value', 'key');
        return response()->json(['data' => $settings]);
    }
}
