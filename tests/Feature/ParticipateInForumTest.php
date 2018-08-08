<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForum extends TestCase
{
	use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->withExceptionHandling();
    	// signed User
        $this->signIn();

    	// Thread to add replies to it
    	$thread = create('App\Thread');

    	// Reply to be add
    	$reply = make('App\Reply');

    	// send the data to the specific route
    	$this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body'   => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    function test_guest_can_not_reply()
    {
        $this->withExceptionHandling()
             ->post('/threads/channel/1/replies', [])
             ->assertRedirect('/login');
    }

    function test_a_reply_requires_body()
    {
       $this->withExceptionHandling()->signIn();

       $thread = create('App\Thread');
       $reply = make('App\Reply', ['body' => null ]);

       $response = $this->post($thread->path() . '/replies', $reply->toArray());
       // $response->assertSessionHasErrors(['body']);
       $this->assertDatabaseMissing('replies', [$reply->id]);
    }
    
    /** @test */
    public function test_a_guest_can_NOT_update_a_reply () 
    {
        $this->withExceptionHandling();
        $reply = create(Reply::class);
        $this->patch('/replies/' . $reply->id)
            ->assertRedirect('/login');
    }

    /** @test */
    public function test_an_authenticated_user_can_update_a_reply () 
    {
        $this->signIn();
        $reply = create(Reply::class, ['user_id'    =>  auth()->id()]);
        $this->patch('/replies/' . $reply->id, ['body'   =>  'testing']);
        $this->assertDatabaseHas('replies', ['id'   =>  $reply->id , 'body' =>  'testing']);
    }

    /** @test */
    public function test_when_body_contains_spam_message_throw_an_Exception () 
    {
        $this->signIn();
        $thread = create(Thread::class);
        $reply = make(Reply::class, [
            'body'  =>  "Yahoo Message Spam"
        ]);
        $this->expectException(\Exception::class);
        $this->post($thread->path() . '\replies',  $reply->toArray());

    }
    
    /** @test */
   public function test_users_may_only_reply_a_max_once_per_minute () 
   {
        $this->withExceptionHandling();
       $this->signIn();

       $thread = create(Thread::class);
       $reply = make(Reply::class);

       $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(200);

       $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);
   }
          
}
