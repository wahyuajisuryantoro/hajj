<?php

namespace App\Http\Controllers;

use App\Models\Jamaah;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class Member_JamaahController extends Controller
{

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $loggedInMitra = Auth::guard('mitra')->user();
            $loggedInCode = $loggedInMitra->code ?? null;

            $data = Jamaah::query()
                ->select([
                    'id',
                    'code',
                    'name',
                    'phone',
                    'email',
                    'job',
                    'status_payment',
                    'status_berangkat',
                    'status',
                    'total_payment',
                    'picture_profile'
                ])
                ->where('code_mitra', $loggedInCode)
                ->orderBy('name', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('email', function ($row) {
                    return $row->email ?? '-';
                })
                ->editColumn('phone', function ($row) {
                    return $row->phone ?? '-';
                })
                ->editColumn('job', function ($row) {
                    return $row->job ?? '-';
                })
                ->editColumn('status_payment', function ($row) {
                    $classes = [
                        'dp' => 'bg-label-warning',
                        'angsuran' => 'bg-label-info',
                        'lunas' => 'bg-label-success'
                    ];
                    $class = $classes[$row->status_payment] ?? 'bg-label-secondary';

                    return '<span class="badge rounded-pill ' . $class . '">' . ucfirst($row->status_payment ?? '-') . '</span>';
                })
                ->editColumn('status_berangkat', function ($row) {
                    $classes = [
                        'belum' => 'bg-label-danger',
                        'sedang' => 'bg-label-warning',
                        'sudah' => 'bg-label-success'
                    ];
                    $class = $classes[$row->status_berangkat] ?? 'bg-label-secondary';

                    return '<span class="badge rounded-pill ' . $class . '">' . ucfirst($row->status_berangkat ?? '-') . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $class = $row->status === 'active' ? 'bg-label-success' : 'bg-label-danger';
                    $title = $row->status === 'active' ? 'Active' : 'Nonactive';

                    return '<span class="badge rounded-pill ' . $class . '">' . $title . '</span>';
                })
                ->editColumn('total_payment', function ($row) {
                    return 'Rp ' . number_format($row->total_payment, 0, ',', '.');
                })
                ->addColumn('full_name', function ($row) {
                    $avatar = $row->picture_profile ?
                        '<img src="' . $row->picture_profile . '" alt="Avatar" class="rounded-circle" width="32">' :
                        '<span class="avatar-initial rounded-circle bg-label-primary">' . strtoupper(substr($row->name ?? 'U', 0, 2)) . '</span>';

                    return ' <a href="' . route('jamaah.show', $row->id) . '">
                                <div class="d-flex justify-content-start align-items-center">
                               
                                    <div class="avatar me-2">' . $avatar . '</div>
                               
                                <div class="d-flex flex-column">
                                    <span class="text-truncate">' . ($row->name ?? 'Unnamed') . '</span>
                                    <small class="text-muted">' . ($row->code ?? '-') . '</small>
                                </div>
                            </div>
                            </a>';
                })
                ->rawColumns(['full_name', 'status_payment', 'status_berangkat', 'status'])
                ->make(true);
        }

        $title = "List Jamaah Anda";
        return view('pages.jamaah.list', compact('title'));
    }

    public function show($id)
    {
        $title = "Detail Jamaah";
        $jamaah = Jamaah::findOrFail($id);
        return view('pages.jamaah.detail-jamaah', compact('title', 'jamaah'));
    }


    // JSON API
    public function getAllDataJamaah(Request $request)
    {
        $codeMitra = $request->header('code_mitra');

        $jamaahs = Jamaah::with([
            'city:id,name',
            'province:id,name',
            'program:code,name',
            'cabang:code,name',
            'customer:code,name'
        ])
            ->where('code_mitra', $codeMitra)
            ->select([
                'id',
                'code',
                'name',
                'phone',
                'email',
                'status',
                'picture_profile',
                'picture_ktp',
                'code_city',
                'code_province',
                'code_program',
                'code_cabang',
                'code_customer',
                'status_payment',
                'status_berangkat',
                'date_program',
                'value',
                'total_payment',
                'job',
                'desc'
            ])
            ->get();

        if ($jamaahs->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data jamaah tidak ditemukan',
                'data' => null
            ], 404);
        }

        $transformedData = $jamaahs->map(function ($item) {
            return [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'phone' => $item->phone,
                'email' => $item->email,
                'status' => $item->status,
                'picture_profile' => $item->picture_profile,
                'picture_ktp' => $item->picture_ktp,
                'status_payment' => $item->status_payment,
                'status_berangkat' => $item->status_berangkat,
                'date_program' => $item->date_program,
                'value' => $item->value,
                'total_payment' => $item->total_payment,
                'job' => $item->job,
                'desc' => $item->desc,
                'city' => $item->city ? ['id' => $item->city->id, 'name' => $item->city->name] : null,
                'province' => $item->province ? ['id' => $item->province->id, 'name' => $item->province->name] : null,
                'program' => $item->program ? ['code' => $item->program->code, 'name' => $item->program->name] : null,
                'cabang' => $item->cabang ? ['code' => $item->cabang->code, 'name' => $item->cabang->name] : null,
                'customer' => $item->customer ? ['code' => $item->customer->code, 'name' => $item->customer->name] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data jamaah berhasil diambil',
            'data' => $transformedData
        ], 200);
    }

    public function getDetailJamaah($id)
    {
        try {
            $jamaah = Jamaah::with([
                'city:id,name',
                'province:id,name',
                'program:id,code,name,price,duration,tanggal_berangkat,desc',
                'cabang:code,name',
                'customer:code,name',
                'mitra:code,name'
            ])
                ->findOrFail($id);


            $transformedData = [
                'id' => $jamaah->id,
                'code' => $jamaah->code,
                'name' => $jamaah->name,
                'phone' => $jamaah->phone,
                'email' => $jamaah->email,
                'job' => $jamaah->job,
                'city_program' => $jamaah->city_program,
                'desc' => $jamaah->desc,
                'date_program' => $jamaah->date_program,
                'formatted_date_program' => $jamaah->date_program ? $jamaah->date_program->format('d-m-Y') : '-',
                'value' => $jamaah->value,
                'total_payment' => $jamaah->total_payment,
                'status' => $jamaah->status,
                'status_payment' => $jamaah->status_payment,
                'status_berangkat' => $jamaah->status_berangkat,
                'picture_profile' => $jamaah->picture_profile,
                'picture_ktp' => $jamaah->picture_ktp,
                'tahun_jamaah' => $jamaah->tahun_jamaah,
                'code_customer' => $jamaah->code_customer,
                'code_program' => $jamaah->code_program,
                'city' => $jamaah->city ? ['id' => $jamaah->city->id, 'name' => $jamaah->city->name] : null,
                'province' => $jamaah->province ? ['id' => $jamaah->province->id, 'name' => $jamaah->province->name] : null,
                'program' => $jamaah->program ? [
                    'code' => $jamaah->program->code,
                    'name' => $jamaah->program->name,
                    'price' => $jamaah->program->price,
                    'formatted_price' => $jamaah->program->price ? 'Rp ' . number_format($jamaah->program->price, 0, ',', '.') : '-',
                    'duration' => $jamaah->program->duration,
                    'tanggal_berangkat' => $jamaah->program->tanggal_berangkat,
                    'formatted_tanggal_berangkat' => $jamaah->program->tanggal_berangkat ? $jamaah->program->tanggal_berangkat->format('d-m-Y') : '-',
                    'desc' => $jamaah->program->desc
                ] : null,
                'cabang' => $jamaah->cabang ? ['code' => $jamaah->cabang->code, 'name' => $jamaah->cabang->name] : null,
                'customer' => $jamaah->customer ? ['code' => $jamaah->customer->code, 'name' => $jamaah->customer->name] : null,
                'mitra' => $jamaah->mitra ? ['code' => $jamaah->mitra->code, 'name' => $jamaah->mitra->name] : null,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Detail jamaah berhasil diambil',
                'data' => $transformedData
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Data jamaah tidak ditemukan: ' . $e->getMessage(),
                'data' => null
            ], 404);
        }
    }
}
