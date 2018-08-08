<?php

namespace Tests\Feature;

use App\User;
use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUserTest extends TestCase
{use DatabaseMigrations;
	/** @test */
    public function test_notify_the_user_when_he_was_mentioned()
    {
        // Given we have a user
        $mosaab = create(User::class, ['name'	=>	'Mosaab']);
        // user -> signed In
        $this->signIn($mosaab);
        // Given another user
        $muhammed = create(User::class, ['name'	=>	'muhammed']);
        // create a thread
        $thread = create(Thread::class);
        // create a reply having other user mentioned
        $reply = make(Reply::class, [
        	'body'	=>	'Hey @muhammed check this out!, '
        ]);
        // assert if he was notified or not
        $this->post($thread->path() . '/replies', $reply->toArray());
        // Then @muhammed should recieve a notification
        $this->assertCount(1, $muhammed->notifications);
    }

    /** @test */
    public function test_it_can_fetch_the_related_user_from_database_Starting_with_given_character () 
    {
        create('App\User', ['name'  =>  'johnDoe']);
        create('App\User', ['name'  =>  'johnDoe2']);
        create('App\User', ['name'  =>  'janeDoe']);

        $results = $this->json('Get', '/api/users/', ['name'   =>  'john']);
        $this->assertCount(2, $results->json());
    }
    
    
}
