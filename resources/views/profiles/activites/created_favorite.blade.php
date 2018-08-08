
@component('threads.components.panel')
    @slot('title')
		<div class="level">
			<div class="flex">
				<a href="{{ $activity->subject->favorited->path() }}">
					{{ $profileUser->name}} 
					Favoriated a Reply
				</a>
			</div>
			<div>
				Since {{ $activity->created_at->diffForHumans() }}
			</div>
		</div>
	@endslot
	{!! $activity->subject->favorited->body !!}
@endcomponent
