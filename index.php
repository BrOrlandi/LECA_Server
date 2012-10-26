<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>LECA Gráficos</title>
</head>
    <body>
    <p>Envie o arquivo da lista de exercicio:</p>
    <form enctype="multipart/form-data" action="index.php" method="post">
    <input type="file" name="questionlistfile" />
    <input type="submit" name="enviar" value="Enviar Lista" />
    </form>
    <p>Envie aqui arquivos dos resultados obtidos</p>
    <form enctype="multipart/form-data" action="arquivos.php" method="post">
    <input type="file" name="userfile" />
    <input type="submit" name="enviar" value="Enviar Resultado" />
    </form>
    <?php

    define("QUESTIONLIST", "data/QuestionList.xml");
    define("MYDATABASE", "ListaDeQuestoes");
    
    $qfile = $_FILES['questionlistfile'];
    //var_dump($qfile);
    if($qfile != NULL && $qfile['error'] == UPLOAD_ERR_NO_FILE){
		echo "<br/>Nenhum arquivo foi selecionado para enviar!<br/>";
    }
    else if($qfile != NULL && $qfile['error'] == UPLOAD_ERR_OK){
		echo "<br/>Lista de exercicios enviada!<br/>";
		$file = fopen(QUESTIONLIST, "w");
		fwrite($file,file_get_contents($qfile['tmp_name']));
		fclose($file);
		
		$sxeList = simplexml_load_file(QUESTIONLIST);
		if(!$sxeList) echo "ERRO de XML!";
		
		$db = createDataBase();
		
		foreach ($sxeList->Questao as $q) {
			insere_questao_db($q,$db);
		}
	}
    
	function createDataBase(){
	
	    $mysql = mysql_connect("localhost","root","einstein");
    	mysql_query("CREATE DATABASE ". MYDATABASE . " COLLATE utf8_bin");
	    mysql_select_db(MYDATABASE,$mysql);
	    
	    
		$sql = 'CREATE TABLE IF NOT EXISTS questions (
				qID INTEGER PRIMARY KEY AUTO_INCREMENT,
				tema TEXT ,
				titulo TEXT ,
				enunciado TEXT ,
				correta INTEGER);';
		mysql_query($sql, $mysql);
		

		$sql = 'CREATE TABLE IF NOT EXISTS alternativa (
					aID INTEGER PRIMARY KEY AUTO_INCREMENT,
					questionID INTEGER ,
					alternativeID INTEGER ,
					texto TEXT );';
		mysql_query($sql, $mysql);
	    
	    //    $sql = 'INSERT INTO questions (tema, titulo, enunciado,correta) VALUES("programação","Titulo","Enunciado",4545)';
			//mysql_query($sql, $mysql);
			
			/*$sql = 'SELECT tema,titulo FROM questions';
			$res = mysql_query($sql, $mysql);
			while($row = mysql_fetch_array($res)){
			    foreach($row as $var => $valor){
			        echo $var . " = " . $valor . "<br/>";
			    }
			}*/
		return $mysql;
	}
	
	function insere_questao_db($questao, $db){
		//var_dump($questao);
		//echo "<br/><br/>";
		
		$num = 0 + $questao->Correta;
		$correta = decode($questao->Enunciado,$num);
		echo $questao->Enunciado . "<br/>";
		//echo $num . "<br/>";
		echo ($correta) . '<br/><br/>';
		$sql = 'INSERT INTO questions (tema, titulo, enunciado,correta) VALUES("programação","Titulo","Enunciado",4545)';
		//mysql_query($sql, $mysql);
	}
	
	function decode($str, $id){
		$primeirochar = substr($str, 0,1); // pega o código do primeiro char do enunciado
		$r = $id%3;
		switch($r)
		{
			case 0:
				$randnum = 47;
				break;
			case 1:
				$randnum = 29;
				break;
			case 2:
				$randnum = 71;
				break;
		}
		$primeirochar = ord($primeirochar);
		$idAlt = floor(((($id/3)/$randnum)/$primeirochar)-1);
		return $idAlt;
	}
	
	?>
    </body>
</html>
