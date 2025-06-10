composer require php-open-source-saver/jwt-auth
composer require dedoc/scramble --dev

php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
php artisan vendor:publish --tag=scramble-config

php artisan key:generate
php artisan jwt:secret