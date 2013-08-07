<?php
	
	$username = "leca"; 
    $password = "leca"; 
    $host = "localhost"; 
    $port = "5432"; 
    $dbname = "LECA";
		
	try{
		$db = new PDO("pgsql:dbname={$dbname};host={$host};port={$port}",$username,$password);
	}catch(PDOException $ex){
		retorna(-1,"Conexão com o banco de dados falhou: " . $ex->getMessage());
	}
	
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 

	if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
    { 
        function undo_magic_quotes_gpc(&$array) 
        { 
            foreach($array as &$value) 
            { 
                if(is_array($value)) 
                { 
                    undo_magic_quotes_gpc($value); 
                } 
                else 
                { 
                    $value = stripslashes($value); 
                } 
            } 
        } 
     
        undo_magic_quotes_gpc($_POST); 
        undo_magic_quotes_gpc($_GET); 
        undo_magic_quotes_gpc($_COOKIE); 
    } 
    
    header('Content-Type: text/html; charset=utf-8'); 
	
	require("database_op.php");
	
	function retorna($status = 0, $message = null, $obj = null){
		$ret['status'] = $status;
		if(!is_null($message)){
			$ret['message'] = $message;
		}
		if(!is_null($obj)){
			$ret['obj'] = $obj;
		}
		echo json_encode($ret);
		die();
	}
    
	function message($status = 0,$message = null, $obj = null){
		$ret['status'] = $status;
		if(!is_null($message)){
			$ret['message'] = $message;
		}
		if(!is_null($obj)){
			$ret['obj'] = $obj;
		}
		return json_encode($ret);
	}
	
    function decode_correta($str, $id) {
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
	
	function abrevia_string($texto, $limite, $tres_p = "…") {
		if (strlen($texto) <= $limite) return $texto;
		$str2 = explode('||', wordwrap($texto, $limite, '||'));
		return array_shift($str2) . $tres_p;
	}
	
