<?php

namespace Tests\Feature;

use App\Thread;
use App\Trending;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
	use DatabaseMigrations;

	protected function setUp()
	{
		parent::setUp();

        $this->trending = new Trending;

		Redis::del($this->trending->cacheKey());
	}

	/** @test */
    public function test_showing_a_thread_increment_its_popularity_using_Redis()
    {
    	$this->assertEmpty(Redis::zrevrange($this->trending->cacheKey(), 0, -1));

        $thread = create(Thread::class);

        $this->call('GET', $thread->path());

        $this->assertCount(1, $trending =  $this->trending->get());

        $this->assertEquals($thread->title, $trending[0]->title);
    }
    
}

