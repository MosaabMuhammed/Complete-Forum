<template>
	<div>
		<div v-for="(reply, index) in items">
			<reply :data="reply" @deleted="remove(index)" :key="reply.id"></reply>
		</div>
		<paginator :dataSet="dataSet" @changed="fetch" class="center-block" style="width: 200px"></paginator>

		<p class="text-center alert alert-danger" v-if="$parent.locked">
			This Thread has been locked. No more replies!
		</p>
		<new-reply @created="add" :endpoint="endpoint" style="margin-top: 50px" v-else></new-reply>
	</div>
</template>
<script>
	import Reply from './Reply.vue';
	import NewReply from './NewReply.vue';
	export default {
		components: { Reply, NewReply }, 
		data() {
			return {
				items: [],
				dataSet: false,
				endpoint: location.pathname + '/replies',
			}
		},
		created() {
			this.fetch();
		},
		methods: {
			fetch(page) {
				axios.get(this.url(page))
					 .then(this.refresh);
			},
			url(page) {
				if(! page) {
					let query = location.search.match(/page=(\d+)/);
					page = query ? query[1] : 1;
				}
				return `${location.pathname}/replies?page=${page}`;
			},
			refresh({data}) {
				this.dataSet = data;
				this.items = data.data;
			},
			add (item) {
				this.items.push(item);
				this.$emit('added');
			},
			remove(index) {
				this.items.splice(index, 1);
				this.$emit('removed');
				flash('Reply was deleted');
			}	
		}
	}
</script>
