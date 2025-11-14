<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KasirReportController extends Controller
{
    public function index(){
        return view('reports.kasir');
    }
    public function getData(Request $request)
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end   = $request->end_date ?? now()->endOfMonth()->toDateString();

        // Ambil user yang pernah input service atau sales
        $userIds = Service::pluck('created_by')
            ->merge(Sales::pluck('id_user'))
            ->unique();

        $users = User::whereIn('id', $userIds)->get();

        $data = [];

        foreach ($users as $user) {
            // Data service
            $services = Service::with('vehicle.client')
                ->where('created_by', $user->id)
                ->whereBetween(DB::raw('DATE(service_date)'), [$start, $end])
                ->get();

            // Data sales
            $sales = Sales::with('client')
                ->where('id_user', $user->id)
                ->whereBetween(DB::raw('DATE(sales_date)'), [$start, $end])
                ->get();

            $data[] = [
                'user_id' => $user->id,
                'nama_user' => $user->name,
                'total_service' => $services->count(),
                'total_sales' => $sales->count(),
                'total_omzet' => $services->sum('total_cost') + $sales->sum('total'),
                'transaksi_service' => $services->map(function ($srv) {
                    return [
                        'nomor' => $srv->nomor_service,
                        'tanggal' => $srv->service_date,
                        'client' => $srv->vehicle->client->nama_client ?? '-',
                        'total' => number_format($srv->total_cost, 0, ',', '.'),
                    ];
                }),
                'transaksi_sales' => $sales->map(function ($sl) {
                    return [
                        'nomor' => $sl->nomor_sales,
                        'tanggal' => $sl->sales_date,
                        'client' => $sl->client->nama_client ?? '-',
                        'total' => number_format($sl->total, 0, ',', '.'),
                    ];
                }),
            ];
        }

        return response()->json(['data' => $data]);
    }
}
