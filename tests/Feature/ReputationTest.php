<?php

namespace Tests\Feature;

use App\Thread;
use App\Reputation;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReputationTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function test_a_user_earns_points_when_creating_a_thread()
    {
    	$thread = create(Thread::class);

    	$this->assertEquals(10, $thread->creator->reputation);
    }

    /** @test */
    public function test_a_user_loses_points_when_deleting_a_thread () 
    {
        $this->withExceptionHandling()->signIn();

        $thread = create(Thread::class, ['user_id'  =>  auth()->id()]);

        $this->assertEquals(10, $thread->creator->reputation);

        $this->delete($thread->path());

        $this->assertEquals(0, $thread->creator->fresh()->reputation);
    }
    
    /** @test */
    public function test_a_user_earns_2_points_when_creating_a_reply () 
    {
    	$thread = create(Thread::class);
    	$reply = $thread->addReply([
    		'user_id'	=>	create('App\User')->id,
    		'body'	=>	'some comment'
    	]);
        $this->assertEquals(Reputation::REPLAY_WAS_ADDED, $reply->owner->reputation);
    }

    /** @test */
    public function test_a_user_loses_2_points_when_deleting_a_reply () 
    {
        $this->withExceptionHandling()->signIn();

        $reply = create('App\Reply', ['user_id' =>  auth()->id()]);

        $this->assertEquals(Reputation::REPLAY_WAS_ADDED, $reply->owner->reputation);

        $this->delete("/replies/" . $reply->id);

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }
    
    
    /** @test */
    public function test_a_user_earns_50_points_when_his_reply_marked_as_best_reply() 
    {
    	$thread = create(Thread::class);
    	$reply = $thread->addReply([
    		'user_id'	=>	create('App\User')->id,
    		'body'	=>	'some comment'
    	]);
    	$thread->markBestReply($reply);
    	$this->assertEquals(52, $reply->owner->reputation);
    }

    /** @test */
    public function test_a_user_awards_5_points_when_his_reply_is_favorited () 
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Thread');
        $reply = create('App\Reply', ['user_id' =>  auth()->id()]);

        $this->post('/replies/'. $reply->id . ' /favorites');

        $total = Reputation::REPLAY_WAS_FAVORITED + Reputation::REPLAY_WAS_ADDED;
        $this->assertEquals($total , $reply->owner->fresh()->reputation);
    }

   /** @test */
    public function test_a_user_loses_5_points_when_his_reply_is_unmarked_as_best_reply () 
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Thread');
        $reply = create('App\Reply', ['user_id' =>  auth()->id()]);

        $thread->markBestReply($reply);

        $total = Reputation::REPLAY_WAS_MARKED_BEST + Reputation::REPLAY_WAS_ADDED;
        $this->assertEquals($total , $reply->owner->reputation);

        $this->delete('/replies/'. $reply->id . ' /favorites');

        $this->assertEquals(Reputation::REPLAY_WAS_ADDED, $reply->owner->fresh()->reputation);
    } 
    
}
