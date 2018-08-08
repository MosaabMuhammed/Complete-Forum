<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NotificationsTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->signIn();
	}
	/** @test */
    public function test_a_notification_is_prepared_when_a_subscribed_thread_recives_a_new_reply_that_is_not_by_the_current_user()
    {
    	$thread = create(Thread::class);

    	$this->post($thread->path() . '/subscriptions');

    	$this->assertCount(0, auth()->user()->notifications);

    	$thread->addReply([
    		'user_id'	=>	auth()->id(),
    		'body'	=>	'some reply here' 
    	]);

    	$this->assertCount(0, auth()->user()->fresh()->notifications);

    	$thread->addReply([
    		'user_id'	=>	create(User::class)->id,
    		'body'	=>	'some reply here', 
    	]);

    	$this->assertCount(1, auth()->user()->fresh()->notifications);
    }
    
    public function test_a_user_can_clear_a_notification()
    {
    	create(DatabaseNotification::class);

    	tap(auth()->user(), function($user) {
	    	$this->assertCount(1, $user->unreadnotifications);

	    	$this->delete("/profiles/" . $user->name . "/notifications/" . $user->unreadnotifications->first()->id);

	    	$this->assertCount(0, $user->fresh()->unreadnotifications);
    	});
    }

    public function test_a_user_can_fetch_all_notifications()
    {
    	create(DatabaseNotification::class);
    	$user = auth()->user();
    	$response = $this->getJson("/profiles/{$user->name}/notifications")->json();
    	$this->assertCount(1, $response);
    }
}
