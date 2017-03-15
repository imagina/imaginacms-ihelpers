# asgardcms-ihelpers

Based in:
https://github.com/spatie/laravel-responsecache/tree/v1
https://github.com/JosephSilber/page-cache (For file cache redirections)

To activate the Cache System

Modify

```php
// config/app.php

'providers' => [
    ...
    Modules\Ihelpers\Other\ImResponseCache\ImResponseCacheServiceProvider::class,
];
```

This package also comes with a facade.

```php
// config/app.php

'aliases' => [
    ...
   'ResponseCache' => Modules\Ihelpers\Other\ImResponseCache::class,
];
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Modules\Ihelpers\Other\ImResponseCache\ImResponseCacheServiceProvider"
```

Command available to clear cache

```bash
php artisan pagecache:clear
```