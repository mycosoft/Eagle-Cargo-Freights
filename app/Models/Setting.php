<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $type = 'text')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    /**
     * Get the currency symbol for the current system currency
     */
    public static function getCurrencySymbol($code = null)
    {
        $code = $code ?: self::get('system_currency', 'UGX');

        return match ($code) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'UGX' => 'UGX',
            'KES' => 'KSh',
            'TZS' => 'TSh',
            'RWF' => 'FRw',
            default => $code,
        };
    }
}
