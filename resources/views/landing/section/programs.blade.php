<!-- start program slider -->
<section class="section program-slider" id="program">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h5 class="mb-0">Program</h5>
                <small class="text-muted">Daftar Program</small>
            </div>
            <a href="{{ route('programs.index') }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5; border-radius: 0.6rem;">Lihat Semua Program</a>
        </div>

        <!-- Swiper slider -->
        <div class="row">
            <div class="col-lg-12">
                <div class="swiper mySwiper1">
                    <div class="swiper-wrapper">
                        @foreach ($programs as $program)
                            <div class="swiper-slide">
                                <div class="program-card">
                                    <div class="program-img">
                                        <a href="{{ route('programs.show', $program->id) }}">
                                            <img src="{{ $program->picture ?: asset('images/default-program.jpg') }}"
                                                alt="{{ $program->name }}" class="img-fluid rounded">
                                        </a>
                                    </div>
                                    <div class="program-content mt-3">
                                        <h6 class="mb-2">
                                            <a href="{{ route('programs.show', $program->id) }}" class="text-dark">
                                                {{ $program->name }}
                                            </a>
                                        </h6>
                                        <p class="text-primary mb-0">Rp{{ number_format($program->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end program slider -->
