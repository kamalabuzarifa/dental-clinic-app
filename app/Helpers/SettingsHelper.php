<?php

use App\Models\Setting;

if (!function_exists('get_setting')) {
    /**
     * Get a setting value by key with optional default.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('set_setting')) {
    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return \App\Models\Setting
     */
    function set_setting(string $key, $value)
    {
        return Setting::set($key, $value);
    }
}
