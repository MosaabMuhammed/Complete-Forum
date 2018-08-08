<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ChannelTest extends TestCase
{
	use DatabaseMigrations;
    function test_view_the_threads_that_is_associated_with_a_channel()
    {
		$channel = create('App\Channel');
		$threadInChannel = create('App\Thread', ['channel_id' => $channel->id ]);
		$threadNotInChannel = create('App\Thread');

		$this->get("/threads/{$channel->slug}")
			 ->assertSee($threadInChannel->title)
			 ->assertDontSee($threadNotInChannel->title);
    }
    function test_a_channel_contains_threads()
    {
    	$channel = create('App\Channel');
    	$thread  = create('App\Thread', ['channel_id' => $channel->id]);

    	$this->assertTrue($channel->threads->contains($thread));
    	// $this->assertInstanceOf(Collection::class, $channel->threads);
    }
    
}
