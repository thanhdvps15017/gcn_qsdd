<?php

namespace App\Repositories;
use App\Models\Setting;

class SettingRepository
{
    public function getValue($key) {
        return Setting::getValue($key);
    }
    public function updateOrCreate($key, $value) {
        return Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
