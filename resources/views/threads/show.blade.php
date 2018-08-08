@extends('layouts.app')

@section('header')
    <link href="/css/vendor/jquery.atwho.css" rel="stylesheet">
@endsection
@section('content')
<thread :thread="{{ $thread }}" inline-template v-cloak>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                @include('threads.layouts._question')
                
                <replies @removed="initial_replies_count--" @added="initial_replies_count++"></replies>
                {{-- <div class="center-block" style="width:200px;">{{ $replies->links() }}</div> --}}
        
            </div>
            <div class="col-md-4">
                @component('threads.components.panel')
                    @slot('title')
                        <h4>{{ $thread->title }}</h4>
                    @endslot
                    This Thread was published <strong>{{ $thread->created_at->diffForHumans() }}</strong>. <br>
                    And Created by <a href="/profile/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a>. <br>
                    And It has <strong>@{{ this.initial_replies_count }}</strong> {{ str_plural('comment', $thread->replies_count) }}.
                    <div>
                        <subscribe-button style="margin-top: 16px; display: inline-block" :active="{{ json_encode($thread->is_subscribed_to) }}" v-show="signIn"></subscribe-button>
                        <button :class="lockClass" @click="toggleLock" v-show="authorize('isAdmin')" v-text="locked ? 'Unlock' : 'Lock'" ></button>
                    </div>          
              @endcomponent
        </div>
    </div>
</div>
</thread>
@endsection

