<?php 

namespace App;

use App\Reputation;
use Illuminate\Database\Eloquent\Model;

trait favoriable
{
	public function favorites()
	{
		return $this->morphMany(Favorite::class, 'favorited');
	}

	public function favorite()
	{
		$attributes = ['user_id' => auth()->id() ];
		if(! $this->favorites()->where($attributes)->exists()) {
			Reputation::award($this->owner, Reputation::REPLAY_WAS_FAVORITED);
			return $this->favorites()->create($attributes);
		}
	}
	public function isFavorited()
	{
		return !! $this->favorites->where('user_id', auth()->id())->count();
	}	
	public function getFavoritesCountAttribute()
	{
		return $this->favorites->count();
	}
	public function unfavorite()
	{
		$attributes = ['user_id' => auth()->id() ];
		$this->favorites()->where($attributes)->get()->each->delete();
		Reputation::reduce(auth()->user(), Reputation::REPLAY_WAS_MARKED_BEST );
	}
	public function getIsFavoritedAttribute()
	{
		return $this->isFavorited();
	}
}
