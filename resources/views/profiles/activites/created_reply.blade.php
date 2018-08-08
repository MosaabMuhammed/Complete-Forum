@component('threads.components.panel')
    @slot('title')
		<div class="level">
			<div class="flex">
				{{ $profileUser->name}} <strong>replied</strong> on a thread "<a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a>"
			</div>
			<div>
				Since {{ $activity->created_at->diffForHumans() }}
			</div>
		</div>
	@endslot
	{!! $activity->subject->body !!}
@endcomponent

