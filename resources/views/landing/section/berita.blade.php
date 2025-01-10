<!-- start news slider -->
<section class="section news-slider" id="berita">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <h3 class="heading mb-3">Berita <span class="text-primary">Terkini</span></h3>
                    <p class="text-muted mb-0">Berikut adalah berita terbaru dari kami.</p>
                </div>
            </div>
        </div>
        <!-- Swiper slider -->
        <div class="row">
            <div class="col-lg-12">
                <div class="swiper mySwiper1">
                    <div class="swiper-wrapper">
                        @foreach($news as $item)
                        <div class="swiper-slide">
                            <div class="team-member">
                                <div class="team-img">
                                    <img src="{{ $item->picture ?: asset('images/default-news.jpg') }}" alt="{{ $item->name }}" class="img-fluid">
                                </div>
                                <div class="team-hover">
                                    <div class="desk">
                                        <h4>{{ $item->name }}</h4>
                                        <p>{{ \Illuminate\Support\Str::limit($item->content, 100) }}</p>
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
<!-- end news slider -->
