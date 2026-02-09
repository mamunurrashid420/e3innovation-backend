<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Appearance
            ['key' => 'bg_services_hero', 'value' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=80', 'group' => 'appearance'],
            ['key' => 'bg_about_hero', 'value' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=80', 'group' => 'appearance'],
            ['key' => 'bg_projects_hero', 'value' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1920&q=80', 'group' => 'appearance'],
            ['key' => 'bg_team_hero', 'value' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1920&q=80', 'group' => 'appearance'],
            ['key' => 'bg_services_section', 'value' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80', 'group' => 'appearance'],
            ['key' => 'bg_about_stats_section', 'value' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1920&q=80', 'group' => 'appearance'],
            ['key' => 'bg_contact_hero', 'value' => 'https://images.unsplash.com/photo-1557426272-fc759fdf7a8d?w=1920&q=80', 'group' => 'appearance'],
            ['key' => 'site_logo', 'value' => null, 'group' => 'appearance'],
            ['key' => 'site_favicon', 'value' => null, 'group' => 'appearance'],

            // Footer
            ['key' => 'footer_about_text', 'value' => 'Empowering businesses with innovative technology solutions. We specialize in software development, cloud services, and digital transformation.', 'group' => 'footer'],
            ['key' => 'footer_address_bd', 'value' => 'Dhaka, Bangladesh', 'group' => 'footer'],
            ['key' => 'footer_address_china', 'value' => 'Guangzhou, China', 'group' => 'footer'],
            ['key' => 'footer_phone', 'value' => '+880 1234 567890', 'group' => 'footer'],
            ['key' => 'footer_email', 'value' => 'info@e3bd.com', 'group' => 'footer'],

            // Social
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/e3innovation', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/e3innovation', 'group' => 'social'],
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/e3innovation', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/e3innovation', 'group' => 'social'],

            // Stats
            ['key' => 'stats_projects_completed', 'value' => '50+', 'group' => 'stats'],
            ['key' => 'stats_happy_clients', 'value' => '30+', 'group' => 'stats'],
            ['key' => 'stats_team_members', 'value' => '20+', 'group' => 'stats'],
            ['key' => 'stats_years_experience', 'value' => '5+', 'group' => 'stats'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
