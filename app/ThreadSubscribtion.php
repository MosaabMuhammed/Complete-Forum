<?php

namespace App;

use App\User;
use App\Notifications\ThreadWasCreated;
use Illuminate\Database\Eloquent\Model;

class ThreadSubscribtion extends Model
{
	protected $guarded = [];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function notify($reply)
	{
		$this->user->notify(new ThreadWasCreated($this, $reply));
	}
}
