{{-- Normal Mode of the thread --}}
<div class="panel panel-default" v-if="! editing">
	<div class="panel-heading"> <div class="level">
            <div class="flex">
                <img src="{{ $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="35" height="40" class="img-rounded" style="margin-right: 10px">
                <strong><a href="/profile/{{ $thread->creator->name }}">{{ $thread->creator->name }} ({{ $thread->creator->reputation }}XP)</a></strong> Wrote:
                <h3 v-text="form.title"></h3>
            </div>
        </div>
    </div>
    <div class="panel-body">
    	<span v-html="form.body"></span>
    </div>
    <div class="panel-footer" v-if="authorize('owns', thread)">
    	<button @click="editing = true" class="btn btn-xs btn-success">Edit</button>
    </div>
</div>

 {{-- Editing the Thread --}}
<div class="panel panel-default" v-else>
	<div class="panel-heading">
        <div class="level">
            <div class="flex">
				<div>
					<img src="{{ $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="35" height="40" class="img-rounded" style="margin-right: 10px">
					<strong><a href="/profile/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a></strong> Wrote:
				</div>
                <br>
            	<div class="form-group">
            		<input type="text" class="form-control" id="title" name="title" v-model="form.title">
            	</div>
            </div>
        </div>
    </div>
    <div class="panel-body">
    	<div class="form-group">
            <wysiwyg v-model="form.body" :value="form.body"></wysiwyg>
    		{{-- <textarea class="form-control" id="body" name="body" rows="3" v-model="form.body"></textarea> --}}
    	</div>
    </div>
    <div class="panel-footer level">
		<div class="flex">
        	<button @click="update" class="btn btn-xs btn-primary">Update</button>
        	<button @click="resetForm" class="btn btn-xs btn-link">Cancel</button>
        </div>        
        <div>
            @can('update', $thread)
                <form action="{{ $thread->path() }}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
            @endcan
        </div>
    </div>
</div>
