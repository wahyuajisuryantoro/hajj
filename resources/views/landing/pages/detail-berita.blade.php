@extends('landing.layouts.app')

@section('content')
<div class="container my-5">
   

    <div class="row">
        <div class="col-md-12">
            <h2>{{ $news->name }}</h2>
            <small class="text-muted d-block mb-3">
                {{ \Carbon\Carbon::parse($news->created_at)->format('l, d F Y H:i') }}
            </small>
            @if ($news->picture)
            <div class="mb-4">
                <img src="{{ $news->picture }}" alt="{{ $news->name }}" class="img-fluid rounded">
            </div>
            @endif
            <div>
                {!! nl2br(e($news->content)) !!}
            </div>
        </div>
    </div>
    <div class="mb-4">
        <a href="{{ route('home') }}" class="btn btn-primary">
            Kembali
        </a>
    </div>
</div>
@endsection
