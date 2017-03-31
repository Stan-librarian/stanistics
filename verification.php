<?php session_start(); ?>
<?php
	require('functions.php');
	$myIniFile = parse_ini_file ("config.ini", TRUE);
?>
<html Content-Type: text/html; charset=UTF-8>
	<head>
		<title>Stanistics Online</title>
		<link rel="stylesheet" type="text/css" href="stanistics.css" media="screen" /> 
	</head>
	<body>
		<h1><a href="index.php" style="text-decoration: none;">Stanistics Online</a> - Vérification des données</h1>
		<?php
			// Vérification des données transmises par index.php :
			$localisation = '';			
			$souslocalisation = '';			
			$catdoc = '';			
			$collection = '';			
			$fonds = '';			
			$statut = '';			
			$support = '';			
			$format = '';			
			$date_creation_debut = '';
			$date_creation_fin = '';
			$cote = '';
			
			// Localisations :
			if(!isset($_POST['localisation'])) {
				echo '<p>Pas de localisation sélectionnée.</p>';
			} else {
				if (!empty($_POST['localisation'])) {
					$Col1_Array = $_POST['localisation'];
					foreach($Col1_Array as $selectValue) {
						$localisation = $localisation.', '.$selectValue;
						if(substr($localisation, 0, 2) == ", ") {
							$localisation = substr($localisation, 2);
						}
					}
					echo '<p>Localisation(s) <b>: '.$localisation.'</b></p>';	
				}
			}
			// Sous-localisations :
			if(!isset($_POST['sous_localisation'])) {
				echo '<p>Pas de sous-localisation sélectionnée.</p>';
			} else {
				if (!empty($_POST['sous_localisation'])) {
					$Col1_Array = $_POST['sous_localisation'];
					foreach($Col1_Array as $selectValue) {
						$souslocalisation = $souslocalisation.', '.$selectValue;
						if(substr($souslocalisation, 0, 2) == ", ") {
							$souslocalisation = substr($souslocalisation, 2);
						}
					}
					echo '<p>Sous-localisation(s) <b>: '.$souslocalisation.'</b></p>';	
				}
			}
			// Catégories documentaires :
			if(!isset($_POST['categories_documentaires'])) {
				echo '<p>Pas de catégorie documentaire sélectionnée.</p>';
			} else {
				if (!empty($_POST['categories_documentaires'])) {
					$Col1_Array = $_POST['categories_documentaires'];
					foreach($Col1_Array as $selectValue) {
						$catdoc = $catdoc.', '.$selectValue;
						if(substr($catdoc, 0, 2) == ", ") {
							$catdoc = substr($catdoc, 2);
						}
					}
					echo '<p>Catégorie(s) documentaire(s) <b>: '.$catdoc.'</b></p>';	
				}
			}
			// Collections :
			if(!isset($_POST['collections'])) {
				echo '<p>Pas de collection sélectionnée.</p>';
			} else {
				if (!empty($_POST['collections'])) {
					$Col1_Array = $_POST['collections'];
					foreach($Col1_Array as $selectValue) {
						$collection = $collection.', '.$selectValue;
						if(substr($collection, 0, 2) == ", ") {
							$collection = substr($collection, 2);
						}
					}
					echo '<p>Collection(s) <b>: '.$collection.'</b></p>';	
				}
			}
			// Fonds :
			if(!isset($_POST['fonds'])) {
				echo '<p>Pas de fonds sélectionné.</p>';
			} else {
				if (!empty($_POST['fonds'])) {
					$Col1_Array = $_POST['fonds'];
					foreach($Col1_Array as $selectValue) {
						$fonds = $fonds.', '.$selectValue;
						if(substr($fonds, 0, 2) == ", ") {
							$fonds = substr($fonds, 2);
						}
					}
					echo '<p>Fonds <b>: '.$fonds.'</b></p>';	
				}
			}
			// Statuts :
			if(!isset($_POST['statuts'])) {
				echo '<p>Pas de statut sélectionné.</p>';
			} else {
				if (!empty($_POST['statuts'])) {
					$Col1_Array = $_POST['statuts'];
					foreach($Col1_Array as $selectValue) {
						$statut = $statut.', '.$selectValue;
						if(substr($statut, 0, 2) == ", ") {
							$statut = substr($statut, 2);
						}
					}
					echo '<p>Statut(s) <b>: '.$statut.'</b></p>';	
				}
			}
			// Supports :
			if(!isset($_POST['supports'])) {
				echo '<p>Pas de support sélectionné.</p>';
			} else {
				if (!empty($_POST['supports'])) {
					$Col1_Array = $_POST['supports'];
					foreach($Col1_Array as $selectValue) {
						$support = $support.', '.$selectValue;
						if(substr($support, 0, 2) == ", ") {
							$support = substr($support, 2);
						}
					}
					echo '<p>Support(s) <b>: '.$support.'</b></p>';	
				}
			}
			// Formats :
			if(!isset($_POST['formats'])) {
				echo '<p>Pas de format sélectionné.</p>';
			} else {
				if (!empty($_POST['formats'])) {
					$Col1_Array = $_POST['formats'];
					foreach($Col1_Array as $selectValue) {
						$format = $format.', '.$selectValue;
						if(substr($format, 0, 2) == ", ") {
							$format = substr($format, 2);
						}
					}
					echo '<p>Format(s) <b>: '.$format.'</b></p>';	
				}
			}
			// Date de création des exemplaires :
			If (isset($_POST["bool_date"])) {
				if ( isset($_POST['date_creation_debut']) && isset($_POST['date_creation_fin']) ) {
					// date de début + date de fin
					$date_creation_debut = $_POST['date_creation_debut'];
					$date_creation_fin = $_POST['date_creation_fin'];
					echo '<p>Date de création de l\'exemplaire entre le : <b>' . $date_creation_debut . '</b> et le <b>' . $date_creation_fin.'</b></p>';
				} elseif ( isset($_POST['date_creation_debut']) && !isset($_POST['date_creation_fin']) ) {
					// date de début mais pas date de fin
					echo '<p>Il manque la <b>date de fin</b></p>';
				} elseif ( !isset($_POST['date_creation_debut']) && isset($_POST['date_creation_fin']) ) {
					// date de fin mais pas date de début
					echo '<p>Il manque la <b>date de début</b></p>';
				} elseif ( !isset($_POST['date_creation_debut']) && !isset($_POST['date_creation_fin']) ) {
					// ni date de début ni date de fin
					echo '<p>Il manque la <b>date de début</b> et la <b>date de fin</b></p>';
				}
			} else {
				if ( isset($_POST['date_creation_debut']) && isset($_POST['date_creation_fin']) ) {
					echo '<p>Date de création de l\'exemplaire : <b>Vous n\'avez pas coché la case ! Cette date ne sera pas prise en compte.</b></p>';
				}
			}
			//
			If (isset($_POST["bool_nombre_prets"])) {
				if (isset($_POST['nombre_prets'])) {
					if (!empty($_POST['nombre_prets'])) {
						$nombre_prets = $_POST['nombre_prets'];
					}
					else {
						$nombre_prets = 0;
					}
				}
				if(isset($_POST['comparaison']) && !empty($_POST['comparaison'])){
					$comparaison_date = $_POST['comparaison'];
				}
			}
			If (isset($_POST["bool_cote"])) {
				$cote = strtoupper($_POST['cote']);
			}	
		?>
		<?php
		
		If (isset($_POST["bool_nombre_prets"])) {
			if (!empty($_POST['nombre_prets'])) {
				echo '<p>Nombre de prêts <b>' . $comparaison_date . ' ' . $nombre_prets.'</b></p>';
			}
			else {
				echo '<p>Nombre de prêts : <b>Il manque le nombre !</b></p>';
			}	
		}
		
		if (!empty($_POST['nombre_prets'])) {
			If (!isset($_POST["bool_nombre_prets"])) {
				echo '<p>Nombre de prêts : <b>Vous n\'avez pas coché la case ! Ce nombre ne sera pas pris en compte.</b></p>';
			}
		}
		If (isset($_POST["bool_cote"])) {
			if (!empty($_POST['cote'])) {
				echo '<p>Cote commençant par : <b>' . $cote . '</b></p>';
			}
			else {
				echo '<p>Cote : <b>Vous n\'avez pas coché la case ! Ce texte ne sera pas pris en compte.</b></p>';
			}
		}
		if (!empty($_POST['cote'])) {
			If (!isset($_POST["bool_cote"])) {
				echo '<p>Cote : <b>Il manque le texte !</b></p>';
			}
		}
		?>
		<h1>Sélection des champs à afficher</h1>
		<form method="post" action="resultat.php">
			<input type="checkbox" name="localisation" value="localisation" checked>&nbsp;Localisation&nbsp;</input>
			<input type="checkbox" name="souslocalisation" value="souslocalisation" checked>&nbsp;Sous-localisation&nbsp;</input>
			<input type="checkbox" name="catdoc" value="catdoc" checked>&nbsp;Catégorie documentaire&nbsp;</input>
			<input type="checkbox" name="collection" value="collection" checked>&nbsp;Collection&nbsp;</input>
			<input type="checkbox" name="fonds" value="fonds" checked>&nbsp;Fonds&nbsp;</input>
			<input type="checkbox" name="cote" value="cote" checked>&nbsp;Cote&nbsp;</input>
			<input type="checkbox" name="cb" value="cb" checked>&nbsp;Code-barres&nbsp;</input>
			<input type="checkbox" name="statut" value="statut" checked>&nbsp;Statut &nbsp;</input>
			<input type="checkbox" name="support" value="support" checked>&nbsp;Support &nbsp;</input>
			<input type="checkbox" name="format" value="format" checked>&nbsp;Format &nbsp;</input>
			<br />
			<input type="checkbox" name="titre" value="titre" checked>&nbsp;Titre&nbsp;</input>
			<input type="checkbox" name="auteur" value="auteur" checked>&nbsp;Auteur&nbsp;</input>
			<input type="checkbox" name="adresse" value="adresse">&nbsp;Éditeur&nbsp;</input>
			<input type="checkbox" name="serie" value="serie" checked>&nbsp;Série&nbsp;</input>
			<input type="checkbox" name="date" value="date" checked>&nbsp;Date de publication&nbsp;</input>
			<br />
			<input type="checkbox" name="datecreationexemplaire" value="datecreationexemplaire" checked>&nbsp;Date de création de l'exemplaire&nbsp;</input>
			<input type="checkbox" name="dateinventaire" value="dateinventaire">&nbsp;Date de récolement&nbsp;</input>
			<br />
			<input type="checkbox" name="nbpretstotal" value="nbpretstotal" checked>&nbsp;Nombre de prêts total&nbsp;</input>
			<input type="checkbox" name="nbpretsannee" value="nbpretsannee">&nbsp;Nombre de prêts sur l'année&nbsp;</input>
			<input type="checkbox" name="datedernierpret" value="datedernierpret" checked>&nbsp;Date du dernier prêt&nbsp;</input>
			<br />
			<input type="checkbox" name="nbreservationsactuel" value="nbreservationsactuel">&nbsp;Nombre de réservations actuel&nbsp;</input>
			<input type="checkbox" name="nbreservationstotal" value="nbreservationstotal">&nbsp;Nombre de réservations total&nbsp;</input>
			<input type="checkbox" name="nbreservationsannee" value="nbreservationsannee">&nbsp;Nombre de réservations sur l'année&nbsp;</input>
			<br />
			<input type="checkbox" name="lienportail" value="lienportail" checked>&nbsp;Lien portail&nbsp;</input>
			<br />
		<?php
			echo '<input type="hidden" name="var1" value="'.$localisation.'"></input>';
			echo '<input type="hidden" name="var2" value="'.$souslocalisation.'"></input>';
			echo '<input type="hidden" name="var3" value="'.$catdoc.'"></input>';
			echo '<input type="hidden" name="var4" value="'.$collection.'"></input>';
			echo '<input type="hidden" name="var5" value="'.$fonds.'"></input>';
			echo '<input type="hidden" name="var6" value="'.$statut.'"></input>';
			echo '<input type="hidden" name="var12" value="'.$support.'"></input>';
			echo '<input type="hidden" name="var13" value="'.$format.'"></input>';
			If (isset($_POST["bool_date"])) {
				echo '<input type="hidden" name="var7" value="'.$date_creation_debut.'"></input>';
				echo '<input type="hidden" name="var8" value="'.$date_creation_fin.'"></input>';
			}
			If (isset($_POST["bool_nombre_prets"])) {
				echo '<input type="hidden" name="var9" value="'.$comparaison_date.'"></input>';
				echo '<input type="hidden" name="var10" value="'.$nombre_prets.'"></input>';
			}
			If (isset($_POST["bool_cote"])) {
				echo '<input type="hidden" name="var11" value="'.$cote.'"></input>';
			}
			echo '<br />';
			echo '<input type="button" onclick="javascript:window.history.go(-1)" value="Revenir à l\'écran de sélection" class="bouton_bleu" />&nbsp;'; // attention, quand on revient en arrière, on perd les champs sélectionnés, car les select sont construits dynamiquement par une requête SQL -> y a-t-il un moyen de garder les champs déjà sélectionnés ?
			// echo '<input type="button" onclick="history.go(-1)" value="Revenir à l\'écran de sélection" class="bouton_bleu" />&nbsp;'; // attention, quand on revient en arrière, on perd les champs sélectionnés, car les select sont construits dynamiquement par une requête SQL -> y a-t-il un moyen de garder les champs déjà sélectionnés ?
			// echo '<input type="button" onclick="document.location.href = document.referrer" value="Revenir à l\'écran de sélection" class="bouton_bleu" />&nbsp;';
			echo '<input type="submit" value="Envoyer" class="bouton_bleu" />';
		echo '</form>';
		?>
		<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Retour</a>

	</body>
</html>
