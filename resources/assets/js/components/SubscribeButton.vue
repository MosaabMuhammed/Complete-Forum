<template>
	<div>
		<button  :class="classes" @click="Subscribe">{{ value }}</button>
	</div>
</template>

<script>
	export default {
		props: ['active'],
		data() {
			return {
				value: this.active ? 'Unsubscribe' : 'Subscribe',
			}
		},
		computed: {
			classes() {
				return ['btn', this.active ? 'btn-primary' : 'btn-default'];
			}
		},
		methods: {
			Subscribe() {
				let requestType = this.active ? 'delete' : 'post';
				axios[(requestType)](location.pathname + '/subscriptions');
				this.active = ! this.active;
				this.value = this.active ? 'Unsubscribe' : 'Subscribe';
				flash(`Thread ${this.active ? 'Subscribe' : 'Unsubscribe'}d! Successfully!`);
			}
		}

	}
</script>
