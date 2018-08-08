<template>
	<div>
		<div v-if="signIn">
			<div class="form-group">
				<wysiwyg v-model="body" name="body" placeholder="Have something to say?" ref="trix"></wysiwyg>
				<!-- <textarea class="form-control" id="body" rows="3" name="body" v-model="body"></textarea> -->
			</div>
				<button type="submit" class="btn btn-default" @click="addReply2">Submit</button>
		</div>
		<div v-else>
			<p class="text-center">You can <a href="/register">Sign In</a> to participate in the discussion.</p>
		</div>
	</div>
</template>
<script>
	import 'at.js';
	import 'jquery.caret';
	export default {
		props: ['endpoint'],
		data() {
			return {
				body: '',
			}
		},
		computed: {
			signIn() {
				return window.App.signIn;
			}
		},
		mounted() {
			$('#body').atwho({
				at: "@", 
				delay: 750,
				callbacks: {
					remoteFilter: function(query, callback) {
						$.getJSON("/api/users", {name: query}, function(usernames) {
							callback(usernames)
						});
					}
				}
			});
		}, 
		methods: {
			addReply2() {
				axios.post(this.endpoint, {
					body: this.body
				})
				.then(response => {
					flash('You Reply has been added!');

					this.$refs.trix.$refs.trix.value = '';

					this.$emit('created', response.data);
				})
				.catch(error => {
					this.body = '';
					flash(error.response.data, 'danger');
				});
			}
		}
	}
</script>
