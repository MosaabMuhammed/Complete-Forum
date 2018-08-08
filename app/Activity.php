<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
	protected $guarded = [];

	public function subject()
	{
		return $this->morphTo();
	}

	protected static function feed(User $user)
	{
		return 	$user
				->activity()
				->latest()
				->with('subject')
				->take(50)
				->get()
				->groupBy(function($activity) {
					return $activity->created_at->format('Y-m-d');
		});
				dd($user
				->activity()
				->latest()
				->with('subject')
				->take(50)
				->get()
				->groupBy(function($activity) {
					return $activity->created_at->format('Y-m-d');
		}));
	}
}
