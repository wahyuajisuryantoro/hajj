<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketingTool extends Model
{
    use HasFactory;

    protected $table = 'marketing_tools';

    protected $fillable = [
        'name',
        'content',
        'picture',
        'file',
        'publish'
    ];

    protected $casts = [
        'publish' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scope untuk mendapatkan e-flayer yang dipublish
    public function scopePublished($query)
    {
        return $query->where('publish', '1');
    }

    // Scope untuk e-flayer
    public function scopeEflayer($query)
    {
        return $query->whereNotNull('picture');
    }

    // Scope untuk video
    public function scopeVideo($query)
    {
        return $query->whereNotNull('file');
    }

    // Formatter Kontent
    public function getFormattedContentAttribute()
    {
        $content = $this->content;
        $data = [
            'tanggal' => null,
            'durasi' => null,
            'pesawat' => null,
            'hotels' => [],
            'fasilitas' => [],
            'non_fasilitas' => [],
            'pembayaran' => []
        ];

        // Ekstrak tanggal dan durasi
        if (preg_match('/Keberangkatan\s+(\d+\s+[A-Za-z]+\s+\d{4})/', $content, $matches)) {
            $data['tanggal'] = $matches[1];
        }
        if (preg_match('/Program\s+(\d+)\s+Hari/', $content, $matches)) {
            $data['durasi'] = $matches[1];
        }

        // Ekstrak pesawat
        if (preg_match('/Pesawat\s+([^H]+)Hotel/', $content, $matches)) {
            $data['pesawat'] = trim($matches[1]);
        }

        // Ekstrak hotel-hotel
        preg_match_all('/Hotel\s+([^:]+):\s*([^\n]+)/', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $data['hotels'][] = [
                'lokasi' => trim($match[1]),
                'nama' => trim($match[2])
            ];
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
