<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, $default = null)
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');
    }

    /**
     * Get all digital signage content settings.
     */
    public static function displayConfig(): array
    {
        return [
            'logo' => static::get('header_logo'),
            'logo_text' => static::get('header_logo_text', ''),

            'eyebrow' => static::get('display_eyebrow', 'Event Saat Ini'),
            'title' => static::get('display_title', 'Event Title'),

            'section_chip' => static::get('display_section_chip', 'Fasilitas'),
            'show_facility_captions' => filter_var(
                static::get('display_show_facility_captions', '1'),
                FILTER_VALIDATE_BOOLEAN
            ),

            'next_event_label' => static::get('next_event_label', 'Event Selanjutnya'),
            'next_event_title' => static::get('next_event_title', 'Event Berikutnya'),
            'next_event_organizer' => static::get('next_event_organizer', ''),
            'next_event_date' => static::get('next_event_date', ''),
            'next_event_time' => static::get('next_event_time', ''),
            'next_event_location' => static::get('next_event_location', ''),
            'next_event_category' => static::get('next_event_category', ''),

            'footer_title' => static::get('footer_title', ''),
            'footer_subtitle' => static::get('footer_subtitle', ''),
            'footer_support' => static::get('footer_support', ''),

            'clock_offset' => (int) static::get('clock_offset', 0),
        ];
    }
}
