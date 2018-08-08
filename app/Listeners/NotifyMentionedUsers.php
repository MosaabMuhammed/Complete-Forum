<?php

namespace App\Listeners;

use App\User;
use App\Events\ThreadHasNewReply;
use App\Notifications\YouWereMentioned;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadHasNewReply  $event
     * @return void
     */
    public function handle(ThreadHasNewReply $event)
    {
        // User::whereIn('name', $event->reply->mentionedUsers())
        //     ->get()
        collect($event->reply->mentionedUsers())
            ->map(function($name) {
                return User::where('name', $name)->first();
            })
            ->filter()
            ->each(function($user) use ($event) {
                $user->notify(new YouWereMentioned($event->reply));
            });
        //////////// the previous login can be performed as below for simplicity //////////
        // $mentionedUsers = $event->reply->mentionedUsers();
        // foreach ($mentionedUsers as $name) {
        //     if($user = User::where('name', $name)->first()) {
        //         $user->notify(new YouWereMentioned($event->reply));
        //     }
        // }
        //
    }
}
