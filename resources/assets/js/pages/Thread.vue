<script>
	import replies from '../components/replies.vue';
	import SubscribeButton from '../components/SubscribeButton.vue';

	export default {
		props: ['thread'],
		components: { replies, SubscribeButton },

		data() {
			return {
				initial_replies_count: this.thread.replies_count,
				locked: this.thread.locked,
				form: {}, 
				editing: false,
			}
		}, 
		created () {
			this.resetForm();
		}, 
		computed:{
			signIn() {
				return window.App.signIn;
			},
			lockClass() {
				return ['btn', this.locked ? 'btn-primary' : 'btn-default'];
			},
		},
		methods: {
			toggleLock () {
				axios[ this.locked ? 'delete' : 'post' ]('/locked-thread/' + this.thread.slug)
				.then(response => {
					flash('Thread Locked Successfully!');
				});
				
				this.locked = ! this.locked;
			},
			update () {
				axios.patch('/threads/' + this.thread.channel.slug + '/' + this.thread.slug, this.form).then(() => {
					this.editing = false;
					this.thread.title = this.form.title;
					this.thread.body= this.form.body;
					flash('Your Thread has been Updated!');
				});
			},
			resetForm () {
				this.form = {
					title: this.thread.title,
					body: this.thread.body
				};
				this.editing = false;
			}
		}
	}
</script>
