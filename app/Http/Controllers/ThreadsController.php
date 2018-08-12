<?php

namespace App\Http\Controllers;

use App\User;
use Zttp\Zttp;
use App\Thread;
use App\Channel;
use App\Trending;
use Carbon\Carbon;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use App\Filters\ThreadFilters;

class ThreadsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Channel      $channel
     * @param ThreadFilters $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filters, Trending $trending)
    {

        $threads = $this->getThreads($channel, $filters);
        if(request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', [
            'threads'   =>  $threads,
            'trending'  =>  $trending->get(), 
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**DB::delete('delete users where name = ?', ['John'])
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Recaptcha $recaptcha)
    {
        $this->validate(request(), [
            'title' => 'required|spamfree',
            'body'  => 'required|spamfree', 
            'channel_id' => 'required|exists:channels,id',
        ]);

        (new \App\Inecpections\Spam)->detect(request('body'));

        $thread = Thread::create([
            'user_id'       => auth()->id(),
            'channel_id'    => request('channel_id'),
            'title'         => request('title'), 
            'body'          => request('body'), 
        ]);
        return redirect($thread->path())
                ->with("flash", "You Thread Has Been Published!");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channel_id, Thread $thread, Trending $trending)
    {
        $key = sprintf("users.%s.visits.%s", auth()->id(), $thread->id);

        cache()->forever($key, Carbon::now());

        $trending->push($thread);

        $thread->recordVisit();

        return view('threads.show', compact('thread'));
    }

    public function update($channel, Thread $thread)
    {
        // authorization
        $this->authorize('update', $thread);
        
        // Validation and Updating 
        $thread->update(request()->validate([
            'title' =>  'required|spamfree', 
            'body' =>  'required|spamfree', 
        ]));

        return $thread;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel, Thread $thread)
    {
        $this->authorize('update', $thread);
        
        $thread->delete();

        if(request()->wantsJson()) {
            return response([], 204);
        }
        return redirect('/threads');
    }

    /**
     * Fetch all relevant threads.
     *
     * @param Channel       $channel
     * @param ThreadFilters $filters
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::filter($filters)->latest();
        // dd(Thread::filter($filters)->get());
        if ($channel->exists) {
            // dd($channel->id);
            $threads->where('channel_id', $channel->id);
        }
        // dd($threads->get());
        return $threads->paginate(20);
    }
}
