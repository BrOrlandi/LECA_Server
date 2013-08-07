// Load the Visualization API and the piechart package.
google.load('visualization', '1', {'packages':['corechart']});


$(document).ready(function (){
	
$('input[type="file"]').change(function () {
	$(this).next().removeAttr('disabled');
}).next().attr('disabled','disabled');
	
	$('#uploadlist_notify').center();
	
$('.upload').iframePostForm({
	iframeID:'err',
	post : function(){
		$('#uploadlist_notify').html('<img src="img/loading.gif" /><p class="center">Enviando arquivo...</p>');
		$('#uploadlist_notify').slideDown();
	},
	complete: function(response){
		//alert('complete!');
		$('.upload').each(function(index) {
		  this.reset();
		  $('.upload > input[type="submit"]').attr('disabled','disabled');
		});
		$('#feedback').html(response);
		$('#charts').css('display','none');
		
		$('#uploadlist_notify').slideUp();
	}
});
	
	
var chart = new google.visualization.PieChart(document.getElementById('chart_draw'));
google.visualization.events.addListener(chart, 'error', function (id,message){alert(message);});
  
$('#chartsbutton').click(function(){
	$('#feedback').html('');
	$.ajax({
		type: 'POST',
		url: 'chartsdata.php',
		data: { nodata: "nodata"}
	}).done(function(html){
		$('#charts_info').html(html);
		$('#charts').css('display','inline');
		
		$('#select_question').change(function(){
			
			var jsondata = $.ajax({
				type: 'POST',
				url: 'chartsdata.php',
          		dataType:'json',
				async: false,
				data: { question: $('#select_question').val()}
			}).responseText;
			
			var json = $.parseJSON(jsondata);
			//$('#charts_info2').text(jsondata);
			
			// Set chart options
     		 var options = json.options;
				// Create our data table out of JSON data loaded from server.
		      var data = new google.visualization.DataTable(json.data);
		
		      // Instantiate and draw our chart, passing in some options.
		      chart.draw(data, options);
			
		});
	});
});



});


function move_center_horizontal(obj){
	var window_width = $(window).width();
	//var window_height = $(window).height();
	var obj_width = obj.width();
	//var obj_height = obj.height();
	
	var xpos = (window_width - obj_width)/2; 
	//var ypos = (window_height - obj_height)/2; 
	//obj.css('left',xpos).css('top',ypos);
	obj.css('left',xpos);
}

jQuery.fn.center = function () {
    this.css("position","fixed");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
    return this;
}