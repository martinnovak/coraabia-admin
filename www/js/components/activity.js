$(function () {
	
	var Model = function (gamerooms) {
		var self = this;
		
		self.gamerooms = ko.observableArray(gamerooms);
		self.activityGamerooms = ko.observableArray([]);
		
	};
	
	ko.applyBindings(new Model(jsCodes.gamerooms));
});