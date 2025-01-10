<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Mitra;
use App\Models\Ujroh;
use App\Models\Jamaah;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Member_DashboardController extends Controller
{
    public function index()
    {
        $loggedInMitra = Auth::guard('mitra')->user();
        $mitraCode = $loggedInMitra->code;
        $currentMonth = Carbon::now();
        $title = "Dashboard Member";
        // Jumlah Mitra yang terkait dengan mitra yang sedang login (sesuai dengan kode mitra)
        $totalMitra = Mitra::where('code_mitra', $mitraCode)->count();

        // Jumlah Customer yang terkait dengan mitra yang sedang login
        $totalCustomer = Customer::where('code_mitra', $mitraCode)->count();
        // Jumlah Mitra 4 bulan lalu (sesuai dengan kode mitra)
        $mitraLast4Months = Mitra::where('code_mitra', $mitraCode)
            ->where('created_at', '>=', Carbon::now()->subMonths(4))
            ->count();

        // Perubahan persentase Mitra
        $mitraPercentage = $mitraLast4Months > 0 ? round((($totalMitra - $mitraLast4Months) / $mitraLast4Months) * 100, 1) : 0;

        // Jumlah Customer 4 bulan lalu (sesuai dengan kode mitra)
        $customerLast4Months = Customer::where('code_mitra', $mitraCode)
            ->where('created_at', '>=', Carbon::now()->subMonths(4))
            ->count();

        // Perubahan persentase Customer
        $customerPercentage = $customerLast4Months > 0 ? round((($totalCustomer - $customerLast4Months) / $customerLast4Months) * 100, 1) : 0;
        // Statistik Jamaah
        $totalJamaah = Jamaah::where('code_mitra', $mitraCode)
            ->where('status', 'active')
            ->count();

        // Menghitung jumlah jamaah berdasarkan status keberangkatan
        $statusBelum = Jamaah::where('code_mitra', $mitraCode)
            ->where('status_berangkat', 'belum')
            ->count();

        $statusSedang = Jamaah::where('code_mitra', $mitraCode)
            ->where('status_berangkat', 'sedang')
            ->count();

        $statusSudah = Jamaah::where('code_mitra', $mitraCode)
            ->where('status_berangkat', 'sudah')
            ->count();

        $jamaahLastWeek = Jamaah::where('code_mitra', $mitraCode)
            ->where('created_at', '>=', $currentMonth->copy()->subWeek())
            ->count();

        $jamaahPrevWeek = Jamaah::where('code_mitra', $mitraCode)
            ->whereBetween('created_at', [
                $currentMonth->copy()->subWeeks(2),
                $currentMonth->copy()->subWeek()
            ])->count();

        $jamaahPercentage = $jamaahPrevWeek > 0
            ? round((($jamaahLastWeek - $jamaahPrevWeek) / $jamaahPrevWeek) * 100, 1)
            : 0;
        // Hitung total rekap bonus (debit)
        $totalBonus = Ujroh::getTotalBonus($mitraCode);

        // Hitung total komisi yang ditransfer (credit)
        $totalTransfer = Ujroh::getTotalTransfer($mitraCode);

        // Total Ujroh/Komisi
        $totalUjroh = Ujroh::where('code_mitra', $mitraCode)
            ->where('status', 'debit')
            ->sum('value');

        $ujrohLastWeek = Ujroh::where('code_mitra', $mitraCode)
            ->where('status', 'debit')
            ->where('tanggal_transaksi', '>=', $currentMonth->copy()->subWeek())
            ->sum('value');

        $ujrohPrevWeek = Ujroh::where('code_mitra', $mitraCode)
            ->where('status', 'debit')
            ->whereBetween('tanggal_transaksi', [
                $currentMonth->copy()->subWeeks(2),
                $currentMonth->copy()->subWeek()
            ])->sum('value');

        $ujrohPercentage = $ujrohPrevWeek > 0
            ? round((($ujrohLastWeek - $ujrohPrevWeek) / $ujrohPrevWeek) * 100, 1)
            : 0;

        // Status Pembayaran Overview
        $statusPembayaran = [
            'dp' => [
                'count' => Jamaah::where('code_mitra', $mitraCode)
                    ->where('status_payment', 'dp')
                    ->where('status', 'active')
                    ->count(),
                'label' => 'Down Payment',
                'icon' => 'ri-money-dollar-circle-line',
                'color' => 'primary'
            ],
            'angsuran' => [
                'count' => Jamaah::where('code_mitra', $mitraCode)
                    ->where('status_payment', 'angsuran')
                    ->where('status', 'active')
                    ->count(),
                'label' => 'Angsuran',
                'icon' => 'ri-exchange-dollar-line',
                'color' => 'warning'
            ],
            'lunas' => [
                'count' => Jamaah::where('code_mitra', $mitraCode)
                    ->where('status_payment', 'lunas')
                    ->where('status', 'active')
                    ->count(),
                'label' => 'Lunas',
                'icon' => 'ri-checkbox-circle-line',
                'color' => 'success'
            ]
        ];

        $totalAllJamaah = collect($statusPembayaran)->sum('count');
        foreach ($statusPembayaran as $key => $value) {
            $statusPembayaran[$key]['percentage'] = $totalAllJamaah > 0
                ? round(($value['count'] / $totalAllJamaah) * 100, 1)
                : 0;
        }

        // Program Overview - Upcoming Programs
        $upcomingPrograms = Program::with([
            'jamaahs' => function ($query) use ($mitraCode) {
                $query->where('code_mitra', $mitraCode);
            }
        ])
            ->where('status', 'active')
            ->where('tanggal_berangkat', '>', Carbon::now())
            ->orderBy('tanggal_berangkat')
            ->take(5)
            ->get()
            ->map(function ($program) {
                return [
                    'name' => $program->name,
                    'date' => $program->formatted_tanggal_berangkat,
                    'jamaah_count' => $program->jamaahs->count(),
                    'occupancy_rate' => round(($program->jamaahs->count() / $program->kuota) * 100, 1)
                ];
            });

        $totalProgram = Program::active()->count();

        $bonusChart = Ujroh::where('code_mitra', $mitraCode)
            ->where('status', 'debit')
            ->whereYear('tanggal_transaksi', date('Y'))
            ->select(
                DB::raw('MONTH(tanggal_transaksi) as month'),
                DB::raw('SUM(value) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return (int)$item->total;
            })
            ->toArray();

        // Isi dengan 0 untuk bulan yang tidak ada datanya
        $completeMonths = array_fill(0, 12, 0);
        foreach ($bonusChart as $index => $value) {
            $completeMonths[$index] = $value;
        }

        return view('dashboard', compact(
            'title',
            'loggedInMitra',
            'totalJamaah',
            'jamaahPercentage',
            'totalUjroh',
            'ujrohPercentage',
            'statusPembayaran',
            'upcomingPrograms',
            'totalMitra',
            'mitraPercentage',
            'totalCustomer',
            'customerPercentage',
            'totalProgram',
            'statusBelum',
            'statusSedang',
            'statusSudah',
            'totalBonus',
            'totalTransfer',
            'completeMonths'
        ));
    }
}
