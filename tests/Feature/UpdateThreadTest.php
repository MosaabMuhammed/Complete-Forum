<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateThreadTest extends TestCase
{
	use RefreshDatabase;

	public function setUp()
	{
		parent::setUp();

		$this->withExceptionHandling();
     	$this->signIn();
	}
     /** @test */
     public function test_a_thread_can_be_updated () 
     {
     	$thread = create(Thread::class, ['user_id'	=>	auth()->id()]);
     	$this->json('patch', $thread->path(), [
     		'title'	=>	'changed', 
     		'body'	=>	'changed body.'
     	]);
     	tap($thread->fresh(), function ($thread) {
     		$this->assertEquals('changed', $thread->title);
     		$this->assertEquals('changed body.', $thread->body);
     	});
     }
     
	/** @test */
	public function test_a_thread_must_be_verified_to_be_updated () 
	{
     	$thread = create(Thread::class, ['user_id'	=>	auth()->id()]);
     	$this->patch($thread->path(), [
     		'title'	=>	'changed', 
     	])->assertSessionHasErrors('body');
	}

	/** @test */
	public function test_a_thread_can_be_updated_only_by_its_creator () 
	{
     	$thread = create(Thread::class, ['user_id'	=>	create(User::class)->id ]);
     	$this->patch($thread->path(), [
     		'title'	=>	'changed', 
     	])->assertStatus(403);
	}
}
