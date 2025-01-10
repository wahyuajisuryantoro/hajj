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
}
