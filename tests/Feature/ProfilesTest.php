<?php

namespace Tests\Feature;

use App\User;
use App\Thread;
use App\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
    function test_a_user_has_a_profile()
    {
        $user = create(User::class);
        $thread = create(Thread::class, ['user_id' => $user->id]);

        // dd("/profile/{$user->name}");
        $this->get("/profile/{$user->name}")
        	 ->assertSee($user->name);
    }
    /** @test */
    function test_display_all_threads_that_was_created_by_specific_user () 
    {
        // $user = create(User::class);
        $this->signIn();
        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $activity = Activity::first();
        $this->get("/profile/" . auth()->user()->name)
             ->assertSee($activity->subject->title)
             ->assertSee($activity->subject->body);
    }
}
