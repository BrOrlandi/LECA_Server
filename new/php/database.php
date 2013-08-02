<?php
	
	$username = "leca"; 
    $password = "leca"; 
    $host = "localhost"; 
    $port = "5432"; 
    $dbname = "LECA";
	
	try{
		$db = new PDO("pgsql:dbname={$dbname};host={$host};port={$port}",$username,$password);
	}catch(PDOException $ex){
		die("Conexão com o banco de dados falhou: " . $ex->getMessage());
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
    