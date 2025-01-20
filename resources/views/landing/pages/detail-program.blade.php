@extends('landing.layouts.app')

@section('content')
<div class="container my-5">
    
    <div class="row">
        <div class="col-md-6">
            <img src="{{ $program->picture ?: asset('images/default-program.jpg') }}" 
                 alt="{{ $program->name }}" class="img-fluid rounded">
        </div>
        <div class="col-md-6">
            <h2>{{ $program->name }}</h2>
            <p class="text-primary">Rp{{ number_format($program->price, 0, ',', '.') }}</p>
            <p><strong>Durasi:</strong> {{ $program->duration }} hari</p>
            <p><strong>Tanggal Keberangkatan:</strong> {{ $program->formatted_tanggal_berangkat }}</p>
            <p><strong>Deskripsi:</strong></p>
            <p>{!! nl2br(e($program->desc)) !!}</p>
        </div>
    </div>

    @if($program->formatted_desc)
    <div class="row mt-4">
        <div class="col-md-12">
            <h4>Detail Program</h4>
            <ul>
                <li><strong>Tanggal:</strong> {{ $program->formatted_desc['tanggal'] ?? '-' }}</li>
                <li><strong>Durasi:</strong> {{ $program->formatted_desc['durasi'] ?? '-' }} hari</li>
                <li><strong>Pesawat:</strong> {{ $program->formatted_desc['pesawat'] ?? '-' }}</li>
            </ul>

            <h5>Fasilitas</h5>
            <ul>
                @foreach($program->formatted_desc['fasilitas'] as $fasilitas)
                    <li>{{ $fasilitas }}</li>
                @endforeach
            </ul>

            <h5>Fasilitas yang Tidak Termasuk</h5>
            <ul>
                @foreach($program->formatted_desc['non_fasilitas'] as $nonFasilitas)
                    <li>{{ $nonFasilitas }}</li>
                @endforeach
            </ul>

            <h5>Ketentuan Pembayaran</h5>
            <ul>
                @foreach($program->formatted_desc['pembayaran'] as $pembayaran)
                    <li>{{ $pembayaran }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    <div class="mb-4">
        <a href="{{ route('home') }}" class="btn btn-primary">
            Kembali
        </a>
    </div>
</div>
@endsection
