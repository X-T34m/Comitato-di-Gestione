<?php

	ini_set( 'display_errors', 1 );
	ini_set( 'display_startup_errors', 1 );
	error_reporting( -1 );

	session_start();
	if (!isset($_SESSION[ 'login' ])) {
		header( 'Location: index.php' );
	}

	$MESE_ALL = "all";
	$MESE_GENNAIO = "01-31";
	$MESE_FEBBRAIO = "02-29";
	$MESE_MARZO = "03-31";
	$MESE_APRILE = "04-30";
	$MESE_MAGGIO = "05-31";
	$MESE_GIUGNO = "06-30";
	$MESE_LUGLIO = "07-31";
	$MESE_AGOSTO = "08-31";
	$MESE_SETTEMBRE = "09-30";
	$MESE_OTTOBRE = "10-31";
	$MESE_NOVEMBRE = "11-30";
	$MESE_DICEMBRE = "12-31";

	$MESE = $MESE_ALL;

	$ANNO = "all";

	require 'DAO.php';

	$dao = new DAO();

	$ORDINE_DATA = 0;
	$ORDINE_SPESA = 1;
	$ORDINE_CAT = 2;
	$ordine_crescente = "ASC";
	$ordine_decrescente = "DESC";

	$ORDINE = $ORDINE_DATA;
	$ordine = $ordine_decrescente;

	$CAT = "all";

	$page = 1;

	if (!empty($_POST)) {
		if (isset($_POST[ 'action' ])) {
			if (strcmp( $_POST[ 'action' ], "delete" ) == 0) $dao->removeSpesa( $_POST[ 'aid' ] );
		}
		if (isset($_POST[ 'ORDINE' ])) $ORDINE = $_POST[ 'ORDINE' ];
		if (isset($_POST[ 'ordine' ])) $ordine = $_POST[ 'ordine' ];
		if (isset($_POST[ 'mese' ])) $MESE = $_POST[ 'mese' ];
		if (isset($_POST[ 'page' ])) $page = $_POST[ 'page' ];
		if (isset($_POST[ 'anno' ])) $ANNO = $_POST[ 'anno' ];
		if (isset($_POST[ 'cat' ])) $CAT = $_POST[ 'cat' ];
	}

	$catList = $dao->getCategorie();
	$list = $dao->getSpese( $ORDINE, $ordine, $MESE, $ANNO, $CAT, ($page * 50) - 50 );
	$listWithoutLimit = $dao->getSpese( $ORDINE, $ordine, $MESE, $ANNO, $CAT, 0, 5000 );
	$_SESSION[ 'export' ] = $listWithoutLimit;
	$nRecords = count( $listWithoutLimit );
	$npage = round( $nRecords / 50, 0, PHP_ROUND_HALF_DOWN );
	if ($npage == 0) $npage++;

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<style type="text/css">
			<?php if (!empty($list)) echo '#emptyList{display:none;}'; ?>
			* {
				font-family: 'Roboto Condensed', sans-serif;
				line-height: 200%;
				font-weight: 400;
			}

			form {
				display: table-cell;
			}

			button {
				padding: 10px;
				font-size: 20px;
				width: 100%;
				max-width: 200px;
			}

			select, option {
				width: 100%;
				max-width: 300px;
				padding: 10px;
				font-size: 20px;
			}

			#emptyList {
				font-size: 28px;
			}

			.item1 {
				background-color: aliceblue;
			}
		</style>
	</head>
	<body>
	<table width="100%">
		<tr>
			<td width="33%" align="left">
				<a href="logout.php">
					<button>ESCI</button>
				</a>
			</td>
			<td width="33%" align="center">
				<a href="#"
					<?php if (!empty($list)){ ?>
				   onclick="window.open('export.php','Esporta','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=350');">
					<?php } else { ?>
						onclick="alert('Non ci sono dati da esportare');">
					<?php } ?>
					<button>ESPORTA</button>
				</a>
			</td>
			<td width="33%" align="right">
				<a href="newSpesa.php">
					<button>AGGIUNGI</button>
				</a>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td width="50%" align="left">
				<form id="formFiltroORDINE" method="post" action="platform.php">
					<input type="hidden" name="ordine" value="<?= $ordine ?>">
					<input type="hidden" name="mese" value="<?= $MESE ?>">
					<input type="hidden" name="anno" value="<?= $ANNO ?>">
					<input type="hidden" name="cat" value="<?= $CAT ?>">
					<label for="ORDINE">Ordina per</label>
					<select name="ORDINE" onchange="document.getElementById('formFiltroORDINE').submit();">
						<?php

							$nu = $ORDINE == $ORDINE_DATA ? "selected" : "";
							$no = $ORDINE == $ORDINE_SPESA ? "selected" : "";
							$ni = $ORDINE == $ORDINE_CAT ? "selected" : "";

						?>
						<option <?= $nu ?> value="<?= $ORDINE_DATA ?>">DATA</option>
						<option <?= $no ?> value="<?= $ORDINE_SPESA ?>">SPESA</option>
						<option <?= $ni ?> value="<?= $ORDINE_CAT ?>">CATEGORIA</option>
					</select>
				</form>
			</td>
			<td width="50%" align="right">
				<form id="formFiltroordine" method="post" action="platform.php">
					<input type="hidden" name="ORDINE" value="<?= $ORDINE ?>">
					<input type="hidden" name="mese" value="<?= $MESE ?>">
					<input type="hidden" name="anno" value="<?= $ANNO ?>">
					<input type="hidden" name="cat" value="<?= $CAT ?>">
					<label for="ordine">Ordina in modo</label>
					<select name="ordine" onchange="document.getElementById('formFiltroordine').submit();">
						<?php

							$c = $ordine == $ordine_crescente ? "selected" : "";
							$d = $ordine == $ordine_decrescente ? "selected" : "";

						?>
						<option <?= $c ?> value="<?= $ordine_crescente ?>">CRESCENTE</option>
						<option <?= $d ?> value="<?= $ordine_decrescente ?>">DECRESCENTE</option>
					</select>
				</form>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td width="33%" align="left">
				<form id="formFiltroMese" method="post" action="platform.php">
					<input type="hidden" name="ordine" value="<?= $ordine ?>">
					<input type="hidden" name="ORDINE" value="<?= $ORDINE ?>">
					<input type="hidden" name="anno" value="<?= $ANNO ?>">
					<input type="hidden" name="cat" value="<?= $CAT ?>">
					<label for="mese">Mesi</label>
					<select name="mese" onchange="document.getElementById('formFiltroMese').submit();">
						<?php

							$tutti = $MESE == $MESE_ALL ? "selected" : "";
							$m_1 = $MESE == $MESE_GENNAIO ? "selected" : "";
							$m_2 = $MESE == $MESE_FEBBRAIO ? "selected" : "";
							$m_3 = $MESE == $MESE_MARZO ? "selected" : "";
							$m_4 = $MESE == $MESE_APRILE ? "selected" : "";
							$m_5 = $MESE == $MESE_MAGGIO ? "selected" : "";
							$m_6 = $MESE == $MESE_GIUGNO ? "selected" : "";
							$m_7 = $MESE == $MESE_LUGLIO ? "selected" : "";
							$m_8 = $MESE == $MESE_AGOSTO ? "selected" : "";
							$m_9 = $MESE == $MESE_SETTEMBRE ? "selected" : "";
							$m_10 = $MESE == $MESE_OTTOBRE ? "selected" : "";
							$m_11 = $MESE == $MESE_NOVEMBRE ? "selected" : "";
							$m_12 = $MESE == $MESE_DICEMBRE ? "selected" : "";

						?>
						<option <?= $tutti ?> value="<?= $MESE_ALL ?>">TUTTI</option>
						<option <?= $m_1 ?> value="<?= $MESE_GENNAIO ?>">GENNAIO</option>
						<option <?= $m_2 ?> value="<?= $MESE_FEBBRAIO ?>">FEBBRAIO</option>
						<option <?= $m_3 ?> value="<?= $MESE_MARZO ?>">MARZO</option>
						<option <?= $m_4 ?> value="<?= $MESE_APRILE ?>">APRILE</option>
						<option <?= $m_5 ?> value="<?= $MESE_MAGGIO ?>">MAGGIO</option>
						<option <?= $m_6 ?> value="<?= $MESE_GIUGNO ?>">GIUGNO</option>
						<option <?= $m_7 ?> value="<?= $MESE_LUGLIO ?>">LUGLIO</option>
						<option <?= $m_8 ?> value="<?= $MESE_AGOSTO ?>">AGOSTO</option>
						<option <?= $m_9 ?> value="<?= $MESE_SETTEMBRE ?>">SETTEMBRE</option>
						<option <?= $m_10 ?> value="<?= $MESE_OTTOBRE ?>">OTTOBRE</option>
						<option <?= $m_11 ?> value="<?= $MESE_NOVEMBRE ?>">NOVEMBRE</option>
						<option <?= $m_12 ?> value="<?= $MESE_DICEMBRE ?>">DICEMBRE</option>
					</select>
				</form>
			</td>
			<td width="33%" align="center">
				<form id="formFiltroCat" method="post" action="platform.php">
					<input type="hidden" name="ordine" value="<?= $ordine ?>">
					<input type="hidden" name="ORDINE" value="<?= $ORDINE ?>">
					<input type="hidden" name="mese" value="<?= $MESE ?>">
					<input type="hidden" name="anno" value="<?= $ANNO ?>">
					<label for="cat">Categoria</label>
					<select name="cat" onchange="document.getElementById('formFiltroCat').submit();">
						<?php

							$a = $CAT == "all" ? "selected" : "";

						?>
						<option <?= $a ?> value="all">TUTTI</option>
						<?php

							foreach ($catList as $cat) {

								$selected = $CAT == $cat[ "id" ] ? "selected" : "";

								echo '<option ' . $selected . ' value="' . $cat[ "id" ] . '">' . $cat[ 'nome' ] . '</option>';
							}

						?>
					</select>
				</form>
			</td>
			<td width="33%" align="right">
				<form id="formFiltroAnno" method="post" action="platform.php">
					<input type="hidden" name="ordine" value="<?= $ordine ?>">
					<input type="hidden" name="ORDINE" value="<?= $ORDINE ?>">
					<input type="hidden" name="mese" value="<?= $MESE ?>">
					<input type="hidden" name="cat" value="<?= $CAT ?>">
					<label for="anno">Anno</label>
					<select name="anno" onchange="document.getElementById('formFiltroAnno').submit();">
						<?php

							$a = strcmp( $ANNO, "all" ) == 0 ? "selected" : "";
							$a14 = strcmp( $ANNO, "2014" ) == 0 ? "selected" : "";
							$a15 = strcmp( $ANNO, "2015" ) == 0 ? "selected" : "";
							$a16 = strcmp( $ANNO, "2016" ) == 0 ? "selected" : "";
							$a17 = strcmp( $ANNO, "2017" ) == 0 ? "selected" : "";

						?>
						<option <?= $a ?> value="all">TUTTI</option>
						<option <?= $a14 ?> value="2014">2014</option>
						<option <?= $a15 ?> value="2015">2015</option>
						<option <?= $a16 ?> value="2016">2016</option>
						<option <?= $a17 ?> value="2017">2017</option>
					</select>
				</form>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td id="emptyList" align="center" style="border-top: 1px solid #949494">Nessun spesa</td>
		</tr>
		<?php

			if (!empty($list)) echo '<tr><td><br></td></tr><th>#</th><th>CATEGORIA</th><th>DESCRIZIONE</th><th>SPESA</th><th>DATA</th><th></th><tr><td colspan="6"><hr></td></tr>';

			$tot = 0;

			$i = 0;
			foreach ($list as $spesa) {

				$tot += $spesa[ 'importo' ];

				$class = "item" . $i;

				/**
				 * ID
				 */
				echo '<tr class="' . $class . '"><td align="center">';
				echo $spesa[ 'id' ];

				/**
				 * CATEGORIA
				 */
				echo '</td><td align="center">';
				echo $spesa[ 'nome' ];

				/**
				 * DESCRIZIONE
				 */
				echo '</td><td align="center">';
				echo $spesa[ 'descr' ];

				/**
				 * SPESA
				 */
				echo '</td><td align="center">';
				echo $spesa[ 'importo' ] == 0 ? "" : "&euro; " . $spesa[ 'importo' ];

				/**
				 * DATA
				 */
				echo '</td><td align="center">';

				$data = explode( "-", explode( " ", $spesa[ 'dat' ] )[ 0 ] );

				echo $data[ 2 ] . "." . $data[ 1 ] . "." . $data[ 0 ];

				/**
				 * RIMUOVI
				 */
				echo '</td><td align="center">';
				$delete = "<form method='post' action='platform.php' onsubmit='return confirm(\"Sei sicuro di voler cancellare questa spesa?\\nQuesta operazione &egrave; irreversibile.\");'>";
				$delete .= '<input type = "hidden" name = "ordine" value = "' . $ordine . '" >';
				$delete .= '<input type = "hidden" name = "ORDINE" value = "' . $ORDINE . '" >';
				$delete .= '<input type = "hidden" name = "mese" value = "' . $MESE . '" >';
				$delete .= '<input type = "hidden" name = "anno" value = "' . $ANNO . '" >';
				$delete .= '<input type = "hidden" name = "page" value = "' . $page . '" >';
				$delete .= '<input type = "hidden" name = "cat" value = "' . $CAT . '" >';
				$delete .= "<input type='hidden' name='action' value='delete'>";
				$delete .= "<input type='hidden' name='aid' value='" . $spesa[ 'id' ] . "'>";
				$delete .= "<input type='submit' value='ELIMINA' /></form>";
				echo $delete;
				echo '</td></tr>';
				$i = $i == 0 ? 1 : 0;
			}

			if (!empty($list)) {
				echo '<tr><td style="color:#ff5a4f;" colspan="6" align="center">Totale: &euro; ' . $tot . '</td></tr>';
			}

		?>
	</table>
	<br>
	<table width="100%">
		<tr align="center">
			<td>
				Pagina:
				<?php

					for ($i = 1; $i <= $npage; $i++) {

						$font = $page == $i ? "23px" : "15px";
						$disabled = $page == $i ? " onsubmit='return false;'" : "";

						echo '<form action="platform.php" method="post" ' . $disabled . '>';
						echo '<input type="hidden" name="page" value="' . $i . '">';
						echo '<input type="hidden" name="ORDINE" value="' . $ORDINE . '">';
						echo '<input type="hidden" name="ordine" value="' . $ordine . '">';
						echo '<input type="hidden" name="mese" value="' . $MESE . '">';
						echo '<input type="hidden" name="anno" value="' . $ANNO . '">';
						echo '<input type="hidden" name="cat" value="' . $CAT . '">';
						echo '<input type="submit" value="' . $i . '" style="font-size:' . $font . '">';
						echo '</form>';

					}

				?>
			</td>
		</tr>
	</table>
	</body>
</html>
