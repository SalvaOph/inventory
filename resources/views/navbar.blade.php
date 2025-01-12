<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width: 40px; height: 40px;" class="me-2" />
            Siddhartha Saha Sport Supplies
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('inventories.index') }}">Inventories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('providers.index') }}">Providers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('clients.index') }}">Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('warehouses.index') }}">Warehouses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('purchases.index') }}">Purchases</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sales.index') }}">Sales</a>
                </li>
                @endauth
            </ul>

        </div>
    </div>
</nav>