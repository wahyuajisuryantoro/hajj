<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';

    protected $fillable = [
        'code',
        'name',
        'code_category',
        'code_city',
        'price',
        'duration',
        'kuota',
        'sisa_kursi',
        'tanggal_berangkat',
        'desc',
        'picture',
        'status'
    ];

    protected $casts = [
        'price' => 'integer',
        'duration' => 'integer',
        'kuota' => 'integer',
        'sisa_kursi' => 'integer',
        'tanggal_berangkat' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    
    public function category()
    {
        return $this->belongsTo(ProgramCategory::class, 'code_category', 'code');
    }

    
    public function jamaahs()
    {
        return $this->hasMany(Jamaah::class, 'code_program', 'code');
    }

    
    public function getFormattedTanggalBerangkatAttribute()
    {
        return $this->tanggal_berangkat ? $this->tanggal_berangkat->format('d M Y') : '-';
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_berangkat', '>', now());
    }


    
    public function getFormattedDescAttribute()
    {
        $content = $this->desc;
        $data = [
            'tanggal' => null,
            'durasi' => null,
            'pesawat' => null,
            'hotels' => [],
            'fasilitas' => [],
            'non_fasilitas' => [],
            'pembayaran' => []
        ];

        
        if (preg_match('/Keberangkatan\s+(\d+\s+[A-Za-z]+\s+\d{4})\s+Program\s+(\d+)\s+Hari/', $content, $matches)) {
            $data['tanggal'] = $matches[1];
            $data['durasi'] = $matches[2];
        }

        
        if (preg_match('/Pesawat\s+([^H]+)Hotel/', $content, $matches)) {
            $data['pesawat'] = trim($matches[1]);
        }

        
        preg_match_all('/Hotel\s+([^:]+):\s*([^\n]+)/', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $data['hotels'][] = [
                'lokasi' => trim($match[1]),
                'nama' => trim($match[2])
            ];
        }

        
        if (preg_match('/Fasilitas\s*:[^\n]*(.+?)Harga tidak termasuk/s', $content, $matches)) {
            $fasilitasText = $matches[1];
            $fasilitas = array_map('trim', explode('-', $fasilitasText));
            $data['fasilitas'] = array_filter($fasilitas);
        }

        
        if (preg_match('/Harga tidak termasuk\s*:[^\n]*(.+?)Ketentuan/s', $content, $matches)) {
            $nonFasilitasText = $matches[1];
            $nonFasilitas = array_map('trim', explode('-', $nonFasilitasText));
            $data['non_fasilitas'] = array_filter($nonFasilitas);
        }

        
        if (preg_match('/Ketentuan Pembayaran\s*:(.+?)$/s', $content, $matches)) {
            $pembayaranText = $matches[1];
            $pembayaran = array_map('trim', explode("\n", $pembayaranText));
            $data['pembayaran'] = array_filter($pembayaran);
        }

        return $data;
    }
}
