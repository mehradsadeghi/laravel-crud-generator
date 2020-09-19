# Laravel CRUD Generator
Generate a CRUD scaffold like a breeze.

*Compatible with Laravel **5.x** **6.x** **7.x** **8.x***.

### Installation
`$ composer require mehradsadeghi/laravel-crud-generator`

### Usage
It works based on your `$fillable` property of the target model.

If you would like to use `$guarded` instead of `$fillable`, It is supported too. 
In that case you'll need to have an existing `Schema` (table), Then the crud generator will autimatically figures out your fillables.

As an example when `$fillable` is available:

`$ php artisan make:crud UserController --model=User --validation`

![laravel-crud-generator](https://user-images.githubusercontent.com/31504728/92512225-b99be400-f223-11ea-84ba-bbfb55d1babd.gif)

#### Customizing Stubs
You can modify default stubs by publishing them:

`$ php artisan crud:publish`

The published stubs will be located within `stubs/crud` directory in the root of your application.
Any changes you make to these stubs will be reflected when you generate crud.
