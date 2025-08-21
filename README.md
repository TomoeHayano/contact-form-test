# お問い合わせフォーム

## 環境構築

### Dockerビルド
1. `https://github.com/TomoeHayano/contact-form-test`
2. `docker-compose up -d --build`

### Laravel環境構築
1. `docker-compose exec php bash`
2. `composer install`
4. `php artisan key:generate`
5. `php artisan migrate`
6. `php artisan db:seed`

## 使用技術 (実行環境)
- PHP 8.1
- Laravel 8.83.29
- MySQL 8.0.26
- nginx 1.21.1
- phpMyAdmin (latest)
- Docker 20.x / Docker Compose v2

## ER図
![ER図](./ER図.png)

## URL
- 開発環境: [http://localhost](http://localhost)
- phpMyAdmin: [http://localhost:8080](http://localhost:8080)
