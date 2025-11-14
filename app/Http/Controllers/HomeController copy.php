<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use App\Models\Service;
use App\Models\ServiceSparepart;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     public function index()
    {
        $today = Carbon::today();
        $month = Carbon::now()->month;

        // Card Summary
        $summary = [
            'services_today' => Service::whereDate('service_date', $today)->count(),
            'services_month' => Service::whereMonth('service_date', $month)->count(),
            'omzet_today'    => Service::whereDate('service_date', $today)->sum('total_cost'),
            'omzet_month'    => Service::whereMonth('service_date', $month)->sum('total_cost'),
            'spare_today'    => ServiceSparepart::whereHas('service', fn($q) => $q->whereDate('service_date', $today))->sum('qty'),
            'spare_month'    => ServiceSparepart::whereHas('service', fn($q) => $q->whereMonth('service_date', $month))->sum('qty'),
        ];

        // Grafik omzet 30 hari terakhir
        $omzetChart = Service::select(
                DB::raw('DATE(service_date) as date'),
                DB::raw('SUM(total_cost) as omzet')
            )
            ->where('service_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top Mekanik bulan ini
        $topMechanics = Mechanic::select('mechanics.name', DB::raw('COUNT(service_mechanics.service_id) as total'))
            ->join('service_mechanics', 'mechanics.id', '=', 'service_mechanics.mechanic_id')
            ->join('services', 'services.id', '=', 'service_mechanics.service_id')
            ->whereMonth('services.service_date', $month)
            ->groupBy('mechanics.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('home', compact('summary', 'omzetChart', 'topMechanics'));
    }
}
