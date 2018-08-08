@extends('layouts.app')

@section('content')
<div class="container">
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="page-header">
		<div class="level">
			<div class="flex">
				<avatar-form :user="{{ $profileUser }}"></avatar-form>
			</div>			
			<div>
				Since <small>{{ $profileUser->created_at->diffForHumans() }}</small>
			</div>	
		</div>
		</div>
		<div>
			@foreach ($activities as $date => $activity)
				<div class="page-header">
					{{ $date }}
				</div>
				@foreach ($activity as $record)
					@if (view()->exists("profiles.activites.{$record->type}"))
						@include("profiles.activites.{$record->type}", ['activity'  => $record ])
					@endif
				@endforeach
			@endforeach
		</div>
	</div>
</div></div>
@endsection
