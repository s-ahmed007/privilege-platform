

### composer global require
`composer global require consolidation/cgr`

### CORS Middleware
install
`composer require barryvdh/laravel-cors`
add to Kernel.php
`Barryvdh\Cors\ServiceProvider::class`
copy config
`php artisan vendor:publish --provider="Barryvdh\Cors\ServiceProvider"`
dynamically add origin to allow list
`$origin = array_get($_SERVER, 'HTTP_ORIGIN');`
`'allowedOrigins' => [$origin],`
