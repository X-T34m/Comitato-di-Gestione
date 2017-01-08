<?php

	session_start();
	if (!isset($_SESSION[ 'login' ])) {
		header( 'Location: index.php' );
	}

	require 'DAO.php';
	$dao = new DAO();

	$addCat = true;

	if (!empty($_POST)) {
		if ($dao->newCategoria( $_POST[ 'nome' ] )) header( 'Location: newSpesa.php' );
		else $addCat = false;
	}

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<style type="text/css">
			<?php if (!$addCat) echo '#errore{color:red;font-size: 25px;}'; ?>
			* {
				font-family: 'Roboto Condensed', sans-serif;
				line-height: 200%;
				font-weight: 400;
			}

			button {
				padding: 10px;
				font-size: 20px;
				width: 100%;
				max-width: 300px;
			}

			textarea, input, select, option {
				width: 100%;
				max-width: 300px;
				padding: 20px;
				font-size: 30px;
			}
		</style>
	</head>
	<body>
	<table width="100%">
		<tr>
			<td>
				<a href="newSpesa.php">
					<button>ANNULLA</button>
				</a>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td><br></td>
		</tr>
		<?php
			if (!$addCat) echo '<tr><td id="errore" align="center">ERRORE, CONTATTA RUBEN</td></tr>';
		?>
		<form method="post" action="newCategoria.php">
			<tr>
				<td align="center">
					<input type="text" name="nome" placeholder="Nome" required>
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="submit" value="CREA">
				</td>
			</tr>
		</form>
	</table>
	</body>
</html>
