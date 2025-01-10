<!-- start program slider -->
<section class="section program-slider" id="program">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <h3 class="heading mb-3">Daftar <span class="text-primary">Program Umroh</span></h3>
                    <p class="text-muted mb-0">Pilih program umroh yang sesuai dengan kebutuhan Anda.</p>
                </div>
            </div>
        </div>
        <!-- Swiper slider -->
        <div class="row">
            <div class="col-lg-12">
                <div class="swiper mySwiper1">
                    <div class="swiper-wrapper">
                        @foreach($programs as $program)
                        <div class="swiper-slide">
                            <div class="team-member">
                                <div class="team-img">
                                    <img src="{{ $program->picture ?: asset('images/default-program.jpg') }}" alt="{{ $program->name }}" class="img-fluid">
                                </div>
                                <div class="team-hover">
                                    <div class="desk">
                                        <h4>{{ $program->name }}</h4>
                                        <p>Harga: Rp{{ number_format($program->price, 0, ',', '.') }}</p>
                                    </div>
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
