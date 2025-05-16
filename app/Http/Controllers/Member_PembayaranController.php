<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Customer;
use App\Helpers\UploadFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentConfirms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Member_PembayaranController extends Controller
{
    public function storePaymentConfirm(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'code_customer' => 'required|exists:customers,code',
                'code_program' => 'required|exists:programs,code',
                'value' => 'required|numeric|min:1',
                'status_payment' => 'required|in:dp,angsuran,pelunasan',
                'picture_scan' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'desc' => 'nullable|string',
                'code_jamaah' => 'nullable|string',
            ], [
                'code_customer.required' => 'Kode customer wajib diisi',
                'code_customer.exists' => 'Customer tidak ditemukan',
                'code_program.required' => 'Kode program wajib diisi',
                'code_program.exists' => 'Program tidak ditemukan',
                'value.required' => 'Nominal pembayaran wajib diisi',
                'value.numeric' => 'Nominal pembayaran harus berupa angka',
                'value.min' => 'Nominal pembayaran minimal 1',
                'status_payment.required' => 'Status pembayaran wajib dipilih',
                'status_payment.in' => 'Status pembayaran tidak valid',
                'picture_scan.required' => 'Bukti pembayaran wajib diupload',
                'picture_scan.image' => 'Bukti pembayaran harus berupa gambar',
                'picture_scan.mimes' => 'Format bukti pembayaran harus jpeg, png, atau jpg',
                'picture_scan.max' => 'Ukuran bukti pembayaran maksimal 2MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Generate unique code untuk payment confirm
            $lastCode = DB::table('payment_confirms')
                ->whereNotNull('code')
                ->orderBy('code', 'desc')
                ->lockForUpdate()
                ->value('code');

            $newCodeNumber = ($lastCode ? intval($lastCode) + 1 : 1);
            $newCode = str_pad($newCodeNumber, 10, '0', STR_PAD_LEFT);

            // Generate kode transaksi
            $codeTransaksi = 'KP-' . date('Ymd') . '-' . Str::random(5);

            // Upload bukti pembayaran
            $pictureScan = null;
            if ($request->hasFile('picture_scan')) {
                $pictureScan = UploadFile::file($request->file('picture_scan'), 'payment/confirm');
            }

            // Ambil data customer dan program
            $customer = Customer::where('code', $request->code_customer)->first();
            $program = Program::where('code', $request->code_program)->first();

            // Gunakan code_jamaah dari request jika ada, atau dari customer jika tidak ada
            $codeJamaah = $request->code_jamaah ?? $customer->code_jamaah ?? null;

            // Log untuk debugging
            Log::info('Menyimpan konfirmasi pembayaran dengan data:', [
                'code_customer' => $request->code_customer,
                'code_program' => $request->code_program,
                'code_jamaah' => $codeJamaah,
                'value' => $request->value,
                'status_payment' => $request->status_payment,
            ]);

            // Simpan data konfirmasi pembayaran
            $paymentConfirm = PaymentConfirms::create([
                'code' => $newCode,
                'code_customer' => $request->code_customer,
                'code_program' => $request->code_program,
                'code_jamaah' => $codeJamaah,
                'value' => $request->value,
                'status_payment' => $request->status_payment,
                'tanggal_transaksi' => date('Y-m-d'),
                'code_transaksi' => $codeTransaksi,
                'desc' => $request->desc,
                'picture_scan' => $pictureScan,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Konfirmasi pembayaran berhasil disimpan',
                'data' => $paymentConfirm
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan konfirmasi pembayaran: ' . $e->getMessage());

            if (isset($pictureScan)) {
                UploadFile::delete('payment/confirm', $pictureScan);
            }

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan konfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Mendapatkan data konfirmasi pembayaran berdasarkan kode customer
     */
    public function getPaymentConfirmsByCustomer($code)
    {
        try {
            $customer = Customer::where('code', $code)->firstOrFail();
            $paymentConfirms = PaymentConfirms::where('code_customer', $code)
                ->orderBy('tanggal_transaksi', 'desc')
                ->get();

            $program = null;
            if ($customer->code_program) {
                $program = Program::where('code', $customer->code_program)->first();
            }

            return response()->json([
                'status' => true,
                'message' => 'Data konfirmasi pembayaran berhasil diambil',
                'data' => [
                    'customer' => [
                        'code' => $customer->code,
                        'name' => $customer->name,
                    ],
                    'program' => $program ? [
                        'code' => $program->code,
                        'name' => $program->name,
                        'price' => $program->price,
                    ] : null,
                    'payment_confirms' => $paymentConfirms
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saat mengambil data konfirmasi pembayaran: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data konfirmasi pembayaran tidak ditemukan: ' . $e->getMessage(),
                'data' => null
            ], 404);
        }
    }

    /**
     * Mendapatkan detail konfirmasi pembayaran
     */
    public function getPaymentConfirmDetail($code)
    {
        try {
            $paymentConfirm = PaymentConfirms::where('code', $code)->firstOrFail();
            $customer = Customer::where('code', $paymentConfirm->code_customer)->first();
            $program = Program::where('code', $paymentConfirm->code_program)->first();

            return response()->json([
                'status' => true,
                'message' => 'Detail konfirmasi pembayaran berhasil diambil',
                'data' => [
                    'payment_confirm' => $paymentConfirm,
                    'customer' => $customer ? [
                        'code' => $customer->code,
                        'name' => $customer->name,
                    ] : null,
                    'program' => $program ? [
                        'code' => $program->code,
                        'name' => $program->name,
                        'price' => $program->price,
                    ] : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saat mengambil detail konfirmasi pembayaran: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Detail konfirmasi pembayaran tidak ditemukan: ' . $e->getMessage(),
                'data' => null
            ], 404);
        }
    }
}
