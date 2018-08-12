<?php

namespace App;

use App\Thread;
use App\Favorite;
use Carbon\Carbon;
use App\Reputation;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
	use favoriable, RecordsActivity;
	
	protected $guarded = [];
	protected $with	= ['owner', 'favorites'];
	protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];

	
	protected static function boot()
	{
		parent::boot();
		static::created(function($reply) {
			$reply->thread->increment('replies_count');
			// $reply->owner->increment('reputation', 2);
			Reputation::award($reply->owner, Reputation::REPLAY_WAS_ADDED);
		});
		static::deleted(function($reply) {
			if($reply->isBest()) {
				$reply->thread->update(['best_reply_id'	=>	NULL]);
			}
			$reply->thread->decrement('replies_count');
			
			Reputation::reduce($reply->owner, Reputation::REPLAY_WAS_ADDED);
		});
	}
	public function owner()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function thread()
	{
		return $this->belongsTo(Thread::class);
	}

	public function favorited()
	{
		return $this->thread->path();
	}
	public function path()
	{
		return $this->thread->path() . "#reply-" . $this->id;
	}

	public function wasJustPublished()
	{
		return $this->created_at->gt(Carbon::now()->subMinute());
	}

	public function mentionedUsers()
	{
        preg_match_all("/\@([\w\-]+)/", $this->body, $names);
        return $names[1];
	}

	public function setBodyAttribute($body)
	{
		$this->attributes['body'] = preg_replace("/\@([\w\-]+)/", "<a href='/profile/$1'>$0</a>", $body);
	}

	public function isBest()
	{
		return ($this->thread->best_reply_id == $this->id);
	}

	public function getIsBestAttribute()
	{
		return $this->isBest();
	}

	public function getBodyAttribute($body)
	{
		return \Purify::clean($body);
	}
}
