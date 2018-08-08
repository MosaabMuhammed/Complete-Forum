<?php

namespace App\Policies;

use App\User;
use App\Reply;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Reply $reply)
    {
        return $user->id == $reply->user_id;
    }

    public function create(User $user)
    {
    	// if he doesn't have any replies yet, just return true
    	if(! $lastReply = $user->fresh()->lastReply) {
    		return true;
    	}
    	// 
    	return ! $lastReply->wasJustPublished();
    }
}
