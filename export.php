<?php

	session_start();
	$listToExport = $_SESSION[ 'export' ];
	$listCategorie = array();
	$listColors = array();

	function random_color_part () {
		return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT );
	}

	function random_color () {
		return random_color_part() . random_color_part() . random_color_part();
	}

	foreach ($listToExport as $spesa) {
		array_push( $listCategorie, $spesa[ 'nome' ] );
	}

	foreach ($listCategorie as $cat) {
		//echo 'LIST:' . $cat . '<br>';
		if (!array_key_exists( $cat, $listColors )) {
			$listColors[ $cat ] = random_color();
			//echo 'LIST C:' . $listColors[ $cat ] . '<br>';
		}
	}

	header( "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
	header( "Content-Disposition: attachment; filename=\"REPORT_" . time() . ".xlsx\"" );
	header( "Cache-Control: max-age=0" );

	//require './PHPExcel/PHPExcel/IOFactory.php';

	include './PHPExcel/PHPExcel.php';
	include './PHPExcel/PHPExcel/Writer/Excel2007.php';

	$objPHPExcel = new PHPExcel();

	//$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
	//$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
	$objPHPExcel->getProperties()->setTitle( "CDG Fraconalto" );
	//$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	//$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

	$objPHPExcel->setActiveSheetIndex( 0 );
	$objPHPExcel->getActiveSheet()->SetCellValue( 'A1', 'ID' );
	$objPHPExcel->getActiveSheet()->SetCellValue( 'B1', 'CATEGORIA' );
	$objPHPExcel->getActiveSheet()->SetCellValue( 'C1', 'DESCRIZIONE' );
	$objPHPExcel->getActiveSheet()->SetCellValue( 'D1', 'SPESA' );
	$objPHPExcel->getActiveSheet()->SetCellValue( 'E1', 'DATA' );

	$tot = 0;
	for ($i = 0, $p = 3; $i < count( $listToExport ); $i++, $p++) {
		$row = $listToExport[ $i ];
		$tot += $row[ 'importo' ];

		$objPHPExcel->getActiveSheet()->SetCellValue( 'A' . $p, $row[ 'id' ] );
		$objPHPExcel->getActiveSheet()->SetCellValue( 'B' . $p, $row[ 'nome' ] );
		$objPHPExcel->getActiveSheet()->getStyle( 'B' . $p )->applyFromArray(
			array(
				'fill' => array(
					'type'  => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array( 'rgb' => $listColors[ $row[ 'nome' ] ] )
				)
			)
		);
		$objPHPExcel->getActiveSheet()->SetCellValue( 'C' . $p, $row[ 'descr' ] );
		$objPHPExcel->getActiveSheet()->SetCellValue( 'D' . $p, $row[ 'importo' ] );

		$data = explode( "-", explode( " ", $row[ 'dat' ] )[ 0 ] );

		$objPHPExcel->getActiveSheet()->SetCellValue( 'E' . $p, $data[ 2 ] . "." . $data[ 1 ] . "." . $data[ 0 ] );
	}

	$objPHPExcel->getActiveSheet()->SetCellValue( 'E' . (count( $listToExport ) + 5), 'TOTALE: euro ' . $tot );

	$objPHPExcel->getActiveSheet()->setTitle( 'CDG' );

	$objWriter = new PHPExcel_Writer_Excel2007( $objPHPExcel );
	$objWriter->save( 'php://output' );

	//print_r($listToExport);

?>
<html>
	<body>
	</body>
</html>

