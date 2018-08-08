<?php

namespace Tests\Feature;

use App\Thread;
use App\Activity;
use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ActivityTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
    public function test_records_activity_when_thread_is_created()
    {
    	$this->signIn();
    	$thread = create(Thread::class);
    	$this->assertDatabaseHas('activities', [
    		'user_id'	=>	auth()->id(),
    		'type'	=>	'created_thread', 
    		'subject_id'	=>	$thread->id,
    		'subject_type'	=>	'App\Thread'
    	]);
    	$activity = Activity::first();
    	$this->assertEquals($activity->subject_id, $thread->id);
    }

    public function test_records_activity_when_reply_is_created()
    {
    	$this->signIn();
    	$reply = create('App\Reply');
    	$this->assertDatabaseHas('activities', [
    		'user_id'		=>	auth()->id(),
    		'subject_id'	=>	$reply->id,
    		'subject_type'	=>	'App\Reply', 
    		'type'			=>	'created_reply'
    	]);

    	$activity = Activity::first();
    	$this->assertEquals($activity->subject_id, $reply->id);
    }

    /** @test */
    public function test_a_profile_shows_the_user_data_with_the_according_date () 
    {
        // Given the authenticated User
        $this->signIn();
        // Given two threads related to that user one (now) and the other week ago
        create(Thread::class, ['user_id'    =>  auth()->id() ], 2);

        auth()->user()->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        $feed = Activity::feed(auth()->user());
        // check if they show correctly on the profile page
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }
    
    
}
