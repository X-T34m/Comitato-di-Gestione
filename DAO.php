<?php

	include_once 'dbCredentials.php';

	class DAO {

		private static $CONN = null;

		public function __construct () {
			$info = "mysql:host=" . constant("HOST") . ";dbname=" . constant("DB") . ";charset=utf8";

			try {
				$opts = array(
					PDO::ATTR_PERSISTENT         => true,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);

				self::$CONN = new PDO($info, constant("USER"), constant("PASS"), $opts);
			} catch(PDOException $e) {
				die("PDOException: $e");
			}

			$error = self::$CONN->errorInfo();
			if (!is_null( $error[ 2 ] )) {
				die($error[ 2 ]);
			}
		}

		public function getSpese ($filtro, $ordine = "DESC", $mese = "all", $anno = "all", $cat = "all", $limit = 0, $max = 50) {

			$where = "";

			if (strcmp( $anno, "all" ) == 0 && !strcmp( $mese, "all" ) == 0) {
				$m = explode( "-", $mese )[ 0 ];
				$where = "WHERE ((s.dat >= '2017-" . $m . "-01' AND s.dat <= '2017-" . $mese . "') OR
				(s.dat >= '2016-" . $m . "-01' AND s.dat <= '2016-" . $mese . "') OR
				(s.dat >= '2015-" . $m . "-01' AND s.dat <= '2015-" . $mese . "') OR
				(s.dat >= '2014-" . $m . "-01' AND s.dat <= '2014-" . $mese . "'))";
			} elseif (!strcmp( $anno, "all" ) == 0 && strcmp( $mese, "all" ) == 0) {
				$where = "WHERE s.dat >= '" . $anno . "-01-01' AND s.dat <= '" . $anno . "-12-31'";
			} elseif (!strcmp( $anno, "all" ) == 0 && !strcmp( $mese, "all" ) == 0) {
				$m = explode( "-", $mese )[ 0 ];
				$where = "WHERE s.dat >= '" . $anno . "-" . $m . "-01' AND s.dat <= '" . $anno . "-" . $mese . "'";
			}

			if (!strcmp( $cat, "all" ) == 0) {
				if (empty($where)) $where = " WHERE ";
				else $where .= " AND ";
				$where .= " c.id = " . $cat;
			}

			$order = "s.dat " . $ordine;

			$ORDINE_DATA = 0;
			$ORDINE_SPESA = 1;
			$ORDINE_CAT = 2;

			if ($filtro == $ORDINE_DATA) $order = "s.dat " . $ordine;
			elseif ($filtro == $ORDINE_SPESA) $order = "s.importo " . $ordine;
			elseif ($filtro == $ORDINE_CAT) $order = "c.nome " . $ordine;

			$query = self::$CONN->prepare( "SELECT s.*, c.nome
											FROM spese AS s
											LEFT JOIN categorie AS c ON s.cID = c.id
											$where ORDER BY $order LIMIT $limit, $max" );
			$query->execute();

			return $query->fetchAll();
		}

		public function newSpesa ($desc, $imp, $date, $cat) {

			$int = 0;
			$dec = 0;

			if (!empty($imp)) {
				if (empty(explode( ",", $imp ))) {
					$int = $imp;
				} else {
					$aIMP = explode( ",", $imp );
					$int = empty($aIMP[ 0 ]) ? 0 : $aIMP[ 0 ];
					$dec = empty($aIMP[ 1 ]) ? 0 : $aIMP[ 1 ];

					if ($dec > 99) $dec = 99;

				}
			}

			$query = self::$CONN->prepare( "INSERT INTO spese (descr,importo,dat,cID) VALUES (:d,:i,:da,:cid)" );
			$query->bindValue( ":d", $desc, PDO::PARAM_STR );
			$query->bindValue( ":i", $int . "." . $dec );
			$query->bindValue( ":da", $date );
			$query->bindValue( ":cid", $cat );

			return $query->execute() ? true : self::$CONN->errorInfo();
		}

		public function removeSpesa ($id) {
			$query = self::$CONN->prepare( "DELETE FROM spese WHERE id = :id" );
			$query->bindValue( ":id", $id, PDO::PARAM_INT );

			return $query->execute();
		}

		public function getCategorie () {
			$query = self::$CONN->prepare( "SELECT * FROM categorie ORDER By nome" );
			$query->execute();

			return $query->fetchAll();
		}

		public function newCategoria ($nome) {
			$query = self::$CONN->prepare( "INSERT INTO categorie (nome) VALUES (:n)" );
			$query->bindValue( ":n", $nome, PDO::PARAM_STR );

			return $query->execute();
		}

	}