<?php

namespace App\Http\Controllers;

use App\User;
use App\Reply;
use App\Thread;
use App\Inecpections\Spam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Notifications\YouWereMentioned;
use App\Http\Requests\CreatePostRequest;

class RepliesController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth', ['except' => 'index']);
	}

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(5);
    }
    public function store($channel_id, Thread $thread, CreatePostRequest $form)
    {
        //  ::: replaced by the createPostRequest ::: 
        // if(Gate::denies('create', new Reply)) {
        //     return response(
        //         'You are posting too frequently. Please take a break. :)', 429
        //     );
        // }
        // $form->persist($thread);
        // if(request()->expectsJson()) {
        //     return $reply->load('owner');
        // }
    	// return back()->with('flash', "Your reply has been saved");
        if($thread->locked) {
            return response("You can't add any reply", 422);
        }
        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id(),
        ]);

        return $reply->load('owner');
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);
        
        $reply->delete();
    }
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        try {
            $this->validate(request(), [
                'body' => 'spamfree'
            ]);
            
            $reply->update(request(['body']));
        } catch(\Exception $e) {
            return response(
                'Sorry You can\'t Update the reply with that content!', 422
        );
        }
    }
}
