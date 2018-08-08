<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadLockTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
	public function test_non_admin_may_not_lock_the_thread () 
	{
		$this->withExceptionHandling();
		$this->signIn();
		$thread = create(Thread::class, ['user_id'	=>	auth()->id()]);
    	$this->post(route('locked-thread-store', $thread))->assertStatus(403);
		$this->assertFalse($thread->fresh()->locked);
	}
	
	/** @test */
    public function test_locking_a_thread_means_not_receving_any_replies()
    {
    	$this->signIn();
        $thread = create(Thread::class);
        $thread->lock();
        $this->post($thread->path() . '/replies', [
        	'body'	=>	'foobar', 
        	'user_id'	=>	auth()->id()
        ])->assertStatus(422);
    }

    /** @test */
    public function test_administrator_can_lock_any_thread () 
    {
    	$this->signIn(factory('App\User')->states('administrator')->create());
    	$thread = create(Thread::class, ['user_id'	=>	auth()->id()]);
    	$this->post(route('locked-thread-store', $thread));
		$this->assertTrue($thread->fresh()->locked);
    }
    
    /** @test */
    public function test_administrator_can_unlock_any_thread () 
    {
        $this->signIn(factory('App\User')->states('administrator')->create());
        $thread = create(Thread::class, ['user_id'  =>  auth()->id(), 'locked'  =>  true]);
        $this->delete(route('locked-thread-destroy', $thread));
        $this->assertFalse($thread->fresh()->locked);
    }
}

