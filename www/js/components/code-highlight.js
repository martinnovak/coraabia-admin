$(function () {
	var self = this;
	
	self.wrapHash = function () {
		var children = $('.wrapped').contents();
		if (children.length) {
			$(children[0]).unwrap();
		}
		var hash = window.location.hash;
		if (hash) {
			$(hash).wrap('<code class="wrapped" />')
		}
	};
	
	self.wrapHash();
	$(window).on('hashchange', function() {
		self.wrapHash();
	});
});
