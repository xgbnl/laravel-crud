## Business Helper

### 简单使用

- 引入包
```shell
composer require xgbnl/business-helper 
```

- 安装 `BaseController`
```shell
php artisan business:install 
```

- 如果需要使用缓存，请配置`redis`

```dotenv
REPOSITORY_CACHE=cache
REDIS_CACHE_DB=1
REDIS_HOST=redis
REDIS_PASSWORD=123456
REDIS_PORT=6379
```
- 编辑 `config/database.php`
```php 
'redis' => [

    // add 
    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
] 
```

[//]: # (### [详细文档]&#40;Doc.md&#41;)
