<?php

namespace App\Helpers;

class ContentFormatter
{
    public static function parseContent($content)
    {
        $data = [
            'tanggal' => null,
            'pesawat' => null,
            'hotel_mekkah' => null,
            'hotel_madinah' => null,
            'fasilitas' => [],
            'non_fasilitas' => [],
            'pembayaran' => []
        ];

        // Ekstrak tanggal keberangkatan
        if (preg_match('/Keberangkatan\s+(\d+\s+[A-Za-z]+\s+\d{4})/', $content, $matches)) {
            $data['tanggal'] = $matches[1];
        }

        // Ekstrak pesawat
        if (preg_match('/Pesawat\s+([^H]+)Hotel/', $content, $matches)) {
            $data['pesawat'] = trim($matches[1]);
        }

        // Ekstrak hotel
        if (preg_match('/Hotel Mekah[^:]*:\s*([^H]+)Hotel/i', $content, $matches)) {
            $data['hotel_mekkah'] = trim($matches[1]);
        }
        if (preg_match('/Hotel Madinah[^:]*:\s*([^\n]+)/i', $content, $matches)) {
            $data['hotel_madinah'] = trim($matches[1]);
        }

        // Ekstrak fasilitas
        if (preg_match('/Fasilitas\s*:[^\n]*(.+?)Harga tidak termasuk/s', $content, $matches)) {
            $fasilitasText = $matches[1];
            $fasilitas = array_map('trim', explode('-', $fasilitasText));
            $data['fasilitas'] = array_filter($fasilitas);
        }

        // Ekstrak non-fasilitas
        if (preg_match('/Harga tidak termasuk\s*:[^\n]*(.+?)Ketentuan/s', $content, $matches)) {
            $nonFasilitasText = $matches[1];
            $nonFasilitas = array_map('trim', explode('-', $nonFasilitasText));
            $data['non_fasilitas'] = array_filter($nonFasilitas);
        }

        // Ekstrak ketentuan pembayaran
        if (preg_match('/Ketentuan Pembayaran\s*:(.+?)$/s', $content, $matches)) {
            $pembayaranText = $matches[1];
            $pembayaran = array_map('trim', explode("\n", $pembayaranText));
            $data['pembayaran'] = array_filter($pembayaran);
        }

        return $data;
    }
}