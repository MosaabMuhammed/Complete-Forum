<template>
	<li class="dropdown navbar-btn" v-show="notifications.length">
		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<span class="glyphicon glyphicon-bell"></span>
		<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
			<li><a :href="notification.data.link" v-for="notification in notifications" @click="markAsRead(notification)">{{ notification.data.message }}</a></li>
		</ul>
	</li>
</template>

<script>
	export default {
		data() {
			return {
				notifications: false,
			}
		}, 
		created() {
			axios.get("/profiles/" + window.App.user.name + "/notifications")
				 .then( response => {
				 	this.notifications = response.data;
				 });
		}, 
		methods: {
			markAsRead(notification) {
				axios.delete("/profiles/" + window.App.user.name +"/notifications/" + notification.id);
			}
		}
	}
</script>
