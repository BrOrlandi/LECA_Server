<!DOCTYPE HTML>
<html lang="pt">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>LECA Gráficos</title>
		<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
	</head>
	<body>
		<p id="center">
			Envie o arquivo da lista de exercicio:
		</p>
<!--		<form enctype="multipart/form-data" action="index.php" method="post"> -->
		<form enctype="multipart/form-data" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
			<input type="file" name="questionlistfile" />
			<input type="submit" name="enviar" value="Enviar Lista" />
		</form>
		<p id="center">
			Envie aqui arquivos dos resultados obtidos
		</p>
<!--		<form enctype="multipart/form-data" action="index.php" method="post"> -->
		<form enctype="multipart/form-data" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
			<input type="file" name="resultadofile" />
			<input type="submit" name="enviar" value="Enviar Resultado" />
		</form>
		<?php
		
		
///------------------------------------------------------------------------------------

		define("QUESTIONLIST", "data/QuestionList.xml");
		define("RESULTS", "data/ResultsTemp.xml");
		define("MYDATABASE", "ListaDeQuestoes");

		//verifica se tem arquivo de lista de questões enviado.
		$qfile = NULL;
		if (isset($_FILES['questionlistfile'])) {
			$qfile = $_FILES['questionlistfile'];
		}
		if ($qfile != NULL && $qfile['error'] == UPLOAD_ERR_NO_FILE) {
			echo "<p><br/>Nenhum arquivo foi selecionado para enviar!<br/></p>";
		} else if ($qfile != NULL && $qfile['error'] == UPLOAD_ERR_OK) {
			//salva arquivo no servidor
			$file = fopen(QUESTIONLIST, "w");
			fwrite($file, file_get_contents($qfile['tmp_name']));
			fclose($file);
			//le o arquivo como XML
			$sxeList = simplexml_load_file(QUESTIONLIST);
			if (!$sxeList)
				echo "<p>ERRO de XML!</p>";

			// cria o banco de dados
			$db = createDataBase();

			//para cada questão encontrada adicionar no banco
			foreach ($sxeList->Questao as $q) {
				insere_questao_db($q, $db);
			}

			
			// debug ver questoes e alternativas
			echo "<br/>";
			$r = mysql_query_b("SELECT * FROM `questions`", $db);
			//echo $res;
			while ($r2 = mysql_fetch_array($r)) {
				echo "<p>" . $r2['enunciado'] . "</p>";
				$r3 = mysql_query_b("SELECT * FROM  `alternativas` WHERE  `questionID` =" . $r2['qID'], $db);
				while ($r4 = mysql_fetch_array($r3)) {
					echo "<p id=\"alt\">" . $r4['texto'] . "</p>";
				}
				mysql_free_result($r3);
				echo "<br/>";
			}
			mysql_free_result($r);
			//*/

			//$str = mysql_result($res, 1,"enunciado");
			//echo "<br/>a<br/>";
			
			echo "<p><br/>Lista de exercicios enviada!<br/></p>";
		}

///------------------------------------------------------------------------------------
		//verifica se tem arquivo de resultados de um usuário
		$rfile = NULL;
		if (isset($_FILES['resultadofile'])) {
			$rfile = $_FILES['resultadofile'];
		}
		if ($rfile != NULL && $rfile['error'] == UPLOAD_ERR_NO_FILE) {
			echo "<p><br/>Nenhum arquivo foi selecionado para enviar!<br/></p>";
		} else if ($rfile != NULL && $rfile['error'] == UPLOAD_ERR_OK) {
			//salva arquivo no servidor
			$file = fopen(RESULTS, "w");
			
			fwrite($file, file_get_contents($rfile['tmp_name']));
			fclose($file);
			//le o arquivo como XML
			$sxeList = simplexml_load_file(RESULTS);
			if (!$sxeList)
				echo "<p>ERRO de XML!</p>";

			// conecta o banco de dados
			$mysql = mysql_connect("localhost", "root", "einstein");
			mysql_select_db(MYDATABASE, $mysql);

			$sql = 'INSERT INTO `ListaDeQuestoes`.`users` (`name`) VALUES (\''. $sxeList['Nome'] .'\');';
			mysql_query_b($sql,$mysql);
			
			//pega o user ID atual
			$result = mysql_query_b("SELECT `ID` FROM `users` WHERE `name` LIKE '" . $sxeList['Nome']."'",$mysql);
			$row = mysql_fetch_array($result);
			$userid = 0 + $row['ID'];
			mysql_free_result($result);
			

			//var_dump($sxeList);
			echo "<br/><br/>";
			//var_dump($sxeList->ListaQuestoes->Questao);
			
			//para cada questão
			foreach ($sxeList->ListaQuestoes->Questao as $q) {
				//var_dump($q);
				$qID = 0 + $q['id'];
				//echo gettype($qID); // integer
				$assinalouPos = $sxeList->R[$qID]-1;
				$inc = $qID+1;
				$sql = "SELECT * FROM `questions` WHERE `qID` =" . $inc;
				$r = mysql_query_b($sql,$mysql);
				$question = mysql_fetch_array($r);
				mysql_free_result($r);
				echo "<p>" . $question['enunciado'] . "</p>";
				$sql = "SELECT * FROM `alternativas` WHERE `questionID` =" . $inc . " AND `alternativeID`=". $q->Alternativa[$assinalouPos]['id'];
				$r = mysql_query_b($sql,$mysql);
				$alt = mysql_fetch_array($r);
				mysql_free_result($r);
				echo "<p id=\"alt\">" . $alt['texto'] . "</p>";
				
				$sql = "REPLACE INTO `respostasAlternativas` (`userID`,`qID`,`assinalada`) VALUES ('". $userid . "','". $inc . "','". $q->Alternativa[$assinalouPos]['id'] . "')";
				mysql_query_b($sql,$mysql);
				//echo mysql_error();
			}
			
			echo "<p><br/>Resultados enviados!<br/></p>";
		}
///------------------------------------------------------------------------------------
		
		function mysql_query_b($sql,$db){
			$res = mysql_query($sql,$db);
			if($err = mysql_error()){
				echo "<p id=\"error\">" . $err . "<p/>";
			}
			return $res;
		}

		function createDataBase() {

			$mysql = mysql_connect("localhost", "root", "einstein");
			mysql_query_b("DROP DATABASE `" . MYDATABASE . "`",$mysql);
			mysql_query_b("CREATE DATABASE " . MYDATABASE . " COLLATE latin1_swedish_ci",$mysql);
			mysql_select_db(MYDATABASE, $mysql);
			

			$sql = 'CREATE TABLE questions (
				qID INTEGER PRIMARY KEY AUTO_INCREMENT,
				tema TEXT ,
				titulo TEXT ,
				enunciado TEXT ,
				correta INTEGER);';
			mysql_query_b($sql, $mysql);

					//aID INTEGER PRIMARY KEY AUTO_INCREMENT,
			$sql = 'CREATE TABLE alternativas (
					questionID INTEGER ,
					alternativeID INTEGER ,
					texto TEXT );';
			mysql_query_b($sql, $mysql);

			$sql = 'CREATE TABLE users (
					ID INTEGER PRIMARY KEY AUTO_INCREMENT,
					name CHAR(255) NOT NULL,
					UNIQUE INDEX (name));';
			mysql_query_b($sql, $mysql);
			
			$sql = 'CREATE TABLE respostasQuestoes (
					userID INTEGER,
					qID INTEGER,
					tentativas INTEGER);';
			mysql_query_b($sql, $mysql);
			
			$sql = 'CREATE TABLE respostasAlternativas (
					userID INTEGER,
					qID INTEGER,
					assinalada INTEGER,
					PRIMARY KEY (userID, qID));';
			mysql_query_b($sql, $mysql);

			//    $sql = 'INSERT INTO questions (tema, titulo, enunciado,correta) VALUES("programação","Titulo","Enunciado",4545)';
			//mysql_query_b($sql, $mysql);

			/*$sql = 'SELECT tema,titulo FROM questions';
			 $res = mysql_query_b($sql, $mysql);
			 while($row = mysql_fetch_array($res)){
			 foreach($row as $var => $valor){
			 echo $var . " = " . $valor . "<br/>";
			 }
			 }*/
			mysql_query_b('TRUNCATE TABLE questions', $mysql);
			mysql_query_b('TRUNCATE TABLE alternativas', $mysql);
			mysql_query_b('TRUNCATE TABLE users', $mysql);
			mysql_query_b('TRUNCATE TABLE respostasQuestoes', $mysql);
			mysql_query_b('TRUNCATE TABLE respostasAlternativas', $mysql);
			return $mysql;
		}

		function insere_questao_db($questao, $db) {

			$num = 0 + $questao -> Correta;
			$correta = decode($questao -> Enunciado, $num);

			//formatar strings para arrumar as aspas \" -> \\\" senão dá erro no banco de dados
			$questao->Tema = formatString($questao->Tema);
			$questao['Titulo'] = formatString($questao['Titulo']);
			$questao->Enunciado = formatString($questao->Enunciado);
			
			
			$sql = 'INSERT INTO questions (tema, titulo, enunciado,correta) VALUES("' . $questao -> Tema . '","' . $questao['Titulo'] . '","' . $questao -> Enunciado . '","' . $correta . '")';
			mysql_query_b($sql, $db);
			
			//pega o question ID atual
			$result = mysql_query_b("SHOW TABLE STATUS LIKE 'questions'",$db);
			$row = mysql_fetch_array($result);
			//var_dump($row);
			$nextId = $row['Rows'];
			mysql_free_result($result);
			
			//add alternativas
			insere_alternativas_db($questao->Alternativa, $db, $nextId);
		}

		function insere_alternativas_db($alternativas, $db, $qID)
		{
			$aid = 0;
			foreach ($alternativas as $key) {
				$key = formatString($key);
				$sql = 'INSERT INTO alternativas (questionID, alternativeID, texto) VALUES("' . $qID . '","' . $aid . '","' . $key . '")';
				mysql_query_b($sql, $db);
				$aid++;
			}
		}

		/**
		 * Formata a string substituindo as aspas(") por (\") 
		 */
		function formatString($string) {
			$str = str_replace("\\", "\\\\", $string);
			$str = str_replace("\"", "\\\"", $str);
			return $str;
		}

		function decode($str, $id) {
			$primeirochar = substr($str, 0, 1);
			// pega o código do primeiro char do enunciado
			$r = $id % 3;
			switch($r) {
				case 0 :
					$randnum = 47;
					break;
				case 1 :
					$randnum = 29;
					break;
				case 2 :
					$randnum = 71;
					break;
			}
			$primeirochar = ord($primeirochar);
			$idAlt = floor(((($id / 3) / $randnum) / $primeirochar) - 1);
			return $idAlt;
		}
		?>
	</body>
</html>
