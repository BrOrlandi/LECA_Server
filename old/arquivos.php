<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>LECA Gráficos</title>
</head>
    <body>
    <p>Arquivos</p>

    <?php
    $filetemp = $_FILES['userfile']['tmp_name'];
    
    $file = fopen($_FILES["userfile"][], "w");
    fprintf($file,"%s", file_get_contents($filetemp));
    fclose($file);
    
    system("mv $filetemp ./teste1.txt");
    move_uploaded_file($filetemp, "./teste2.txt");
    copy($filetemp, "./teste.txt");
	echo $filetemp;
	
	?>
    </body>
</html>