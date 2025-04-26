# CoachTech 勤怠管理アプリ

## 環境構築

### Docker環境構築(ビルド)
1. `git clone https://github.com/Roba-97/ObanaRyota-mockcase1-furima-app.git`
2. Docker Desctop アプリを起動して `docker-compose up -d --build`

### Laravel環境構築
1. `docker-compose exec php bash`
2. `composer install`
3. .env.exampleをコピーして「.env」ファイルを作成し以下の環境変数を設定
``` copy
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
4. `php artisan key:generate`
5. `php artisan migrate --seed`

## 使用技術
- PHP 7.4.9
- Laravel Framework 8.83.8
- mysql:8.0.26
- nginx:1.21.1
- mailhog:latest

## ER図
![ER図](/src/ER-diagram.drawio.png)

## URL
- 開発環境 : [http://localhost/](http://localhost/ )
- 一般ユーザー登録 : [http://localhost/register/](http://localhost/register)
- 管理ユーザーログイン : [http://localhost/admin/login](http://localhost/admin/login)
- phpMyAdmin : [http://localhost:8080/](http://localhost:8080/)
- MailHog : [http://localhost:8025/](http://localhost:8025/)
