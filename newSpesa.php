<?php

	session_start();
	if (!isset($_SESSION[ 'login' ])) {
		header( 'Location: index.php' );
	}

	require 'DAO.php';
	$dao = new DAO();

	$catList = $dao->getCategorie();

	$addSpesa = true;

	if (!empty($_POST)) {
		if ($dao->newSpesa( $_POST[ 'd' ], $_POST[ 'imp' ], $_POST[ 'da' ], $_POST[ 'cat' ] )) header( 'Location: platform.php' );
		else $addSpesa = false;
	}

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<style type="text/css">
			<?php if (!$addSpesa) echo '#errore{color:red;font-size: 25px;}'; ?>
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
			<td width="50%" align="left">
				<a href="platform.php">
					<button>ANNULLA</button>
				</a>
			</td>
			<td width="50%" align="right">
				<a href="newCategoria.php">
					<button>CATEGORIA</button>
				</a>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td><br></td>
		</tr>
		<?php
			if (!$addSpesa) echo '<tr><td id="errore" align="center">ERRORE, CONTATTA RUBEN</td></tr>';
		?>
		<form method="post" action="newSpesa.php">
			<tr>
				<td align="center">
					<textarea name="d" placeholder="Descrizione" required></textarea>
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="text" name="imp" placeholder="Spesa esempio: 20,34">
				</td>
			</tr>
			<tr>
				<td align="center">
					<select name="cat" required>
						<option selected value="" disabled>CATEGORIA</option>
						<?php

							foreach ($catList as $cat) {
								echo '<option value="' . $cat[ "id" ] . '">' . $cat[ 'nome' ] . '</option>';
							}

						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="date" name="da" value="<?= date( "Y-m-d" ) ?>" min="2015-01-01">
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="submit" value="AGGIUNGI">
				</td>
			</tr>
		</form>
	</table>
	</body>
</html>
