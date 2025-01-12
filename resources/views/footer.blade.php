<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <!-- Logo o Nombre -->
            <div class="col-md-6 mb-3">
                <h5>Siddhartha Saha Sport Supplies S.A de C. V.</h5>
                <p>Sports Equipment and Machinery Industry.</p>
            </div>

            <!-- Información de contacto -->
            <div class="col-md-3 mb-3">
                <h5>Contact us</h5>
                <ul class="list-unstyled">
                    <li><i class="bi bi-telephone-fill"></i> Telephone: +503 2555-2555</li>
                    <li><i class="bi bi-envelope-fill"></i> E-mail: info@siddharthasport.com</li>
                </ul>
            </div>

            <!-- Enlaces rápidos -->
            <div class="col-md-3 mb-3">
                <h5>Links</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('products.index') }}" class="text-white text-decoration-none">Products</a></li>
                    <li><a href="{{ route('warehouses.index') }}" class="text-white text-decoration-none">Warehouses</a></li>
                    <li><a href="{{ route('sales.index') }}" class="text-white text-decoration-none">Sales</a></li>
                </ul>
            </div>
        </div>

        <hr class="border-white">
        <div class="text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Siddhartha Saha Sport Supplies S.A de C.V. All rights reserved.</p>
        </div>
    </div>
</footer>
