<?php

namespace Tests\Feature;

use App\User;
use App\Reply;
use App\Thread;
use App\Activity;
use App\Trending;
use Tests\TestCase;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class CreateThreadsTest extends TestCase
{
	use DatabaseMigrations, MockeryPHPUnitIntegration;

	public function setUp()
	{
		parent::setUp();

		app()->singleton(Recaptcha::class, function() {
			return \Mockery::mock(Recaptcha::class, function ($m) {
				$m->shouldReceive('passes')->andReturn(true);
			});
		});
	}
	function test_a_guest_can_not_create_a_thread()
	{
		$this->withExceptionHandling()
			 ->get('/threads/create', [])
			 ->assertRedirect(route('login'));
	}
	// function test_an_authenticated_user_can_create_a_thread()
	// {
	// 	$response = $this->publishThread(['title' => 'Some Title', 'body' => 'Some body.']);
	// 	$this->get($response->headers->get('Location'))
	// 		 ->assertSee('Some Title')
	// 		 ->assertSee('Some body.');
	// }


	/** @test */
	function test_an_authorized_user_can_delete_its_own_thread () 
	{
		$this->signIn();
		$thread = create(Thread::class, ['user_id' => auth()->id() ]);
		$reply = create(Reply::class, ['thread_id' => $thread->id]);
		$response = $this->json('DELETE', $thread->path());
		$this->assertDatabaseMissing('threads', [ 'id' => $thread->id ]);
		$this->assertDatabaseMissing('replies', [ 'id' => $reply->id ]);

		$this->assertEquals(0, Activity::count());
		$response->assertStatus(204);
	}
	/** @test */
	function test_guests_can_not_delete_threads_and_replies () 
	{
		$this->withExceptionHandling();
		$thread = create(Thread::class);
		$this->delete($thread->path())->assertRedirect(route('login'));

		$this->signIn();
		$this->delete($thread->path())->assertStatus(403);
	}

	/** @test */
	public function test_Increment_the_slug_if_we_have_multiple_thread_with_the_same_title () 
	{
		$this->withExceptionHandling();
		$this->signIn();
		$thread = create(Thread::class, ['title'	=>	'Help Me']);
		$this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response'	=>	'token']);
		$this->assertEquals($thread->slug, 'help-me');
		$this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response'	=>	'token']);
		// $this->assertTrue(Thread::where('slug', 'help-me-2')->exists());
	}

	/** @test */
	public function test_a_thread_requires_recaptcha_verification () 
	{
		unset(app()[Recaptcha::class]);

		$this->publishThread(['g-recaptcha-response'	=>	'test'])
			 ->assertSessionHasErrors('g-recaptcha-response');
	}
	
     function publishThread($overrides = [])
     {
        $this->withExceptionHandling()->signIn();
        $thread = make('App\Thread', $overrides);
        return $response = $this->post('/threads', $thread->toArray() + ['g-recaptcha-response'	=>	'token']);
     }
	
	
			
}
