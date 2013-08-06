
//global
var listas;

$(document).ready(function (){
	
//-------------------------- submissão de nova lista de exercícios.
reset_submit();


$('#submit_button').click(function() {
	var html_content = $('#modal_content').html();
	$('#form_resultado').ajaxSubmit({
		beforeSubmit: function(){
			var new_html = get_loading_html();
			$('#modal_content').html(new_html);
		},
		success: function(data,status){
			$('#modal_content').html(html_content);
			reset_submit();
			var m = JSON.parse(data);
			if(m.status == 0){
				$('#message_return').html('<h3>Avaliação enviada com sucesso!</h3>');
			}
			else
			{
				$('#message_return').html('Erro '+m.status +': '+m.message);
			}
			$('#ok_button').show();
		}		
	});
});

$('#cancel_submit_button').click(function() {
	reset_submit();
});
$('#ok_button').click(function() {
	reset_submit();
});

//----------------------------------------------------------

get_listas();

$('#ver_resultados').click(function(e){
	e.preventDefault();
	var i = $('.lista.accordion-body.collapse.in').parent().index();
	resultados(i);
});



});

function get_submissoes(leid){
	$.ajax({
		type: 'GET',
		url: "./php/get.php",
		data: {'get':'get_submissoes', 'lista':leid},
		beforeSend: function(){
			$('#n_submissoes').html('...');
		},
		error: function(jqxhr,status,error){
			$('#n_submissoes').html('Erro ao carregar submissoes.<br>'+status+' : '+error);
		},
		success: function(data,status){
			var json = JSON.parse(data);
			if(json.status != 0){
				$('#n_submissoes').html('Erro: '+json.status+' : '+json.message);
			}else{
				$('#n_submissoes').html(json.obj.submissoes);
			}
		}
	});
}

function get_listas(){
	$.ajax({
	type: 'GET',
	url: "./php/get.php",
	data: {'get':'get_listas'},
	beforeSend: function(){
		$('#listas').html(get_loading_html('Carregando Listas de Exercícios...'));
	},
	error: function(jqxhr,status,error){
		$('#listas').html(status+'\n'+error);
	},
	success: function(data,status){
		var json = JSON.parse(data);
		if(json.status != 0){
			$('#listas').html('Erro: '+json.status+'\n'+json.message);
		}
		else
		{
			listas = json.obj;
			var size = listas.length;
			var str = '<div class="accordion" id="accordion1">';
			for(var i=0;i<size;i++){
				str += '<div class="accordion-group"><div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-lista="'+i+'" data-parent="#accordion1" href="#collapse1-'+ i +'">';
	    		str += listas[i].titulo + '</a></div>';
	    		str += '<div id="collapse1-'+ i +'" class="lista accordion-body collapse" data><div class="accordion-inner">';
				str += '<h3>'+listas[i].titulo+'</h3><div class="row-fluid"><div class="span8">';
	
				str += '<p>	<b>Tema: </b>'+listas[i].tema+'<br>';
				str += '<b>Autor: </b>'+listas[i].autor+'<br>';
				str += '<b>Data: </b>'+listas[i].data+'<br>';
				str += '<b>Descrição: </b>'+listas[i].descricao+'<br>';
				str += '<b>Permutar Exercícios: </b>'+listas[i].permuta+'<br>';
				str += '<b>Tipo de Avaliação: </b>'+listas[i].avaliacao+'<br> </p></div>';
				str += '<div class="span4">';
				str += '<a class="btn btn-large btn-primary" type="button" onclick="ver_resultados()">Resultados</a><br><br>';
				str += '<a href="#submeter" data-toggle="modal" class="btn btn-large btn-primary" type="button">Submeter Avaliação</a>';
				str += '</div></div></div></div></div>';
			}
			str += '</div>';
			$('#listas').html(str);
			fix_accordion();
		}
	}
});

}

function ver_resultados(){
	$('.tabbable a[href="#tab2"]').tab('show');
	var i = $('.lista.accordion-body.collapse.in').parent().index();
	resultados(i);
}

function resultados(i){
	if(i == -1){
		$('#tab2').html('<h3>Selecione uma <a id="temp-link" href="#">Lista de Exercícios</a> na aba anterior.</h3>');
		$('#temp-link').click(function(e) {
			e.preventDefault();
			$('.tabbable a[href="#tab1"]').tab('show');
		});
	}
	else{
		var str = '';
		str += '<h3>'+listas[i].titulo+'</h3><div class="row-fluid"><div class="span5">';
		str += '<p>	<b>Tema: </b>'+listas[i].tema+'<br>';
		str += '<b>Autor: </b>'+listas[i].autor+'<br>';
		str += '<b>Data: </b>'+listas[i].data+'<br>';
		str += '<b>Descrição: </b>'+listas[i].descricao+'<br>';
		str += '<b>Permutar Exercícios: </b>'+listas[i].permuta+'<br>';
		str += '<b>Tipo de Avaliação: </b>'+listas[i].avaliacao+'<br> </p></div>';
		str += '<div class="span7">';
		str += '<b>Avaliações Submetidas: </b><span id="n_submissoes"></span><br><br><br>';
		str += '<a href="#submeter" data-toggle="modal" class="btn btn-large btn-primary" type="button">Submeter Avaliação</a>';
		str += '</div>';
		str += '</div>';
		str += '<h4>Exercícios:</h4><div id="lista_exercicios"></div>';
		$('#tab2').html(str);	
		
		if(listas[i].exercicios == null){
			var leid = listas[i].leid;
			$.ajax({
				type: 'GET',
				url: "./php/get.php",
				data: {'get':'get_exercicios','lista': leid},
				beforeSend: function(){
					$('#lista_exercicios').html(get_loading_html('Carregando Exercícios...'));
				},
				error: function(jqxhr,status,error){
					$('#lista_exercicios').html(status+'\n'+error);
				},
				success: function(data,status){
					$('#lista_exercicios').html('');
					var json = JSON.parse(data);
					if(json.status != 0){
						$('#lista_exercicios').html('Erro: '+json.status+'\n'+json.message);
					}
					else
					{
						listas[i].exercicios = json.obj;
						atualiza_exercicios(i);	
					}
				}
			});
		}
		else{
			atualiza_exercicios(i);
		}
		
		get_submissoes(listas[i].leid);
		
	}
}

function atualiza_exercicios(i_lista){
	var str = '';	
	var exercicios = listas[i_lista].exercicios;
	var size = exercicios.length;
	str += '<div class="accordion" id="accordion_exercicios">';
	for(var i=0;i<size;i++){
		str += '<div class="accordion-group"><div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_exercicios" href="#collapse2-'+ i +'">'+ (i+1)+') ' + exercicios[i].titulo +'</a></div>';
		str += '<div id="collapse2-'+ i +'" class="exercicios accordion-body collapse" data-lista="'+ i_lista +'" data-exercicio="'+ i +'"><div class="accordion-inner">';
		str += '<h4>'+exercicios[i].titulo+'</h4>'
		str += '<div class="row-fluid"><div class="span5">';
		str += '<p>	<b>Tema: </b>'+ exercicios[i].tema +'<br>' + 
					'<b>Autor: </b>'+ exercicios[i].autor +'<br>' + 
					'<b>Permutar Alternativas: </b>'+ exercicios[i].permuta +'<br>' + 
				'</p>';
		str += '<b>Enunciado:</b><p>'+ exercicios[i].enunciado +'</p>';
		str += '<div id="exercicio_alternativas'+i+'"></div>';
		str += '</div>';
		str += '<div id="grafico_'+i+'" class="span7">';
		str += '<img src="http://www.statmethods.net/graphs/images/pie2.jpg">';
		str += '</div></div>';
		str += '</div></div></div>';
	}
	str += '</div>';
	$('#lista_exercicios').html(str);
	fix_accordion();
	$('.exercicios.accordion-body').on('show',function(e){clicou_exercicio(e);});
}

function clicou_exercicio(e){
	var lista = $(e.target).attr('data-lista');
	var ex = $(e.target).attr('data-exercicio');
	
	if(listas[lista].exercicios[ex].alternativas == null){
		var select = '#exercicio_alternativas'+ex;
		var eid = listas[lista].exercicios[ex].eid;
		$.ajax({
			type: 'GET',
			url: "./php/get.php",
			data: {'get':'get_alternativas','exercicio':eid},
			beforeSend: function(){
				$(select).hide();
				$(select).after(get_loading_html('Carregando Exercícios...'));
			},
			error: function(jqxhr,status,error){
				$(select).next().remove();
				$(select).show();
				alert(status+'\n'+error);
			},
			success: function(data,status){
				$(select).next().remove();
				$(select).show();
				var json = JSON.parse(data);
				if(json.status != 0){
					alert('Erro: '+json.status+'\n'+json.message);
				}
				listas[lista].exercicios[ex].alternativas = json.obj;
				atualizar_alternativas(lista,ex);
			}
		});
	}
	else{
		atualizar_alternativas(lista,ex);
	}
	
}

function atualizar_alternativas(i_lista,i_exercicio){
	var select = '#exercicio_alternativas'+i_exercicio;
	
	$(select).html('');
	var str = '';
	
	var correta = listas[i_lista].exercicios[i_exercicio].correta;
	var alternativas = listas[i_lista].exercicios[i_exercicio].alternativas;
	var size = alternativas.length;
	for(var i=0;i<size;i++){
		if(correta == alternativas[i].id)
			str += '<p class="breadcrumb alternativa-correta">'+alternativas[i].texto+'</p>';
		else
			str += '<p class="breadcrumb">'+alternativas[i].texto+'</p>';
	}
	$(select).html(str);
}

function reset_submit(){
	$('#input_resultado').bind('change',function () {
		$('#submit_button').removeAttr('disabled');
	});
	$('#form_resultado').resetForm();
	$('#submit_button').attr('disabled','disabled');
	$('#message_return').html('');
	$('#ok_button').hide();
}
