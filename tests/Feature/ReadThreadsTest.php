<?php

namespace Tests\Feature; use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThreadsTest extends TestCase
{
    // Because we're working with mySQL in migration and SQLite in phpunit
    // this line tells laravel to work with the existing migrations in mySQL
    use DatabaseMigrations;

    function setUp()
    {
        parent::setUp();
        $this->thread = create('App\Thread');
    }
    /** @test */
    function test_a_user_can_view_all_threads()
    {
        $this->get('/threads')
             ->assertSee($this->thread->title);
    }
   function test_a_user_can_see_one_single_thread()
    {
        $this->get('/threads/channel/' . $this->thread->slug)
             ->assertSee($this->thread->title);
    }
    /**
     * test for a user to read
     * @return type
     
     */
    function test_a_user_can_read_replies_that_associated_with_a_thread()
    {
        $reply = create('App\Reply', ['thread_id' => $this->thread->id ]);

        $this->get('/threads/channel/' . $this->thread->slug);
        $this->assertDatabaseHas('replies', ['body' =>  $reply->body]);
    }

    function test_filter_the_threads_by_userName()
    {
        $this->signIn(create('App\User', ['name' => 'JohnDoe']));
        $threadToSee = create(Thread::class, ['user_id' => auth()->id() ]);
        $threadToNotSee = create(Thread::class);

        $this->get('/threads?by=JohnDoe')
             ->assertSee($threadToSee->title)
             ->assertDontSee($threadToNotSee->title);

    }
    function test_arranging_the_threads_by_popularity()
    {
        $threadThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadThreeReplies->id], 3);
        $threadTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadTwoReplies->id], 2);
        $threadNoReplies = $this->thread;

        $response = $this->getJson('/threads?popular')->json();
        $this->assertEquals(['3', '2', '0'], array_column($response['data'], 'replies_count'));
    } 

    /** @test */
    public function test_a_user_can_see_all_the_replies_associated_with_a_thread () 
    {
        $thread = create(Thread::class);
        create(Reply::class, ['thread_id' => $thread->id], 2);
        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }

    /** @test */
    public function test_purify_the_body_of_the_thread () 
    {
        $thread = make(Thread::class, ['body' =>  "<script>alert('hello')</script> <p>Hello</p>"]);

        $this->assertEquals($thread->body, "<p>Hello</p>");
    }
    
    
}
