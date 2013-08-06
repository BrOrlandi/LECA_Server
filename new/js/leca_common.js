function get_loading_html(message){
	if(typeof message !== 'undefined')
		return '<div class="carregando"><img src="img/loading.gif" /><h3>'+message+'</h3></div>';
	else
		return '<div class="carregando"><img src="img/loading.gif" /><h3>Carregando...</h3></div>';
}

function fix_accordion(){
    $('.accordion').on('show', function (e) {
         $(e.target).prev('.accordion-heading').find('.accordion-toggle').addClass('active-accordion-leca');
    });
    
    $('.accordion').on('hide', function (e) {
        $(this).find('.accordion-toggle').not($(e.target)).removeClass('active-accordion-leca');
    });
}
