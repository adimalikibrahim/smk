TEST
TEST SECOND

cp .env.example .env
composer install
composer update

php artisan key:generate
php artisan migrate
php artisan db:seed


DB_DATABASE=TestDb
DB_USERNAME=postgres
DB_PASSWORD=