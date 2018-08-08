<?php

namespace Tests\Unit;

use App\Reply;
use App\Thread;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{
	use DatabaseMigrations;

	function test_it_has_an_owner() {
		$reply = create('App\Reply');

		$this->assertInstanceOf('App\User', $reply->owner);
	}

	/** @test */
	public function test_guest_can_not_delete_a_reply () 
	{
		$this->withExceptionHandling();
		$reply = create(Reply::class);
		$response = $this->delete("replies/" . $reply->id);
		$response->assertRedirect('login');

		$this->signIn()
			 ->delete('/replies/' . $reply->id)
			 ->assertStatus(403);
	}

	/** @test */
	public function test_a_user_can_delete_his_own_reply () 
	{
		$this->signIn();
		$reply = create(Reply::class, ['user_id'	=>	auth()->id()]);
		$this->delete('replies/' . $reply->id);
		$this->assertDatabaseMissing('replies', ['id'	=>	$reply->id]);
	}

	/** @test */
	public function test_reply_knows_if_it_was_just_published () 
	{
		$reply = create(Reply::class);

		$this->assertTrue($reply->wasJustPublished());

		$reply->created_at = Carbon::now()->subMonth();

		$this->assertFalse($reply->wasJustPublished());
	}

	
	/** @test */
	public function test_reply_turns_the_mentioned_users_into_anchor_tag () 
	{
		$reply = new Reply([
			'body'	=>	"Hello @Jane-Doe.", 
		]);
		$this->assertEquals(
			"Hello <a href=\"/profile/Jane-Doe\">@Jane-Doe</a>.",
			$reply->body
		);
	}

	/** @test */
	public function test_is_best_function () 
	{
		$reply = create(Reply::class);
		$this->assertFalse($reply->isBest());
		$reply->thread->update(['best_reply_id'	=>	$reply->id]);
		$this->assertTrue($reply->fresh()->isBest());
	}
	
    /** @test */
    public function test_purify_the_body_of_the_reply () 
    {
        $reply= make(Reply::class, ['body' =>  "<script>alert('hello')</script> <p>Hello</p>"]);

        $this->assertEquals($reply->body, "<p>Hello</p>");
    }
}
