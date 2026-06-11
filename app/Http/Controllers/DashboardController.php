<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardService $service) {
        $this->service = $service;
    }

    public function index() {
        $data = $this->service->getDashboardData();
        return view('dashboard', $data);
    }
}
