<?php session_start(); ?>
<?php
	require('functions.php');
	connect_to_database();
?>
<html Content-Type: text/html; charset=UTF-8>
	<head>
		<title>Stanistics Online</title>
		<script src="sorttable.js"></script>
		<script type="text/javascript" src="resizable-tables.js"></script>
		<link rel="stylesheet" type="text/css" href="stanistics.css" media="screen" /> 
	</head>
	<body>
		<h1><a href="index.php" style="text-decoration: none;">Stanistics Online</a> - Affichage du résultat</h1>
		<?php
			$txt = '';
			$entetecsv = '';
			
			
			
			effacecsv();
			
			// récupération des variables
			$localisations = ajoute_guillemets($_POST["var1"]);
			$sous_localisations = ajoute_guillemets($_POST["var2"]);
			if ($sous_localisations == '\'pas de sous-localisation\'') {
				$sous_localisations = '\'\'';
			}
			$catdoc = ajoute_guillemets($_POST["var3"]);
			$collections = ajoute_guillemets($_POST["var4"]);
			$fonds = ajoute_guillemets($_POST["var5"]);
			$statuts = ajoute_guillemets($_POST["var6"]);
			$supports = ajoute_guillemets($_POST["var12"]);
			$formats = ajoute_guillemets($_POST["var13"]);
			if (isset($_POST["var7"]) && !empty($_POST["var7"])) {
				$date_creation_debut = $_POST["var7"];
				$date_creation_fin = $_POST["var8"];
			}
			
			// $query = 'select t1.branch_code, ltrim(t1.subscriber_no, \'0\'), t2.subscriber_name, t3.call_num, ltrim(t1.document_no, \'0\'), t1.due_date, t1.due_hour, case when (t1.due_date = current_date and t1.due_hour < current_time) then \'Oui\' when (t1.due_date < current_date) then \'Oui\' else \'Non\' end as"Retard" from ci_dt_loan as t1 inner join ci_dt_subscriber as t2 on t1.subscriber_no = t2.subscriber_no inner join ca_dt_copies as t3 on t1.document_no = t3.document where t1.category = \'TA\' order by branch_code, t1.due_date, t1.due_hour';
			
			$query_count = 'SELECT count(*) FROM ca_dt_copies WHERE branch_code in ('.$localisations.') and sec_loca_code in ('.$sous_localisations.') and category_code in ('.$catdoc.') and collection_code in ('.$collections.') and fond_code in ('.$fonds.') and status in ('.$statuts.') and medium in ('.$supports.') and format in ('.$formats.')';
			if (strpos($query_count, 'and collection_code in ()') !== false) {
				$query_count = str_replace('and collection_code in ()', '', $query_count);
			}
			if (strpos($query_count, 'and fond_code in ()') !== false) {
				$query_count = str_replace('and fond_code in ()', '', $query_count);
			}
			if (isset($date_creation_debut) && !empty($date_creation_debut) && isset($date_creation_fin) && !empty($date_creation_fin)){
				$query_count = $query_count . " and (creation_date between '" . $date_creation_debut ."' and '" . $date_creation_fin . "')";
			}
			if (isset($_POST["var9"]) && !empty($_POST["var9"])) {
				$comparaison_date = $_POST["var9"];
				$nombre_prets = $_POST["var10"];
				$query_count = $query_count . " and check_out_life " . $comparaison_date . " " . $nombre_prets ;
			}
			if (isset($_POST["var11"]) && !empty($_POST["var11"])) {
				$cote = $_POST["var11"];
				$query_count = $query_count . " and call_num like '" . $cote . "%'" ;
			}
			if (strpos($query_count, 'and medium in ()') !== false) {
				$query_count = str_replace('and medium in ()', '', $query_count);
			}
			if (strpos($query_count, 'and format in ()') !== false) {
				$query_count = str_replace('and format in ()', '', $query_count);
			}
			echo $query_count . '<br />' ;
			// echo '<hr />';
			$result_count = pg_query($query_count) or die('Échec de la requête : ' . pg_last_error());
			$nombre_lignes = pg_fetch_row($result_count)[0];
			echo  $nombre_lignes.' exemplaires trouvés';
			// echo '<form action="index.php">';
				// echo '<input type="submit" value="Retour à l\'accueil" class="bouton_bleu" />';
			// echo '</form>';
			// Libère le résultat
			pg_free_result($result_count);
			
			// tout le reste ne doit être exécuté que s'il y a des exemplaires à afficher :
			if ($nombre_lignes <> 0) {
				$date = date("Y-d-m");
				$heure = date("H-i-s");
				$fichier = 'stanistics_' . $date . '_' . $heure . '.csv';
				$csvstream = fopen($fichier, "w") or die("impossible d'ouvrir le csv !");
				
				// pour qu'excel détecte bien que c'est de l'UTF8 : (cf http://www.ygi.ch/forcer-ouverture-fichier-csv-utf-8-excel/ et http://stackoverflow.com/questions/25686191/adding-bom-to-csv-file-using-fputcsv)
				$BOM = "\xEF\xBB\xBF"; // UTF-8 BOM
				fwrite($csvstream, $BOM); 
				//
				
				$champs = '';
				$entetetableau = '<tr>';
				$compteur = 0;
				
				If (isset($_POST["localisation"])) {
					$champs =  $champs . ' t1.branch_code as "Localisation"';
					$entetetableau = $entetetableau . '<th>Localisation</th>';
					$compteur++;
				}
				If (isset($_POST["souslocalisation"])) {
					$champs = $champs . ', t1.sec_loca_code as "Sous-localisation"';
					$entetetableau = $entetetableau . '<th>Sous-localisation</th>';
					$compteur++;
				}
				If (isset($_POST["catdoc"])) {
					$champs = $champs . ', t1.category_code as "Catégorie"';
					$entetetableau = $entetetableau . '<th>Catégorie documentaire</th>';
					$compteur++;
				}
				If (isset($_POST["collection"])) {
					$champs = $champs . ', t1.collection_code as "Code collection"';
					$entetetableau = $entetetableau . '<th>Collection</th>';
					$compteur++;
				}
				If (isset($_POST["fonds"])) {
					$champs = $champs . ', t1.fond_code as "Code de fonds"';
					$entetetableau = $entetetableau . '<th>Fonds</th>';
					$compteur++;
				}
				If (isset($_POST["cote"])) {
					$champs = $champs . ', t1.call_num as "Cote"';
					$entetetableau = $entetetableau . '<th style="min-width:100px">Cote</th>';
					$compteur++;
				}
				If (isset($_POST["cb"])) {
					$champs = $champs . ', t1.document as "Code-barres"';
					$entetetableau = $entetetableau . '<th>CB</th>';
					$compteur++;
				}
				If (isset($_POST["statut"])) {
					$champs = $champs . ', t1.status as "Statut"';
					$entetetableau = $entetetableau . '<th>État de disponibilité</th>';
					$compteur++;
				}
				If (isset($_POST["support"])) {
					$champs = $champs . ', t1.medium as "Support"';
					$entetetableau = $entetetableau . '<th>Support</th>';
					$compteur++;
				}
				If (isset($_POST["format"])) {
					$champs = $champs . ', t1.format as "Format"';
					$entetetableau = $entetetableau . '<th>Format</th>';
					$compteur++;
				}
				If (isset($_POST["titre"])) {
					$champs = $champs . ', t2.br0245 as "Titre"';
					$entetetableau = $entetetableau . '<th>Titre</th>';
					$compteur++;
				}
				If (isset($_POST["auteur"])) {
					$champs = $champs . ', t2.br01xx as "Auteur"';
					$entetetableau = $entetetableau . '<th>Auteur</th>';
					$compteur++;
				}
				If (isset($_POST["adresse"])) {
					$champs = $champs . ', t2.br0260 as "Éditeur"';
					$entetetableau = $entetetableau . '<th>Éditeur</th>';
					$compteur++;
				}
				If (isset($_POST["serie"])) {
					$champs = $champs . ', t2.br04xx as "Série"';
					$entetetableau = $entetetableau . '<th>Série</th>';
					$compteur++;
				}
				If (isset($_POST["date"])) {
					$champs = $champs . ', t2.br1003 as "Date de publication"';
					$entetetableau = $entetetableau . '<th>Date</th>';
					$compteur++;
				}
				If (isset($_POST["datecreationexemplaire"])) {
					$champs = $champs . ', t1.creation_date as "Date de création de l\'exemplaire"';
					$entetetableau = $entetetableau . '<th style="min-width:85px">Date création ex.</th>';
					$compteur++;
				}
				If (isset($_POST["dateinventaire"])) {
					$champs = $champs . ', t1.inventory_date as "Date de récolement"';
					$entetetableau = $entetetableau . '<th style="min-width:85px">Date inventaire</th>';
					$compteur++;
				}
				If (isset($_POST["nbpretstotal"])) {
					$champs = $champs . ', t1.check_out_life as "Nombre de prêts total"';
					$entetetableau = $entetetableau . '<th>Nb prêts total</th>';
					$compteur++;
				}
				If (isset($_POST["nbpretsannee"])) {
					$champs = $champs . ', t1.check_out_year as "Nombre de prêts dans l\'année"';
					$entetetableau = $entetetableau . '<th>Nb prêts année</th>';
					$compteur++;
				}
				If (isset($_POST["datedernierpret"])) {
					$champs = $champs . ', t1.last_loan as "Date du dernier prêt"';
					$entetetableau = $entetetableau . '<th style="min-width:85px">Date dernier prêt</th>';
					$compteur++;
				}
				If (isset($_POST["nbreservationsactuel"])) {
					$champs = $champs . ', t1.resv_cur as "Nombre de réservations en cours"';
					$entetetableau = $entetetableau . '<th>Nb réservations actuelles</th>';
					$compteur++;
				}
				If (isset($_POST["nbreservationstotal"])) {
					$champs = $champs . ', t1.resv_life as "Nombre de réservations total"';
					$entetetableau = $entetetableau . '<th>Nb réservations total</th>';
					$compteur++;
				}
				If (isset($_POST["nbreservationsannee"])) {
					$champs = $champs . ', t1.resv_year as "Nombre de réservations dans l\'année"';
					$entetetableau = $entetetableau . '<th>Nb réservations années</th>';
					$compteur++;
				}
				If (isset($_POST["lienportail"])) {
					$champs = $champs . ', lpad(t1.seq_no::text, 10, \'0\')';
					$entetetableau = $entetetableau . '<th>Lien portail</th>';
					$compteur++;
				}
				
				if(substr($champs, 0, 2) == ", ") {
					$champs = substr($champs, 2);
				}
				// echo $compteur;
				// foreach($_POST['champs'] as $valeur)
				// {
				   // echo "La checkbox $valeur a été cochée<br>";
				// }
				$entetetableau = $entetetableau . '</tr>';
				
				$entetecsv = $entetetableau;
				$entetecsv = str_replace('<th>', '"', $entetecsv);
				$entetecsv = str_replace('<th style="min-width:85px">', '"', $entetecsv);
				$entetecsv = str_replace('<th style="min-width:100px">', '"', $entetecsv);
				$entetecsv = str_replace('</th>', '";', $entetecsv);
				$entetecsv = str_replace('<tr>', '', $entetecsv);
				$entetecsv = str_replace(';</tr>', '', $entetecsv);
				$entetecsv = str_replace('Lien portail', 'Numéro notice bib', $entetecsv);
				
				$txt = diacritiques($entetecsv).PHP_EOL;
				fwrite($csvstream, $txt);

				echo '<hr />';

				$query = 'SELECT ' . $champs . ' FROM ca_dt_copies as t1 inner join ca_title_info as t2 on t1.seq_no = t2.seq_no WHERE t1.branch_code in ('.$localisations.') and t1.sec_loca_code in ('.$sous_localisations.') and t1.category_code in ('.$catdoc.') and t1.collection_code in ('.$collections.') and t1.fond_code in ('.$fonds.') and t1.status in ('.$statuts.') and t1.medium in ('.$supports.') and t1.format in ('.$formats.')';
				
				if (strpos($query, 'and t1.collection_code in ()') !== false) {
					$query = str_replace('and t1.collection_code in ()', '', $query);
				}
				if (strpos($query, 'and t1.fond_code in ()') !== false) {
					$query = str_replace('and t1.fond_code in ()', '', $query);
				}
				if (strpos($query, 'and t1.medium in ()') !== false) {
					$query = str_replace('and t1.medium in ()', '', $query);
				}
				if (strpos($query, 'and t1.format in ()') !== false) {
					$query = str_replace('and t1.format in ()', '', $query);
				}
				if (isset($date_creation_debut) && !empty($date_creation_debut) && isset($date_creation_fin) && !empty($date_creation_fin)){
					$query = $query . " and (t1.creation_date between '" . $date_creation_debut ."' and '" . $date_creation_fin . "')";
				}
				if (isset($_POST["var9"]) && !empty($_POST["var9"])) {
					$comparaison_date = $_POST["var9"];
					$nombre_prets = $_POST["var10"];
					$query = $query . " and t1.check_out_life " . $comparaison_date . " " . $nombre_prets ;
				}
				if (isset($_POST["var11"]) && !empty($_POST["var11"])) {
					$cote = $_POST["var11"];
					$query = $query . " and call_num like '" . $cote . "%'" ;
				}
				echo $query . '<br />';
				// echo '<hr />';
				echo '<form action="'.$fichier.'">';
				echo '<input type="submit" value="Télécharger en csv" class="bouton_bleu" />';
				echo '</form>(NB : Cliquer sur les en-têtes de colonne pour trier)';
				
				//on augmente le timeout pour cette requête :
				set_time_limit(60);
				
				$result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());
				if (!$result) {
				 echo"An error occurred.\n";
				 exit;
				}
				$val = pg_fetch_result($result, 0, 0);
				$table = '<table id="result" name="result" class="sortable" border="1" width="100%" >';
				$table = $table . $entetetableau;
				while ($row = pg_fetch_row($result)) {	
					$txt = '';
					$table = $table . '<tr>';
					// for ($n=0; $n<$compteur; $n++) {
						// if (($n == 8) || ($n == 9) || $n == 10) {
							// $table = $table . '<td>' . diacritiques($row[$n]) . '</td>'; // colonnes 8-10 = titre, auteur, série
						// }
						// elseif ($n == $compteur - 1) {
							// $table = $table . '<td><a href="http://www.bm-lille.fr/Default/doc/SYRACUSE/' .$row[$n] . '" target="_blank">Lien</a></td>';
						// }
						// else {
							// $table = $table . '<td>' .$row[$n] . '</td>';
						// }
						// pour chaque ligne sql, remplissage du csv :
						// $txt = $txt . '"' . $row[$n] . '";';
					// }
					for ($n=0; $n<$compteur; $n++) {
						if ($n == $compteur - 1) {
							$table = $table . '<td><a href="http://www.bm-lille.fr/Default/doc/SYRACUSE/' .$row[$n] . '" target="_blank">Lien</a></td>';
						}
						elseif (strpos(strval($row[$n]), '/') !== false) {
							$tableau = explode('/', strval($row[$n]));
							$row[$n] = array_shift($tableau);
							$table = $table . '<td>' . diacritiques($row[$n]) . '</td>';
						}
						else {
							$table = $table . '<td>' . diacritiques($row[$n]) . '</td>';
						}
						// pour chaque ligne sql, remplissage du csv :
						$txt = $txt . '"' . $row[$n] . '";';
					}
					$txt = rtrim ($txt,';');	
					$txt = diacritiques($txt);
					$txt = $txt . PHP_EOL;
					fwrite($csvstream, $txt);
					$table = $table . '</tr>';
				}
				$table = $table . '</table>';
				echo $table;
				// ferme le csv
				fclose($csvstream);
				// Libère le résultat
				pg_free_result($result);
				
				echo '<form action="'.$fichier.'">';
				echo '<input type="submit" value="Télécharger en csv" class="bouton_bleu" />';
				echo '</form>';
			}
			// fin du code qui ne s'exécute que quand il y a des résultats sql
			// Dans tous les cas, on ferme la connexion :
			pg_close($db_connection);
			?>
	</body>
</html>