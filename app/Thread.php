<?php

namespace App;
 use App\ThreadSubscribtion;
use App\Filters\ThreadFilters;
use App\Events\ThreadHasNewReply;
use Illuminate\Support\Facades\Redis;
use App\Notifications\ThreadWasCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Thread extends Model
{
    use RecordsActivity, RecordVisits;
    
    protected $guarded = [];
    protected $with = ['creator', 'channel'];
    protected $appends = ['is_subscribed_to'];
    protected $casts = ['locked'    =>  'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($thread) {
            $thread->replies->each->delete();
        });

        static::created(function($thread) {
            $thread->update(['slug' =>  $thread->title ]);
        });
    }
    public function path()
    {
    	return "/threads/{$this->channel->slug}/{$this->slug}";
    }
    public function replies()
    {
    	return $this->hasMany(Reply::class);
    }
    public function creator()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }
    public function addReply($reply)
    {
        // return $this->replies()->create($reply);
        $reply = $this->replies()->create($reply);
        
        event(new ThreadHasNewReply($this, $reply));
        
        return $reply;
    }
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
    public function scopeFilter($query, ThreadFilters $filters)
    {
        return $filters->apply($query);
    }
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id'   =>  $userId ?: auth()->id()
        ]);
        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
             ->where('user_id', $userId ?: auth()->id())
             ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscribtion::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                      ->where('user_id', auth()->id())
                      ->exists();
    }

    public function HasUpdatesFor($user)
    {
        $key = sprintf("users.%s.visits.%s", auth()->id(), $this->id);

        return $this->updated_at > cache($key);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        if(static::where('slug', $slug)->exists()) {
            $slug = "{$slug}-" . $this->id;
        }
        $this->attributes['slug'] = $slug;
    }

    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id'  =>  $reply->id]);
    }

    public function lock()
    {
        $this->update(['locked' =>  true]);
    }

    public function unlock()
    {
        $this->update(['locked'  =>  false]);
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }
}
