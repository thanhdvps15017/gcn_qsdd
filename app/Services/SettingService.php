<?php

namespace App\Services;
use App\Repositories\SettingRepository;

class SettingService
{
    protected $repo;
    public function __construct(SettingRepository $repo) {
        $this->repo = $repo;
    }
    public function getLoginBg() {
        return $this->repo->getValue('login_bg');
    }
    public function updateLoginBg($path) {
        return $this->repo->updateOrCreate('login_bg', $path);
    }
}
