$(function () {

	$('.grido').grido({
        ajax: false
    });
	
	//register Grido to nette extensions
	/*$.nette.ext('grido',
	{
		load: function() {
			$('.grido').grido();
		},
		success: function(payload) {
			$('.grido').trigger('gridoAjaxSuccess', payload);
			//$('html, body').animate({ scrollTop: 0 }, 400); //scroll up after ajax update
		}
	});*/
	
	$('.ajax').removeClass('ajax');
	
	$.nette.init();
});