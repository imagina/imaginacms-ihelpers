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

### URL rewriting

In order to serve the static files directly once they've been cached, you need to properly configure your web server to check for those static files.

- **For nginx:**

    Update your `location` block's `try_files` directive to include a check in the `page-cache` directory:

    ```nginxconf
    location / {
        try_files $uri $uri/ /page-cache/$uri.html /index.php?$query_string;
    }
    ```

- **For apache:**

    Open `public/.htaccess` and add the following before the block labeled `Handle Front Controller`:

    ```apacheconf
    # Serve Cached Page If Available...
    RewriteCond %{REQUEST_URI} ^/?$
    RewriteCond %{DOCUMENT_ROOT}/page-cache/pc__index__pc.html -f
    RewriteRule .? page-cache/pc__index__pc.html [L]
    RewriteCond %{DOCUMENT_ROOT}/page-cache%{REQUEST_URI}%{QUERY_STRING}.html -f
    RewriteRule . page-cache%{REQUEST_URI}.html [L]
    ```
  
### CUSTOM include and relationship features:
  
  You can set custom includes and relationships in any entity from any module as follows:
- **In Modules\Imodule\Config\config.php:**
   ```php
   'includes'=>[
       'EntityTransformer'=>[
         'otherEntity'=>[
           'path'=>'Modules\Iothermodule\Transformers\OtherEntityTransformer', //this is the transformer path
           'multiple'=>false, //if the relationship is one-to-many, multiple must be set to true
         ],
       ],
       ...
   ],   
   'relations' =>[
     'entity'=>[
       'otherEntity' => function () {
         return $this->hasOne(
           \Modules\Iothermodule\Entities\OtherEntity::class, 'model_id');
       },
     ],
     ...
   ],  
   ```
- **In Modules\Imodule\Entities\Entity.php:**
  
  You must be use the trait for use the custom relations as follows:
  
  ```php
  use Modules\Ihelpers\Traits\Relationable;
  class Entity extends Model{
    use Relationable;
  ```
- **In Modules\Imodule\Transformers\EntityTransformer.php:**
    
  You must be use the trait for use the custom includes as follows:
  
  ```php
    use Modules\Ihelpers\Traits\Transformeable;
    class EntityTransformer extends JsonResource{
      use Transformeable;
  ```  

### UserStamp (created_by, updated_by and deleted_by-deleted_at) feature:

 **First, you must create a migration with following fields:**
 
 
 
 Usually generated as Modules\Imodule\Database\Migrations\2021_04_28_173335_add_userstamps_to_imodule_table_table.php:
 
  ```php
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class AddUserstampsToImoduleTableTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('imodule__table', function (Blueprint $table) {
                $table->integer('created_by')->unsigned()->nullable();
                $table->integer('updated_by')->unsigned()->nullable();
                $table->integer('deleted_by')->unsigned()->nullable();
                $table->timestamp('deleted_at')->nullable();
                $table->foreign('created_by')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
                $table->foreign('updated_by')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
                $table->foreign('deleted_by')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
            });
        }
    
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('imodule__table', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropForeign(['deleted_by']);
                $table->dropColumn(['created_by','updated_by','deleted_by','deleted_at']);
            });
        }
    }

  ``` 
**Use in Module Entities**

**In Modules\Imodule\Entities\Entity.php:**
  
  You must be use the trait for use the user stamps as follows:
  
  ```php
  use Modules\Ihelpers\Traits\UserStamps;
  class Entity extends Model{
    use UserStamps;
  ```
  
  Besides, if you want to use softdeleting you must use the SoftDeletes trait and set softdeleting to true:
  
  ```php
  use Modules\Ihelpers\Traits\UserStamps;
  use Illuminate\Database\Eloquent\SoftDeletes;
  class Entity extends Model{
    use UserStamps, SoftDeletes;
    
    protected $table = 'imodule__table';
    
    protected $softdeleting = true; //it enables or disables the softdeleting
  ```


