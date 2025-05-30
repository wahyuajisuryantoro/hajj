<?php

namespace App\Http\Controllers;

use App\Models\Ujroh;
use Illuminate\Http\Request;
use App\Models\UjrohCategory;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Member_BonusController extends Controller
{
    public function index()
    {
        $loggedInMitra = Auth::guard('mitra')->user();
        $loggedInCode = $loggedInMitra->code ?? null;

        // Hitung total rekap bonus (debit)
        $totalBonus = Ujroh::getTotalBonus($loggedInCode);

        // Hitung total komisi yang ditransfer (credit)
        $totalTransfer = Ujroh::getTotalTransfer($loggedInCode);

        // Hitung saldo
        $saldo = $totalBonus - $totalTransfer;

        // Untuk statistik perbandingan minggu lalu
        $comparison = Ujroh::getWeeklyComparison($loggedInCode);
        $bonusPercentage = $comparison['percentage'];

        // Data untuk tabel
        if (request()->ajax()) {
            $data = Ujroh::with('jamaah')
                ->select('ujrohs.*')
                ->where('code_mitra', $loggedInCode)
                ->orderBy('tanggal_transaksi', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal_transaksi', function ($row) {
                    return $row->formatted_tanggal;
                })
                ->addColumn('nama_jamaah', function ($row) {
                    return $row->jamaah->name ?? '-';
                })
                ->editColumn('desc', function ($row) {
                    return $row->desc ?? 'Tidak ada deskripsi untuk transaksi ini';
                })
                ->editColumn('value', function ($row) {
                    return $row->formatted_value;
                })
                ->editColumn('status', function ($row) {
                    $class = $row->status === 'debit' ? 'bg-label-success' : 'bg-label-warning';
                    return '<span class="badge rounded-pill ' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        $title = "Rekap Bonus";
        return view('pages.bonus.index', compact(
            'title',
            'totalBonus',
            'totalTransfer',
            'saldo',
            'bonusPercentage'
        ));
    }

    // JSON API
  public function getSaldoBonus(Request $request) {
        $codeMitra = $request->header('code_mitra');
        
        // 1. Total Bonus (jumlah dari debit)
        $totalBonus = Ujroh::where('code_mitra', $codeMitra)
            ->where('status', 'debit')
            ->sum('value');
            
        // 2. Bonus Diterima (jumlah dari kredit)
        $bonusDiterima = Ujroh::where('code_mitra', $codeMitra)
            ->where('status', 'credit')
            ->sum('value');
            
        // 3. Saldo (Total Bonus - Bonus Diterima)
        $saldo = $totalBonus - $bonusDiterima;
        
        // 4. TAMPILKAN SEMUA KATEGORI UJROH + JUMLAH YANG DIDAPAT USER
        $allCategories = UjrohCategory::all();
        
        $bonusPerKategori = $allCategories->map(function($category) use ($codeMitra, $totalBonus) {
            // Hitung berapa yang didapat user dari kategori ini
            $totalValue = Ujroh::where('code_mitra', $codeMitra)
                ->where('status', 'debit')
                ->where('code_category', $category->code)
                ->sum('value');
            
            $totalCount = Ujroh::where('code_mitra', $codeMitra)
                ->where('status', 'debit')
                ->where('code_category', $category->code)
                ->count();
            Log::info("Category {$category->code} ({$category->name}): Value={$totalValue}, Count={$totalCount}");
            return [
                'category_code' => $category->code,
                'category_name' => $category->name,
                'category_desc' => $category->desc,
                'total_value' => (int) $totalValue,
                'total_count' => (int) $totalCount,
                'formatted_value' => 'Rp ' . number_format($totalValue, 0, ',', '.'),
                'percentage' => $totalBonus > 0 ? round(($totalValue / $totalBonus) * 100, 1) : 0
            ];
        });
        
        // 5. Riwayat/mutasi dengan sumber customer dan valuenya
        $mutasi = Ujroh::leftJoin('ujroh_categories', 'ujrohs.code_category', '=', 'ujroh_categories.code')
            ->leftJoin('customers', 'ujrohs.code_customer', '=', 'customers.code')
            ->where('ujrohs.code_mitra', $codeMitra)
            ->select(
                'ujrohs.code',
                'ujrohs.code_category',
                'ujrohs.code_customer',
                'ujrohs.value',
                'ujrohs.status',
                'ujrohs.desc',
                'ujrohs.tanggal_transaksi',
                'ujrohs.sisa_saldo',
                'ujrohs.created_at',
                'ujroh_categories.name as category_name',
                'ujroh_categories.desc as category_desc',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
                'customers.email as customer_email'
            )
            ->orderBy('ujrohs.tanggal_transaksi', 'desc')
            ->orderBy('ujrohs.created_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'code' => $item->code,
                    'category_code' => $item->code_category,
                    'category_name' => $item->category_name ?? 'Kategori Tidak Diketahui',
                    'category_desc' => $item->category_desc ?? '',
                    'customer_code' => $item->code_customer,
                    'customer_name' => $item->customer_name ?? 'Customer Tidak Diketahui',
                    'customer_phone' => $item->customer_phone ?? '-',
                    'customer_email' => $item->customer_email ?? '-',
                    'value' => (int) $item->value,
                    'formatted_value' => 'Rp ' . number_format($item->value, 0, ',', '.'),
                    'status' => $item->status,
                    'status_label' => $item->status === 'debit' ? 'Bonus Masuk' : 'Bonus Keluar',
                    'desc' => $item->desc ?? 'Tidak ada deskripsi',
                    'tanggal_transaksi' => $item->tanggal_transaksi,
                    'formatted_date' => $item->tanggal_transaksi ? 
                        \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y') : '-',
                    'formatted_date_time' => $item->tanggal_transaksi ? 
                        \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y H:i') : '-',
                    'sisa_saldo' => (int) $item->sisa_saldo,
                    'formatted_sisa_saldo' => 'Rp ' . number_format($item->sisa_saldo ?? 0, 0, ',', '.'),
                    'created_at' => $item->created_at ? 
                        \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') : '-'
                ];
            });
        
        // Summary data
        $summary = [
            'total_transaksi' => $mutasi->count(),
            'total_debit' => $mutasi->where('status', 'debit')->count(),
            'total_credit' => $mutasi->where('status', 'credit')->count(),
            'total_kategori' => $allCategories->count(),
            'kategori_aktif' => $bonusPerKategori->where('total_count', '>', 0)->count(),
            'transaksi_terbaru' => $mutasi->first()
        ];
        
        return response()->json([
            'status' => true,
            'message' => 'Data saldo bonus berhasil diambil',
            'data' => [
                'total_bonus' => (int) $totalBonus,
                'bonus_diterima' => (int) $bonusDiterima,
                'saldo' => (int) $saldo,
                'formatted_total_bonus' => 'Rp ' . number_format($totalBonus, 0, ',', '.'),
                'formatted_bonus_diterima' => 'Rp ' . number_format($bonusDiterima, 0, ',', '.'),
                'formatted_saldo' => 'Rp ' . number_format($saldo, 0, ',', '.'),
                'bonus_per_kategori' => $bonusPerKategori,
                'mutasi' => $mutasi,
                'summary' => $summary
            ]
        ]);
    }



}
