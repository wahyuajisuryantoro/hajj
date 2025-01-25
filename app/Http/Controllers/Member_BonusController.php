<?php

namespace App\Http\Controllers;

use App\Models\Ujroh;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
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
        
        $totalDebit = Ujroh::where('code_mitra', $codeMitra)
            ->where('status', 'debit')
            ->sum('value');
            
        $totalCredit = Ujroh::where('code_mitra', $codeMitra)
            ->where('status', 'credit')
            ->sum('value');
            
        $saldoBonus = $totalDebit - $totalCredit;
     
        $mutasi = Ujroh::with('category')
            ->where('code_mitra', $codeMitra)
            ->select('code', 'code_category', 'value', 'status', 'desc', 'tanggal_transaksi')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'code' => $item->code,
                    'category_name' => $item->category->name ?? 'Unknown',
                    'value' => $item->value,
                    'status' => $item->status,
                    'desc' => $item->desc,
                    'tanggal_transaksi' => $item->tanggal_transaksi
                ];
            });
        
        return response()->json([
            'status' => true,
            'data' => [
                'total_bonus' => $totalDebit,
                'saldo_bonus' => $saldoBonus,
                'mutasi' => $mutasi
            ]
        ]);
     }
}
