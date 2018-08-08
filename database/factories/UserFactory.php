<?php

use App\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\uuid4;
use Faker\Generator as Faker;
use Illuminate\Notifications\DatabaseNotification;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'confirmed' => true,
    ];
});

$factory->state(User::class, 'unconfirmed', function() {
    return [
        'confirmed' =>  false
    ];
});

$factory->state(User::class, 'administrator', function() {
    return [
        'name'  =>  'JohnDoe'
    ];
});

$factory->define(App\Thread::class, function (Faker $faker) {
    $title = $faker->sentence;
    return [
    	'user_id' => function() {
    		return factory('App\User')->create()->id;
    	},
        'channel_id' => function() {
            return factory('App\Channel')->create()->id;
        },
    	'title' => $title,
    	'body' => $faker->paragraph,
        'slug'  =>  str_slug($title), 
        'locked'    =>  false
    ];
});

$factory->define(App\Reply::class, function (Faker $faker) {
	$user_ids = App\User::pluck('id');
    return [
    	'thread_id' => function() {
    		return factory('App\Thread')->create()->id;
    	},
    	'user_id' =>  function () { 
            return factory('App\User')->create()->id;
        },
      	'body' => $faker->paragraph
    ];
});
$factory->define(App\Channel::class, function (Faker $faker) {
    $name = $faker->word;
    return [
       'name'   => $name,
       'slug'   => $name
    ];
});

$factory->define(DatabaseNotification::class, function (Faker $faker) {
    return [
        'id'    =>  Uuid::uuid4()->toString(),
        'type'  =>  'App\Notifications\ThreadWasCreated', 
        'notifiable_id' =>  function() {
            return auth()->id() ?: factory(User::class)->create()->id;
        }, 
        'notifiable_type'   =>  'App\User', 
        'data'  =>  ['foo'  =>  'bar']
    ];
});
