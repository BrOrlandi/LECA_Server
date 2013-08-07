<!DOCTYPE HTML>
<html lang="pt">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>LECA Gráficos</title>
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
        
        
        <!-- Google Charts API -->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        
        <script type="text/javascript" src="js/jquery1.8.3.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/jquery.iframe-post-form.js"></script>
        
	</head>
	<body>
	    <div class="notify" id="uploadlist_notify" style="display: none"></div>
		<p class="center">
			Envie o arquivo da lista de exercicio:
		</p>
<!--		<form enctype="multipart/form-data" action="index.php" method="post"> -->
		<form class="upload" enctype="multipart/form-data" action="uploadlist.php" method="post">
			<input type="file" name="questionlistfile" />
			<input type="submit" name="enviar" value="Enviar Lista"/>
		</form>
        

		<p class="center">
			Envie aqui arquivos dos resultados obtidos
		</p>
<!--        <form enctype="multipart/form-data" action="index.php" method="post"> -->
        <form class="upload" enctype="multipart/form-data" action="uploadlist.php"" method="post">
            <input type="file" name="resultadofile" />
            <input type="submit" name="enviar" value="Enviar Resultado"/>
        </form>
        <div id="chartsbutton">Ver gráficos</div>
        <div id="feedback"></div>
        
        <div id="charts" style="display : none">
            <div id="charts_info">
            </div>
            <div id="charts_info2">
            </div>
            <div id="chart_draw">
            </div>
        </div>
	</body>
</html>
