@forelse ($threads as $thread)
    <article>
      <div class="panel panel-default">
        <div class="panel-heading">
                <div class="level">
                    <div class="flex">
                       <h5>
                           <a href="{{ $thread->path() }}">
                               @if (auth()->id() && $thread->HasUpdatesFor(auth()->id()))
                                   <h4><strong>{{ $thread->title }}</strong></h4>
                               @else 
                                   <h4>{{ $thread->title }}</h4>
                               @endif
                           </a>
                       </h5>                            
                       <h6>
                           Posted By: <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a>
                       </h6>
                   </div>                                       
                   <strong> <a href="{{ $thread->path() }}"> {{ $thread->replies_count }} {{ str_plural('Reply', $thread->replies_count ) }} </a></strong>
                </div>
        </div>
           <div class="panel-body">
                <p>{!! $thread->body !!}</p>
           </div>
            <div class="panel-footer">
                <strong>{{ $thread->getVisits() ?? 0 }}</strong> {{ str_plural('visit', $thread->getVisits() ) }}
            </div>
      </div>
    </article>
@empty
    <p class="alert alert-warning text-center">There is no relevent Thread for this channel yet!</p>
@endforelse

