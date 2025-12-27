<header class="main-header">
    {{-- Item 1: Logo (Stays on the Left) --}}
    <div class="logo">
    <a href="{{ route('home') }}" class="logo-link">
        <span>H</span>
        <span>A</span>
        <span>N</span>
        <span>D</span>
        <span>M</span>
        <span>A</span>
        <span>D</span>
        <span>E</span>
    </a>
</div>

    {{-- Item 2: A single container for EVERYTHING on the right --}}
    <div class="header-right-side">
        <nav class="main-nav">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('shop.index') }}">Shop</a></li>

                {{-- The "Sign Up" and "Log In" links are only for guests --}}
                @guest
                    <li><a href="{{ route('register') }}">Sign Up</a></li>
                    <li><a href="{{ route('login') }}">Log In</a></li>
                @endguest
            </ul>
        </nav>

        <div class="header-search">
            <form action="{{ route('shop.index') }}" method="GET">
                <input type="text" name="search" placeholder="Search" value="{{ request('search') }}">
                <button type="submit" aria-label="Search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
            </form>
        </div>

        <div class="header-icons">
            <a href="{{ route('cart.index') }}" aria-label="Shopping Bag">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            </a>
            
            @guest
                {{-- If the user is a guest, the icon links to the SIGN UP page --}}
                <a href="{{ route('register') }}" aria-label="Sign Up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>
            @endguest

            @auth
                {{-- Your full, working dropdown menu for authenticated users --}}
                <div class="profile-dropdown-container">
                    <a href="{{ route('profile') }}" class="profile-dropdown-toggle" aria-label="User menu">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </a>
                    <div class="profile-dropdown-menu">
                        <a href="{{ route('profile') }}" class="dropdown-item">User Profile</a>
                        {{-- 
    This is a ternary operator. It's a clean one-line if/else statement.
    IF the user has a shop, the link goes to the 'shops.dashboard' route.
    ELSE (if they don't), the link goes to the 'shops.create' route.
--}}
<a href="{{ Auth::user()->shop ? route('shops.dashboard') : route('shops.create') }}" class="dropdown-item">
    Shop Page
</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" 
                               class="dropdown-item" 
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </a>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
