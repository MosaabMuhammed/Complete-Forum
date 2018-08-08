<?php

namespace Tests\Feature;

use App\User;
use App\Reply;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoriteTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
	function test_a_guest_can_not_favorite_anything()
	{
		$this->withExceptionHandling()
			 ->post('/replies/1/favorites')
			 ->assertRedirect('/login');
	}
	/** @test */
    function test_an_authenticatetd_user_can_favorite_any_reply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post('/replies/' . $reply->id . '/favorites');
        $this->assertCount (1, $reply->favorites);
    }
    
    function test_an_authenticated_user_can_favorite_one_time_only()
    {
    	$this->signIn();

    	$reply =  create(Reply::class);

    	// try {
    		$this->post('/replies/' . $reply->id . '/favorites');
    		$this->post('/replies/' . $reply->id . '/favorites');
    	// } catch (\Exception $e) { 
    	// 	$this->fail('Did not except to favorite more than one time for the same reply!');
    	// }
    	$this->assertCount(1, $reply->favorites);
    }
    /** @test */
    public function test_an_authenticated_user_can_unfavorite_a_reply () 
    {
        $this->signIn();
        $reply = create(Reply::class);
        $reply->favorite();
        $this->delete('replies/' . $reply->id . '/favorites');
        $this->assertCount(0, $reply->favorites);
    }
    
}
