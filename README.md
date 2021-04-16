# Laravel CRUD Generator
Generate a CRUD scaffold like a breeze.

*Compatible with Laravel **5.x** **6.x** **7.x** **8.x***.

### Installation
`$ composer require mehradsadeghi/laravel-crud-generator`

### Usage
It works based on your `$fillable` property of the target model.

If you would like to use `$guarded` instead of `$fillable`, It is supported too. 
In that case you'll need to have an existing `Schema` (table), Then the crud generator will automatically figures out your fillables.

As an example when `$fillable` is available:

`$ php artisan make:crud UserController --model=User --validation`

![laravel-crud-generator](https://user-images.githubusercontent.com/31504728/92512225-b99be400-f223-11ea-84ba-bbfb55d1babd.gif)

#### Customizing Stubs
You can modify default stubs by publishing them:

`$ php artisan crud:publish`

The published stubs will be located within `stubs/crud` directory in the root of your application.
Any changes you make to these stubs will be reflected when you generate crud.

----------------------------------------------

### Your Stars Matter 
If you find this package useful and you want to encourage me to maintain and work on it, Just press the star button to declare your willing.

----------------------------------------------

### Reward me with a cup of tea :tea:

Send me as much as a cup of tea worth in your country, so I'll have the energy to maintain this package.

- Ethereum: 0x2D5BFdEc132F9F0E9498Fb0B58C800db4007D154

