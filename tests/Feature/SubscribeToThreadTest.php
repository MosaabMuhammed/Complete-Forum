<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use App\ThreadSubscribtion;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
    public function test_a_user_can_subscribe_to_a_thread()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $this->post($thread->path() . '/subscriptions');

        $this->assertEquals(auth()->id(), ThreadSubscribtion::first()->user_id);
    }
    
    /** @test */
    public function test_a_user_can_unsubscribe_to_a_thread () 
    {
    	$this->signIn();
    	$thread = create(Thread::class);
    	$thread->subscribe(auth()->id());
    	// go to the endpoint to unsubscribe
        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0, $thread->subscriptions);
    }

    /** @test */
    public function test_check_if_the_thread_was_subscribed_by_the_user () 
    {
    	// Given the user
    	$this->signIn();
    	// Given the thread (not subscribed yet)
    	$thread = create(Thread::class);
    	// assert if not subscribed 
    	$this->assertFalse($thread->is_subscribed_to);
    	// subscribe to the thread
    	$thread->subscribe(auth()->id());
    	// assert if subscribed
    	$this->assertTrue($thread->is_subscribed_to);
    	// Unsubscribe to the thread
    	$thread->unsubscribe(auth()->id());
    	// assert if subscribed ...
    	$this->assertFalse($thread->is_subscribed_to);
    }
    
    
}
