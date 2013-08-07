

<?php
/*
<!-- <html>
	<head></head>
	<body>
		<form class="upload" enctype="multipart/form-data" action="uploadfiles.php" method="post">
			<input type="file" name="listaexerciciosfile" />
			<input type="submit" name="enviar" value="Enviar Lista"/>
		</form>
	</body>
</html> -->
 * */
	require_once('common.php');

	define("LISTA_EXERCICIOS", "../data_uploaded/ListaExerciciosTemp.xml");
	define("AVALIACAO", "../data_uploaded/AvaliaçãoTemp.xml");
	
	
	//verifica se tem arquivo de lista de exercicios enviado.
	if (isset($_FILES['listaexerciciosfile'])) {
		$lefile = $_FILES['listaexerciciosfile'];
        if($lefile['error'] == UPLOAD_ERR_NO_FILE) {
        	retorna(51,'Nenhum arquivo foi selecionado para enviar!');
		} else if ($lefile['error'] == UPLOAD_ERR_OK) {
			//salva arquivo no servidor
			$file = fopen(LISTA_EXERCICIOS, "w");
			fwrite($file, file_get_contents($lefile['tmp_name']));
			fclose($file);
			//le o arquivo como XML
			$sxeList = simplexml_load_file(LISTA_EXERCICIOS);
			if (!$sxeList)
        		retorna(52,'Erro ao interpretar o arquivo XML!');


			$json['titulo']= $sxeList['Titulo'] . "";
			$json['tema'] = $sxeList->Tema . "";
			$json['autor'] = $sxeList->Autor . "";
			$json['descricao'] = $sxeList->Obs . "";
			$json['idioma'] = $sxeList->Idioma . "";
			$json['data'] = $sxeList->Data . "";
			$json['permuta'] = $sxeList->Permutar . "";
			$json['avaliacao'] = $sxeList->Avaliacao . "";
			

			$i=0;
			//para cada questão encontrada
			foreach ($sxeList->Questao as $q) {
				$json['exercicios'][$i]['titulo'] = $q['Titulo'] . "";
				$json['exercicios'][$i]['autor'] = $q->Autor . "";
				$json['exercicios'][$i]['enunciado'] = $q->Enunciado . "";
				$json['exercicios'][$i]['tema'] = $q->Tema . "";
				$num = 0 + $q->Correta;
				$correta = decode_correta($q->Enunciado, $num);
				$json['exercicios'][$i]['correta'] = $correta . "";
				$json['exercicios'][$i]['permuta'] = $q->Permutar . "";
				
				$j=0;
				foreach ($q->Alternativa as $a) {
					$json['exercicios'][$i]['alternativas'][$j]['texto'] = $a . "";
					$j++;
				}		
				$i++;
			}
				
			$str = json_encode($json);
			insert_lista_exercicios($db,$str);
			retorna(0,'Sucesso!');
		}
	}

	if (isset($_FILES['resultadofile'])) {
		$resultadofile = $_FILES['resultadofile'];
        if($resultadofile['error'] == UPLOAD_ERR_NO_FILE) {
        	retorna(51,'Nenhum arquivo foi selecionado para enviar!');
		} else if ($resultadofile['error'] == UPLOAD_ERR_OK) {
			//salva arquivo no servidor
			$file = fopen(AVALIACAO, "w");
			fwrite($file, file_get_contents($resultadofile['tmp_name']));
			fclose($file);
			//le o arquivo como XML
			$sxe = simplexml_load_file(AVALIACAO);
			if (!$sxe)
        		retorna(52,'Erro ao interpretar o arquivo XML!');


			$json['usuario']= $sxe['Nome'] . "";
			$json['l_titulo'] = $sxe->ListaQuestoes['Titulo'] . "";
			$json['l_tema'] = $sxe->ListaQuestoes->Tema . "";
			$json['l_autor'] = $sxe->ListaQuestoes->Autor . "";
			

			$i=0;
			//para cada questão encontrada
			foreach ($sxe->ListaQuestoes->Questao as $q) {
                $qID = 0 + $q['id'];
                $assinalouPos = $sxe->R[$qID]-1;
				$json['respostas'][$i]['id'] = $q['id'] . "";
				$json['respostas'][$i]['assinalou'] = $q->Alternativa[$assinalouPos]['id']. "";

				$num = 0 + $q->Tentativas;
				$tentativas = decode_correta($q->Enunciado, $num);
				$json['respostas'][$i]['tentativas'] = $tentativas . "";
				
				$i++;
			}
				
			$str = json_encode($json);
			insert_resultados_file($db,$str);
			retorna(0,'Sucesso!');
		}
	}

	retorna(53,'Unknow.');
