<nav class="navbar navbar-expand-lg fixed-top navbar-white navbar-custom sticky" id="navbar">
    <div class="container">
        <!-- LOGO -->
        <a class="navbar-brand text-uppercase" href="{{ route('home') }}">
            <img class="logo-light" src="{{ asset('images/logoap.png') }}" alt="" height="50">
            <img class="logo-dark" src="{{ asset('images/logoap.png') }}" alt="" height="50">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="mdi mdi-menu"></span>
            <!-- toggle button -->
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mx-auto" id="navbar-navlist">
                @if (Request::route()->getName() === 'home')
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#program">Program</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#berita">Berita</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="panduanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Panduan
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="panduanDropdown">
                            <li><a class="dropdown-item" href="{{ route('panduan.hapus-akun') }}">Hapus Akun</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('home') }}#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#program">Program</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#berita">Berita</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="panduanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Panduan
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="panduanDropdown">
                            <li><a class="dropdown-item" href="{{ route('panduan.hapus-akun') }}">Hapus Akun</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#testimonials">Testimonials</a>
                    </li>
                @endif
            </ul>
        </div>
            <!-- Button trigger modal -->
            <a href="{{ route('mitra.login') }}" class="btn btn-primary nav-btn">
                Masuk Mitra
            </a>
            <!-- Nav btn -->
        </div>
    </div><!-- End container -->
</nav>
