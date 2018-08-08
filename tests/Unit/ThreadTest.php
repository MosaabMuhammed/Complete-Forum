<?php

namespace Tests\Unit;

use App\User;
use App\Thread;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadTest extends TestCase
{
	use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    function setUp()
    {
        parent::setUp();
        $this->thread = create('App\Thread');
    }
    // @test //
    function test_a_thread_has_replies()
    {
    	$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }
    // @test // 
    function test_a_thread_has_a_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }
    // @test //
    function test_a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar', 
            'user_id' => 1
        ]);
        $this->assertCount(1, $this->thread->replies);
    }
    
    function test_a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');
        $this->assertInstanceOf('App\Channel', $thread->channel);
    }
    function test_check_the_path_of_the_thread()
    {
       $thread = create('App\Thread') ;
       $this->assertEquals(
            "/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path()
        );
    }

    function test_a_thread_requires_a_title()
     {
        $this->publishThread(['title' => null ])
             ->assertSessionHasErrors(['title']);
     } 

     function test_a_thread_requires_a_body()
     {
        $this->publishThread(['body' => null ]) 
             ->assertSessionHasErrors(['body']);
     }

     function test_a_thread_requires_channel_id()
     {
        $this->withExceptionHandling()->signIn();

        $this->publishThread(['channel_id' => null])
           ->assertSessionHasErrors(['channel_id']);

        $this->publishThread(['channel_id' => 999])
             ->assertSessionHasErrors(['channel_id']);
     }

     function publishThread($overrides = [])
     {
        $this->withExceptionHandling()->signIn();
        $thread = make('App\Thread', $overrides);
        return $response = $this->post('/threads', $thread->toArray());
     }
     
     /** @test */
     public function test_a_user_can_subscribe_to_a_thread () 
     {
         // Given the thread
        $thread = $this->thread;
        // the user tries to subscribe to the thread 
        $thread->subscribe($userId = 2);
        // check if the user subscribed to the thread
        $this->assertEquals(
            1, 
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
     }

    public function test_a_user_can_unsubscribe_to_a_thread() 
     {
         // Given the thread
        $thread = $this->thread;
        // the user tries to subscribe to the thread 
        $thread->unsubscribe($userId = 2);
        // check if the user subscribed to the thread
        $this->assertEquals(
            0, 
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
     }

     /** @test */
     public function test_a_thread_can_check_if_the_authenticated_user_has_read_all_replies () 
     {
         $this->signIn();

         $thread = create(Thread::class);
         $this->assertTrue($thread->HasUpdatesFor(auth()->id()));
         $key = sprintf("users.%s.visits.%s", auth()->id(), $thread->id);
         cache()->forever($key, Carbon::now());

         $this->assertFalse($thread->HasUpdatesFor(auth()->user()));
     }
     
     /** @test */
     public function test_each_thread_shows_a_number_of_visits () 
     {
         $thread = create(Thread::class);

         Redis::del($thread->visitsCacheKey());

         $thread->recordVisit();

         $this->assertEquals(1, $thread->getVisits());
     }

    /** @test */
    public function test_an_authenticated_user_can_create_only_with_confirmation () 
    {
        $user = factory(User::class)->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make('App\Thread');

        $this->post(route('threads'), $thread->toArray())
             ->assertRedirect(route('threads'))
             ->assertSessionHas('flash', 'You must first confirm your email address.');
    }

    /** @test */
    public function test_lock_function () 
    {
        $this->assertFalse($this->thread->fresh()->locked);
        $this->thread->lock();
        $this->assertTrue($this->thread->fresh()->locked);
    }
    
}

