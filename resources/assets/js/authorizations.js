let user = window.App.user;

module.exports = {
	// updateReply(reply) {
	// 	return reply.user_id === user.id;
	// }
	owns(model, props = 'user_id') {
		return model[props] === user.id;
	},
	isAdmin() {
		return ['JohnDoe', 'Mosaab Muhammed'].includes(user.name);
	}
};
