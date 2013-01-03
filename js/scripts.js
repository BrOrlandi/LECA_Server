$(document).ready(function (){
	
$('input[type="file"]').change(function () {
	$(this).next().removeAttr('disabled');
}).next().attr('disabled','disabled');
	
});
