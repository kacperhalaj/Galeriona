<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <i class="fas fa-palette me-2 fs-3"></i>
            <span class="fw-bold">Galeriona</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#wyszukiwarka-section" id="header-search-btn"><i class="fas fa-search me-1"></i>Przeglądaj
                        dzieła</a></li>
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('guests.sellersIndex') }}">
                            <i class="fas fa-users me-1"></i>Sprzedawcy
                        </a>
                    </li>
                    @auth

                    @endauth
                @endguest
                <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-th me-1"></i>Kategorie</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-info me-1"></i>O nas</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-envelope me-1"></i>Kontakt</a>
                </li>
            </ul>

            <ul class="navbar-nav me-aitp">
                {{-- KOSZYK tylko dla zwykłego użytkownika --}}
                @auth
                    @php($user = auth()->user())
                    @if(auth()->user()->role === 'user')
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="show-followed-offers">
                                <i class="fas fa-heart me-1"></i>Zaobserwowani
                            </a>
                        </li>
                    @endif
                    @if (!$user->can('is-seller') && !$user->can('is-admin'))
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link text-white position-relative d-flex align-items-center"
                                href="{{ route('client.cart.index') }}">
                                <i class="fas fa-shopping-cart me-1"></i>
                                <span class="ms-1" style="font-weight: 500;">Koszyk</span>
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <span class="nav-link text-white">
                                Saldo: <span class="text-warning fw-bold">{{ number_format(auth()->user()->balance, 2, ',', ' ') }} zł</span>
                            </span>
                        </li>
                    @endif
                @endauth

                @auth
                    <li class="nav-item d-flex align-items-center">
                        <span class="nav-link text-white">
                            @can('is-seller')
                                Witaj sprzedawco <span class="text-warning fw-bold">{{ $user->username }}</span>!
                                <span class="ms-2">Saldo: <span class="text-warning fw-bold">{{ number_format(auth()->user()->balance, 2, ',', ' ') }} zł</span></span>
                            @elsecan('is-admin')
                                Witaj adminie <span class="text-warning fw-bold">{{ $user->username }}</span>!
                            @else
                                Witaj <span class="text-warning fw-bold">{{ $user->username }}</span>!
                            @endcan
                        </span>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        @can('is-seller')
                            <a class="nav-link text-white" href="{{ route('seller.panel') }}">
                                <i class="fas fa-store me-1"></i>Panel sprzedaży
                            </a>
                        @elsecan('is-admin')
                            <a class="nav-link text-white" href="{{ route('admin.panel') }}">
                                <i class="fas fa-tools me-1"></i>Panel admina
                            </a>
                        @else
                            <a class="nav-link text-white" href="{{ route('client.panel') }}">
                                <i class="fas fa-user-circle me-1"></i>Twój profil
                            </a>
                        @endcan
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link" style="padding: 0; color: red;">
                                <i class="fas fa-sign-out-alt me-1"></i>Wyloguj się
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i>Logowanie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary ms-2"
                            href="{{ route('register', ['role' => 'user']) }}">
                            <i class="fas fa-user-plus me-1"></i>Rejestracja
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
