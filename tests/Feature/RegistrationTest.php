<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
    public function test_A_confirmation_email_is_sent_upon_registration()
    {
    	Mail::fake();

    	event(new Registered(create('App\User')));

    	Mail::assertSent(PleaseConfirmYourEmail::class);
    }

    /** @test */
    public function test_user_can_fully_confirm_their_email_addresses () 
    {
    	Mail::fake();

    	$this->post(route('register'), [
    		'name'	=>	'John', 
    		'email'	=>	'john@example.com',
    		'password'	=>	'foobar', 
    		'password_confirmation'	=>	'foobar'
    	]);

    	$user = User::where('name', 'John')->first();

    	$this->assertFalse($user->confirmed);
    	$this->assertNotNull($user->confirmation_token);

    	$this->get(route('register.confirm', ['token'	=>	$user->confirmation_token]))
			 ->assertRedirect(route('threads'));

		$this->assertTrue($user->fresh()->confirmed);
    }

    /** @test */
    public function test_confirming_an_invaild_token () 
    {
    	$this->get(route('register.confirm', ['token'	=>	'invalid']))
    		 ->assertRedirect(route('threads'))
    		 ->assertSessionHas('flash', 'Unknown Token.');
    }
    
    
    
}
