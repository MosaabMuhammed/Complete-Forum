<template>
	<div class="panel" :class="isBest ? 'panel-success' : 'panel-default'">
		<div class="panel-heading" :id="'reply-' + this.reply.id">
			<div class="level">	
				<h4 class="flex">
					<a :href="'/profile/'+ this.reply.owner.name" v-text="this.reply.owner.name"></a>
					said:
					<span v-text="ago"></span>
				</h4>
			<div> 
				<div v-if="signIn">
					<favorite :reply="reply" ></favorite>
				</div>
			</div>
		</div>
		</div>

		<div class="panel-body">
			<div v-if="editting">
				<form @submit="update">
					<div class="form-group">
						<wysiwyg v-model="body"></wysiwyg>
						<br>
						<button type="submit" class="btn btn-primary btn-xs">Update</button>
						<button type="button" class="btn btn-link btn-xs" @click="editting = false">Cancel</button>
					</div>
				</form>			
			</div>
			<span v-html="body" v-if="! editting"></span>
		</div>
		<div  class="level panel-footer">
			<div v-if="authorize('owns', reply)">
				<button type="submit" class="btn btn-success btn-xs" @click="editting = true" style="margin-right: 20px">Edit</button>
				<button type="submit" class="btn btn-danger btn-xs" @click="destroy">Delete</button>
			</div>
			<button type="submit" class="btn btn-default btn-xs" style="margin-left: auto" @click="markBest" v-if="authorize('owns', reply.thread) && ! isBest">Best Reply ?</button>
		</div>
	</div>
</template>
<script>
	import Favorite from './Favorite.vue';
	import moment from 'moment';
	export default {
		props: ['data'],
		components: {Favorite }, 
		data() {
			return {
				editting: false, 
				body: this.data.body, 
				reply: this.data, 
				isBest: this.data.isBest
			}
		},
		computed: {
			ago() {
				return moment(this.data.created_at).fromNow();
			}
		},
		created() {
			window.events.$on('best-reply-selected', id => {
				console.log()
				this.isBest = (id === this.reply.id);
			});
		},
		methods: {
			update() {
				axios.patch('/replies/' + this.data.id , { body : this.body })
					.then( response => {
						this.editting = false;
						// this.body = this.$props.data.body;
						flash('reply updated!', 'success');
					})
					.catch(error => {
						flash(error.response.data, 'danger');
					});
			},
			destroy() {
				this.$emit('deleted');
				axios.delete('/replies/' + this.data.id);
			}, 
			markBest() {
				axios.post('/replies/' + this.reply.id + '/best');
				window.events.$emit('best-reply-selected', this.reply.id);
			}
		}
	}
</script>
