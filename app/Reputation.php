<?php

namespace App;

class Reputation
{
	const THREAD_WAS_PUBLISHED   = 10;
	const REPLAY_WAS_ADDED	   	 = 2;
	const REPLAY_WAS_MARKED_BEST = 50;
	const REPLAY_WAS_FAVORITED	 = 5;

	public static function award($user, $points)
	{
		$user->increment('reputation', $points);
	}

	public static function reduce($user, $points)
	{
		$user->decrement('reputation', $points);
	}
}