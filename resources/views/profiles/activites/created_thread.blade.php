
@component('threads.components.panel')
    @slot('title')
		<div class="level">
			<div class="flex">
				{{ $profileUser->name}} 
				published a <strong>thread</strong> "<a href="{{ $activity->subject->path() }}">
					{{ $activity->subject->title }}</a>"
			</div>
			<div>
				Since {{ $activity->created_at->diffForHumans() }}
			</div>
		</div>
	@endslot
	{{ $activity->subject->body }}
@endcomponent
