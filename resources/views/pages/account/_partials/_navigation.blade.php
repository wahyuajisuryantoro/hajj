<div class="nav-align-top">
    <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('account.settings') ? 'active' : '' }}" 
               href="{{ route('account.settings') }}">
                <i class="ri-user-line me-2"></i>Akun
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('account.edit-bank') ? 'active' : '' }}"
               href="{{ route('account.edit-bank') }}">
                <i class="ri-bank-line me-2"></i>Info Bank
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('account.security') ? 'active' : '' }}"
               href="{{route('account.security')}}">
                <i class="ri-lock-line me-2"></i>Keamanan
            </a>
        </li>
    </ul>
</div>