<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddAvatarTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
    public function test_an_authenticated_users_can_only_add_avatar()
    {
    	$this->withExceptionHandling();
    	$this->json('POST', "/api/users/1/avatar")
        	 ->assertStatus(401);
    }

    /** @test */
    public function test_a_vaild_avatar_must_be_provided () 
    {
    	$this->withExceptionHandling();
    	$this->signIn();
    	$this->json('POST', "/api/users/". auth()->id() . "/avatar", [
    		'avatar'	=>	'not-an-image'
    	])->assertStatus(422);
    }
    
    /** @test */
    public function test_an_authenticated_user_can_upload_an_avatar_to_their_profile () 
    {
    	$this->signIn();
    	Storage::fake('public');
    	$this->json('POST', "/api/users/". auth()->id() . "/avatar", [
    		'avatar'	=>	$file = UploadedFile::fake()->image('avatar.jpg')
    	]);
    	$this->assertEquals('/storage/avatars/' . $file->hashName(), auth()->user()->avatar_path);
    	Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }

    /** @test */
    public function test_the_default_avatar_and_personal_avatar () 
    {
    	$user = create(User::class, ['avatar_path'	=>	'avatars/me.png']);

    	$this->assertEquals("/storage/avatars/me.png", $user->avatar_path);

    	$user = create(User::class);

    	$this->assertEquals("/storage/avatars/default.png", $user->avatar_path);
    }
    
    
}

