init:
	docker-compose up -d --build
	sleep 30
	docker-compose exec php composer install
	docker-compose exec php cp .env.example .env
	docker-compose exec php php artisan key:generate
	docker-compose exec php chmod -R 777 storage bootstrap/cache
	docker-compose exec php php artisan migrate --seed