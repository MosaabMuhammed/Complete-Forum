<?php 

namespace App;

use Illuminate\Support\Facades\Redis;

trait RecordVisits
{
    public function recordVisit()
    {
        if(! Redis::get($this->visitsCacheKey())) {
            Redis::incrby($this->visitsCacheKey(), 1);
        }
        return $this;
    }

    public function getVisits()
    {
        return Redis::eval("return #redis.call('keys', 'thread." . $this->id . ".visits.*')", 0);
    }

    public function visitsCacheKey()
    {
        return ("thread." . $this->id . ".visits." . auth()->id() );
    }
}
