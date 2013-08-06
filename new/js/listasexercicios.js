
//global
var listas;

$(document).ready(function (){
	
//-------------------------- submissão de nova lista de exercícios.
reset_submit();


$('#submit_button').click(function() {
	var html_content = $('#modal_content').html();
	$('#form_lista_exercicios').ajaxSubmit({
		beforeSubmit: function(){
			var new_html = get_loading_html('Enviando Listas de Exercícios...');
			$('#modal_content').html(new_html);
		},
		success: function(data,status){
			$('#modal_content').html(html_content);
			reset_submit();
			var m = JSON.parse(data);
			if(m.status == 0){
				$('#message_return').html('<h3>Lista de Exercícios enviada com sucesso!</h3>');
				get_listas();
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

});

function get_listas(){
	$.ajax({
		type: 'GET',
		url: "./php/get.php",
		data: {'get':'get_listas'},
		beforeSend: function(){
			$('#listas_header').nextAll().remove();
			$('#listas_header').after(get_loading_html('Carregando Listas de Exercícios...'));
		},
		error: function(jqxhr,status,error){
			$('#listas_header').nextAll().remove();
			$('#listas_header').after(status+'\n'+error);
		},
		success: function(data,status){
			$('#listas_header').nextAll().remove();
			var json = JSON.parse(data);
			if(json.status != 0){
				$('#listas_header').after('Erro: '+json.status+'\n'+json.message);
			}
			else
			{
				listas = json.obj;
				var size = listas.length;
				var str = '';
				for(var i=0;i<size;i++){
					str += '<li><a class="lista_select" data-lista="'+ i +'" href="#tab2" data-toggle="tab">'+listas[i].titulo+'</a></li>';
				}
				$('#listas_header').after(str);
				$('.lista_select').click(function(){clicou_lista($(this))});
			}
		}
	});
}

function clicou_lista(obj){
	if(!obj.parent().hasClass('active'))
	{
		var i = obj.attr('data-lista');
		$('#lista_titulo').text(listas[i].titulo);
		$('#lista_tema').text(listas[i].tema);
		$('#lista_autor').text(listas[i].autor);
		$('#lista_data').text(listas[i].data);
		$('#lista_descricao').text(listas[i].descricao);
		$('#lista_permuta').text(listas[i].permuta);
		$('#lista_avaliacao').text(listas[i].avaliacao);
		
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
					else{
						listas[i].exercicios = json.obj;
						atualiza_exercicios(i);	
					}					
				}
			});
		}
		else{
			atualiza_exercicios(i);
		}
	}
}

function atualiza_exercicios(i_lista){
	var str = '';
	
	var exercicios = listas[i_lista].exercicios;
	var size = exercicios.length;
	str += '<div class="accordion" id="accordion_exercicios">';
	for(var i=0;i<size;i++){
		str += '<div class="accordion-group"><div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_exercicios" href="#collapse-'+ i +'">'+ (i+1)+') ' + exercicios[i].titulo +'</a></div>';
		str += '<div id="collapse-'+ i +'" class="accordion-body collapse" data-lista="'+ i_lista +'" data-exercicio="'+ i +'"><div class="accordion-inner">';
		
		str += '<div class="row-fluid"><div class="span8">';
		str += '<p>	<b>Tema: </b>'+ exercicios[i].tema +'<br>' + 
					'<b>Autor: </b>'+ exercicios[i].autor +'<br>' + 
					'<b>Permutar Alternativas: </b>'+ exercicios[i].permuta +'<br>' + 
				'</p>';
		str += '</div>';
		str += '<div class="span2">';
		str += '<a href="./resultados.html?get=1" class="btn btn-primary" data-lista="'+ i_lista +'" data-exercicio="'+ i +'" type="button">Resultados</a>';
		str += '</div></div>';
		str += '<b>Enunciado:</b><p>'+ exercicios[i].enunciado +'</p>';
		str += '<div id="exercicio_alternativas'+i+'"></div>';
		str += '</div></div></div>';
	}
	str += '</div>';
	$('#lista_exercicios').html(str);
	fix_accordion();
	$('.accordion-body').on('show',function(e){clicou_exercicio(e);});
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
				$(select).html(get_loading_html('Carregando Exercícios...'));
			},
			error: function(jqxhr,status,error){
				$(select).html(status+'\n'+error);
			},
			success: function(data,status){
				$(select).html('');
				var json = JSON.parse(data);
				if(json.status != 0){
					$(select).html('Erro: '+json.status+'\n'+json.message);
				}
				else{
					listas[lista].exercicios[ex].alternativas = json.obj;
					atualizar_alternativas(lista,ex);
				}
			}
		});
	}
	else{
		atualizar_alternativas(lista,ex);
	}
	
}

function atualizar_alternativas(i_lista,i_exercicio){
	var select = '#exercicio_alternativas'+i_exercicio;
	
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
	$('#input_lista_exercicios').bind('change',function () {
		$('#submit_button').removeAttr('disabled');
	});
	$('#form_lista_exercicios').resetForm();
	$('#submit_button').attr('disabled','disabled');
	$('#message_return').html('');
	$('#ok_button').hide();
}
