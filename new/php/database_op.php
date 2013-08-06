<?php


function insert_lista_exercicios($db, $json){
	$o = json_decode($json);

	$db->beginTransaction();
	
	$query = "INSERT INTO ListaExercicios(
			titulo,
			tema,
			autor,
			descricao,
			idioma,
			data,
			permuta,
			avaliacao
		) VALUES (
			:titulo,
			:tema,
			:autor,
			:descricao,
			:idioma, 
			:data,
			:permuta,
			:avaliacao
		) RETURNING leid;";
		
	if(strcmp($o->data, "") == 0){
		$o->data = null;
	}
	
	if(strcmp($o->permuta, 'Sim') == 0){
		$o->permuta = true;
	}else if(strcmp($o->permuta, 'Nao') == 0){
		$o->permuta = false;
	}
	
	$query_params = array( 
		':titulo' => $o->titulo, 
		':tema' => $o->tema, 
		':autor' => $o->autor, 
		':descricao' => $o->descricao,
		':idioma' => $o->idioma,
		':data' => $o->data,
		':permuta' => $o->permuta,
		':avaliacao' => $o->avaliacao
    );  
		
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){
		$db->rollBack();
		retorna(1,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
	
	$row = $stmt->fetch();
	$leid = $row['leid'];

	$i=0;
	foreach ($o->exercicios as $e) {
		insert_exercicio($db, $e,$leid,$i);
		$i++;	
	}
	$db->commit();
}

function insert_exercicio($db, $json_or_obj, $list_id = null, $id = null){
	try{
		$db->beginTransaction();
		$new_transaction = true;
	}catch(PDOException $ex){
		$new_transaction = false;
	}
	
	if(is_string($json_or_obj)){
		$o = json_decode($json_or_obj);
	}else
	{
		$o = $json_or_obj;	
	}
	
	$query = "INSERT INTO Exercicios(
			titulo,
			autor,
			enunciado,
			tema,
			correta,
			permuta
		) VALUES (
			:titulo,
			:autor,
			:enunciado,
			:tema,
			:correta,
			:permuta
		) RETURNING eid;";
		
	if(strcmp($o->permuta, 'Sim') == 0){
		$o->permuta = 'true';
	}else if(strcmp($o->permuta, 'Nao') == 0){
		$o->permuta = 'false';
	}
	
	$query_params = array( 
		':titulo' => $o->titulo, 
		':autor' => $o->autor, 
		':enunciado' => $o->enunciado,
		':tema' => $o->tema, 
		':correta' => $o->correta,
		':permuta' => $o->permuta
    );  
		
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){
		$db->rollBack();
		retorna(2,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
	
	$row = $stmt->fetch();
	$eid = $row['eid'];

	$i=0;
	foreach ($o->alternativas as $a) {
		insert_alternativa($db, $a,$eid,$i);
		$i++;	
	}
	
	if(!is_null($list_id) && !is_null($id)){
		insert_exercicio_list($db,$list_id,$eid,$id);
	}
	
	if($new_transaction){
		$db->commit();
	}
}

function insert_alternativa($db, $json_or_obj,$eid,$id){
	if(is_string($json_or_obj)){
		$o = json_decode($json_or_obj);
	}else
	{
		$o = $json_or_obj;	
	}
	
	$query = "INSERT INTO Alternativas(
			id,
			eid,
			texto
		) VALUES (
			:id,
			:eid,
			:texto
		);";
		
	$query_params = array( 
		':id' => $id, 
		':eid' => $eid, 
		':texto' => $o->texto
    );  
		
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){
		$db->rollBack();
		retorna(3,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
}

function insert_exercicio_list($db, $leid, $eid, $id){
	$query = "INSERT INTO ExListaEx(
			leid,
			eid,
			id
		) VALUES (
			:leid,
			:eid,
			:id
		);";
		
	$query_params = array( 
		':leid' => $leid, 
		':eid' => $eid, 
		':id' => $id
    );  
		
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){
		$db->rollBack();
		retorna(4,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
}

function get_listas($db){
	$query = "SELECT *,extract(day from data) AS dia,extract(month from data) AS mes,extract(year from data) AS ano FROM ListaExercicios;";
	try{
		$result = $db->query($query);
	}
	catch(PDOException $ex){
		retorna(8,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
	$rows = $result->fetchAll();
	
	$size = sizeof($rows);
	
	foreach ($rows as &$row) {
		if(!is_null($row['data']))
		{
			$row['data'] = str_pad($row['dia'], 2,'0',STR_PAD_LEFT) . '/' . str_pad($row['mes'], 2,'0',STR_PAD_LEFT)  .'/' . $row['ano'];
		}
		else
		{
			$row['data'] = 'Sem data.';
		}
		unset($row['dia']);
		unset($row['mes']);
		unset($row['ano']);
		
		if($row['permuta']){
			$row['permuta'] = 'Sim';	
		}else
		{
			$row['permuta'] = 'Não';	
		}
		
		switch ($row['avaliacao']) {
			case 0:
				$row['avaliacao'] = "Imediata";
			break;
			case 1:
				$row['avaliacao'] = "Final";
			break;
			case 2:
				$row['avaliacao'] = "Escolha do Aluno";
			break;
		}
	}
	
	retorna(0,'Sucesso!',$rows);
}

function get_exercicios($db,$leid){
	$query = "SELECT E.eid,
		CASE E.titulo WHEN '' THEN E.enunciado ELSE E.titulo END AS titulo,
		CASE E.titulo WHEN '' THEN false ELSE true END AS titulo_valido,
		E.autor,E.enunciado,E.tema,E.correta,E.permuta 
		FROM ExListaEx EL JOIN Exercicios E ON EL.leid = :leid AND EL.eid = E.eid;";
		
	$query_params = array( 
		':leid' => $leid
    );  
		
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){
		retorna(9,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
	
	$rows = $stmt->fetchAll();
	
	$size = sizeof($rows);
	
	foreach ($rows as &$row) {
		if(!$row['titulo_valido'])
			$row['titulo'] = abrevia_string($row['titulo'],75);
		unset($row['titulo_valido']);
		if($row['permuta']){
			$row['permuta'] = 'Sim';	
		}else
		{
			$row['permuta'] = 'Não';	
		}
		$row['enunciado'] = nl2br($row['enunciado']);
	}
	
	retorna(0,'Sucesso!',$rows);
}

function get_alternativas($db,$eid){
	$query = "SELECT id,texto FROM Alternativas WHERE EID = :eid;";
		
	$query_params = array( 
		':eid' => $eid
    );  
		
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){
		retorna(10,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
	
	$rows = $stmt->fetchAll();
	
	$size = sizeof($rows);
	
	retorna(0,'Sucesso!',$rows);
}

function insert_resultados_file($db,$json){
	
	$o = json_decode($json);
	
	$query = "SELECT leid FROM ListaExercicios WHERE titulo LIKE :titulo AND tema LIKE :tema AND autor LIKE :autor;";
	$query_params = array( 
		':titulo' => $o->l_titulo,
		':tema' => $o->l_tema,
		':autor' => $o->l_autor
    );
	
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}catch(PDOException $ex){
		retorna(11,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
	
	$row = $stmt->fetch();
	if(!$row)
		retorna(12,'Lista de Exercícios não foi encontrada.');

	$leid = $row['leid'];
	
	$query = "INSERT INTO Usuarios VALUES (:nome);";

	$query_params = array( 
		':nome' => $o->usuario
    );  
		
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){
		if($ex->getCode() != 23505)
		{
			$db->rollBack();
			retorna(13,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
		}
	}
	
	$db->beginTransaction();
	
	$query = "INSERT INTO Submissoes(usuario,leid) VALUES (:nome,:leid) RETURNING subid;";

	$query_params = array( 
		':nome' => $o->usuario,
		':leid' => $leid
    );  
	
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}catch(PDOException $ex){
		retorna(14,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}

	$row = $stmt->fetch();
	$subid = $row['subid'];

	
	$query = "SELECT id,eid FROM ExListaEx WHERE leid = :leid ORDER BY id ASC;";
	$query_params = array( 
		':leid' => $leid
    );
	
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}catch(PDOException $ex){
		retorna(15,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}
	
	$rows = $stmt->fetchAll();
	
	foreach ($o->respostas as $r) {
		$r->id = $rows[$r->id]['eid'];
		insert_resposta_alternativa($db,$r,$subid);
	}

	$db->commit();
}

function insert_resposta_alternativa($db,$resposta,$subid){
	
	$query = "INSERT INTO RespostasAlternativas(
			subid,
			eid,
			assinalou,
			tentativas
		) VALUES (
			:subid,
			:eid,
			:assinalou,
			:tentativas
		);";
		
	$query_params = array( 
		':subid' => $subid, 
		':eid' => $resposta->id, 
		':assinalou' => $resposta->assinalou, 
		':tentativas' => $resposta->tentativas
    );  
		
	try{
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){
		$db->rollBack();
		retorna(16,'Erro '.$ex->getCode() . ' : ' . $ex->getMessage(),$query_params);
	}	
}
/*
function get_

SELECT max(subid) as subid,S.usuario,S.leid FROM Submissoes S WHERE leid=12 GROUP BY S.usuario,S.leid ORDER BY subid;
*/