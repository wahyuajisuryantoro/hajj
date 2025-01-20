<!--start testimonials-->
<section class="section testimonials" id="testimonials">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <div>
                    <h5 class="mb-0">Testimoni</h5>
                </div>
            </div>
        </div>
        <!--end row-->
        <div class="row">
            <div class="col-lg-12">
                <div class="swiper mySwiper1 my-5">
                    <div class="swiper-wrapper">
                        @foreach($testimonis as $testimoni)
                        <div class="swiper-slide client-box card">
                            <div class="client-content card-body p-0">
                                <p class="text-dark mb-0 fs-10 lh-base">{{ $testimoni->content }}</p>
                            </div>
                            <!--end client-content-->
                            <div class="d-flex align-items-center mt-4 pt-3">
                                <img src="{{ $testimoni->picture }}" alt="{{ $testimoni->name }}" height="30" width="30" class="rounded-circle">
                                <div class="ms-2">
                                    <p class="mb-0 fs-8">{{ $testimoni->name }}</p>
                                    <p class="text-muted mb-0">{{ $testimoni->address }}</p>
                                </div>
                            </div>
                        </div>
                        <!--end client-box-->
                        @endforeach
                    </div>
                </div>
                <!--end widget-slider-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>
<!--end testimonials-->
