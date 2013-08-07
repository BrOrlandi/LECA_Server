<?php

	require_once('common.php');
	
	if(isset($_GET['get'])){
		$get = $_GET['get'];
		switch ($get) {
			case 'get_listas':
				echo get_listas($db);
			break;
			
			case 'get_exercicios':
				if(!isset($_GET['lista'])){
					retorna(55,'Lista não especificada');
				}
				$leid = $_GET['lista'];
				get_exercicios($db,$leid);
			break;
			
			case 'get_alternativas':
				if(!isset($_GET['exercicio'])){
					retorna(56,'Exercício não especificada');
				}
				$eid = $_GET['exercicio'];
				get_alternativas($db,$eid);
			break;
			
			case 'get_submissoes':
				if(!isset($_GET['lista'])){
					retorna(57,'Lista não especificada');
				}
				$leid = $_GET['lista'];
				get_submissoes($db,$leid);
			break;
			
			case 'get_assinaladas_por_exercicio':
				if(!isset($_GET['lista'])){
					retorna(58,'Lista não especificada');
				}
				$leid = $_GET['lista'];
				if(!isset($_GET['exercicio'])){
					retorna(59,'Exercício não especificada');
				}
				$eid = $_GET['exercicio'];
				get_assinaladas_por_exercicio($db,$leid,$eid);
			break;
			
			default:
				retorna(54,'Requisição desconhecida!');
			break;
		}
	}
