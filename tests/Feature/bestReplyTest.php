<?php

namespace Tests\Feature;

use App\User;
use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class bestReplyTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
    public function test_a_thread_creator_can_mark_best_reply()
    {
    	$this->withExceptionHandling();
        $this->signIn();
         $thread = create('App\Thread', ['user_id' => auth()->id()]);
         $replies = create('App\Reply', ['thread_id' => $thread->id], 2);
         $this->assertFalse($replies[1]->isBest());
         $this->postJson(route('bestReply.store', [$replies[1]->id]));
         $this->assertTrue($replies[1]->fresh()->isBest());
    }
    
    /** @test */
    public function test_Thread_creator_only_can_mark_best_reply () 
    {
    	$this->withExceptionHandling()->signIn();
    	$thread = create(Thread::class, ['user_id'	=>	auth()->id()]);
    	$replies = create(Reply::class, ['thread_id'	=>	$thread->id], 2);
    	$this->signIn(create(User::class));
    	$this->postJson(route('bestReply.store', [$replies[1]->id]))->assertStatus(403);
    	$this->assertFalse($replies[1]->fresh()->isBest());
    }
    
}
