@extends('layouts.app')

@section('header')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')
    <div class="container">
        @component('threads.components.panel')
            @slot('title')
                Create A Thread:
            @endslot
            <form action="/threads" method="POST">
                {{ csrf_field() }}
                @include('threads.layouts.errors')
                <div class="form-group">
                  <label for="channel_id">Channel Name:</label>
                  <select name="channel_id" id="" class="form-control">
                    <option value="">Choose One...</option>
                    @foreach ($channels as $channel)
                      <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                    @endforeach
                  </select>
                </div>
                  <div class="form-group">
                       <label for="title">Title:</label>
                       <input type="text" class="form-control" id="title" name="title" placeholder="Enter the title" value=" {{ old('title') }}">
                   </div> 
                <div class="form-group">
                    <label for="body">Body:</label>
                    <wysiwyg name="body"></wysiwyg>
                    {{-- <textarea class="form-control" id="body" name="body" rows="3" value="{{ old('body') }}"></textarea> --}}
                </div>

                <div class="form-group">
                  <div class="g-recaptcha" data-sitekey="6LfYx2gUAAAAAD_bYD6WDdmKQqC1RlCyIv3qfwsP"></div>
                </div>
                <button type="submit" class="btn btn-primary">Publish</button>
            </form>
        @endcomponent
    </div>
@endsection
