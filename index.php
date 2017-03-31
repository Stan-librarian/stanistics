<?php session_start(); ?>
<?php
	require('functions.php');
	$myIniFile = parse_ini_file ("config.ini", TRUE);
	connect_to_database();
?>
<html Content-Type: text/html; charset=UTF-8>
	<head>
		<title>Stanistics Online</title>
		<link rel="stylesheet" type="text/css" href="jquery-ui.css" media="screen" /> 
		<link rel="stylesheet" type="text/css" href="stanistics.css" media="screen" /> 
		<script type="text/javascript" src="sorttable.js"></script>
		<script type="text/javascript" src="jquery-3.1.1.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>
		<script type="text/javascript">
			$.datepicker.regional['fr'] = {
				closeText: 'Fermer',
				prevText: 'Précédent',
				nextText: 'Suivant',
				currentText: 'Aujourd\'hui',
				monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
				monthNamesShort: ['Janv.','Févr.','Mars','Avril','Mai','Juin','Juil.','Août','Sept.','Oct.','Nov.','Déc.'],
				dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
				dayNamesShort: ['Dim.','Lun.','Mar.','Mer.','Jeu.','Ven.','Sam.'],
				dayNamesMin: ['D','L','M','M','J','V','S'],
				weekHeader: 'Sem.',
				dateFormat: 'yy-mm-dd',
				firstDay: 1,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: ''
			};
			$.datepicker.setDefaults($.datepicker.regional['fr']);
			jQuery(document).ready(function($){
				$(".datepicker").datepicker({minDate: new Date("2006-04-23"), maxDate: 'today'});
			});
		</script>
	</head>
	<body>
		<h1>Stanistics Online - Sélection des exemplaires<a href="http://www.bm-lille.fr/" target="_blank"><img src="./images/logo_bml.png" height="50px" class="flotte" /></a></h1>
		<p>Sélectionner une ou plusieurs localisation(s), sous-localisation(s) etc… puis cliquer sur <b>Envoyer</b></p>
		<a href="aide.html"  onclick="window.open('aide.html', 'Popup', 'scrollbars=yes,resizable=yes,height=400,width=600,top=300,left=300'); return false;" target ="_blank">Aide</a>
		<!-- début du formulaire : -->
		<form name="form1" method="post" action="verification.php">
		<div id="container">
			<div class="element"><div class="boite">Localisations (obligatoire)</div>
				<?php
					$localisations_exclues = ajoute_guillemets_pour_requete($myIniFile["EXCLUSIONS"]["LOCALISATIONS"]);
					if (!empty($localisations_exclues)) {
						// $requete_liste_localisation = 'select t1.branch_code as code, t2.description as nom from ca_dt_branch as t1 inner join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' and t1.branch_code not in (' . $localisations_exclues . ') order by t1.branch_code';
						$requete_liste_localisation = 'select 0, string_agg(branch_code, \', \' order by branch_code), \'all\' from ca_dt_branch where branch_code not in (' . $localisations_exclues . ') union select  row_number() over(PARTITION BY t1.branch_code ORDER BY t1.branch_code), t1.branch_code, t2.description from 	ca_dt_branch as t1 inner join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' and t1.branch_code not in (' . $localisations_exclues . ') order by 1, 2';
					}
					else {
						// $requete_liste_localisation = 'select t1.branch_code as code, t2.description as nom from ca_dt_branch as t1 inner join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' order by t1.branch_code';
						$requete_liste_localisation = 'select 0, string_agg(branch_code, \', \' order by branch_code), \'all\' from ca_dt_branch union select  row_number() over(PARTITION BY t1.branch_code ORDER BY t1.branch_code), t1.branch_code, t2.description from 	ca_dt_branch as t1 inner join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' order by 1, 2';
					}
					// echo $requete_liste_localisation . '<br />' ;
					$resultat_liste_localisation = pg_query($requete_liste_localisation) or die('Échec de la requête : ' . pg_last_error());
					if (!$resultat_liste_localisation) {
						echo"An error occurred.\n";
						exit;
					}
					echo '<select id="select1" name="localisation[]" multiple="multiple" size="8" required >';
					while ($row = pg_fetch_row($resultat_liste_localisation)) {	
						if ($row[2] == 'all') {
							echo '<option value="' . $row[1] . '">&nbsp;-- toutes --&nbsp;</option>';
						}
						else {
							echo '<option value="' . $row[1] . '">' . $row[1] . ' : ' . enleve_double_espace($row[2]) . '</option>';
						}		
					}	
					echo '</select>';
				?>
			</div>
			<div class="element"><div class="boite">Sous-localisations (facultatif)</div>
				<?php
					$souslocalisations_exclues = ajoute_guillemets_pour_requete($myIniFile["EXCLUSIONS"]["SOUS_LOCALISATIONS"]);
					if (!empty($souslocalisations_exclues)) {
						$requete_liste_souslocalisation = 'select 0, concat(string_agg(distinct codeonly, \', \' order by codeonly), \', aucune\'), \'all\' FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABSUBLO\' and t2.lang = \'FR\' AND t1.codeonly not in (' . $souslocalisations_exclues . ') union select 1, \'\', \'pas de sous-localisation\' union SELECT row_number() over(PARTITION BY t1.codeonly ORDER BY t1.codeonly), t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABSUBLO\' and t2.lang = \'FR\' AND t1.codeonly not in (' . $souslocalisations_exclues . ') order by 1, 2';
					}
					else {
						$requete_liste_souslocalisation = 'select 0, concat(string_agg(distinct codeonly, \', \' order by codeonly), \', aucune\'), \'all\' FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABSUBLO\' and t2.lang = \'FR\' union select 1, \'\', \'pas de sous-localisation\' union SELECT row_number() over(PARTITION BY t1.codeonly ORDER BY t1.codeonly), t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABSUBLO\' and t2.lang = \'FR\' order by 1, 2';
					}
					// echo $requete_liste_souslocalisation . '<br />' ;
					$resultat_liste_souslocalisation = pg_query($requete_liste_souslocalisation) or die('Échec de la requête : ' . pg_last_error());
					if (!$resultat_liste_souslocalisation) {
						echo"An error occurred.\n";
						exit;
					}
					echo '<select id="select2" name="sous_localisation[]" multiple="multiple" size="8">';
					while ($row = pg_fetch_row($resultat_liste_souslocalisation)) {	
						if ($row[2] == 'all') {
							echo '<option value="' . $row[1] . '">&nbsp;-- toutes --&nbsp;</option>';
						}
						elseif ($row[2] == 'pas de sous-localisation') {
							echo '<option value="pas de sous-localisation">&nbsp;-- pas de sous-localisation --&nbsp;</option>';
						}
						else {
							echo '<option value="' . $row[1] . '">' . $row[1] . ' : ' . enleve_double_espace($row[2]) . '</option>';
						}
					}	
					echo '</select>';
				?>
			</div>
			<div class="element"><div class="boite">Catégories documentaires (obligatoire)</div>
				<?php
					$catdoc_exclues = ajoute_guillemets_pour_requete($myIniFile["EXCLUSIONS"]["CATEGORIES_DOCUMENTAIRES"]);
					if (!empty($catdoc_exclues)) {
						// $requete_liste_catdoc = 'select distinct t1.category_code, t2.description from ca_dt_category as t1 left outer join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' AND t1.category_code not in (' . $catdoc_exclues . ') order by t1.category_code';
						$requete_liste_catdoc = 'select 0, string_agg(distinct category_code, \', \' order by category_code), \'all\' FROM ca_dt_category as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE  t2.lang = \'FR\' AND t1.category_code not in (' . $catdoc_exclues . ') union select  distinct rank() over(PARTITION BY t1.category_code ORDER BY t1.category_code), t1.category_code, t2.description from ca_dt_category as t1 left outer join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' AND t1.category_code not in (' . $catdoc_exclues . ') order by 1 , 2';
					}
					else {
						$requete_liste_catdoc = 'select 0, string_agg(distinct category_code, \', \' order by category_code), \'all\' FROM ca_dt_category as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE  t2.lang = \'FR\' union select  distinct rank() over(PARTITION BY t1.category_code ORDER BY t1.category_code), t1.category_code, t2.description from ca_dt_category as t1 left outer join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' order by 1 , 2';
					}
					// echo $requete_liste_catdoc . '<br />' ;
					$resultat_liste_catdoc = pg_query($requete_liste_catdoc) or die('Échec de la requête : ' . pg_last_error());
					if (!$resultat_liste_catdoc) {
						echo"An error occurred.\n";
						exit;
					}
					echo '<select id="select3" name="categories_documentaires[]" multiple="multiple" size="8" required>';
					while ($row = pg_fetch_row($resultat_liste_catdoc)) {	
						if ($row[2] == 'all') {
							echo '<option value="' . $row[1] . '">&nbsp;-- toutes --&nbsp;</option>';
						}
						else {
							echo '<option value="' . $row[1] . '">' . $row[1] . ' : ' . enleve_double_espace($row[2]) . '</option>';
						}
					}	
					echo '</select>';
				?>
			</div>
			<div class="element"><div class="boite">Collections (facultatif)</div>
				<?php
					$collections_exclues = ajoute_guillemets_pour_requete($myIniFile["EXCLUSIONS"]["COLLECTIONS"]);
					if (!empty($collections_exclues)) {
						$requete_liste_collections = 'SELECT t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABPCOL\' and t2.lang = \'FR\' AND t1.codeonly not in (' . $collections_exclues . ') order by t1.codeonly';
					}
					else {
						$requete_liste_collections = 'SELECT t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABPCOL\' and t2.lang = \'FR\' order by t1.codeonly';
					}								
					// echo $requete_liste_collections . '<br />' ;
					$resultat_liste_collections = pg_query($requete_liste_collections) or die('Échec de la requête : ' . pg_last_error());
					if (!$resultat_liste_collections) {
						echo"An error occurred.\n";
						exit;
					}
					echo '<select id="select4" name="collections[]" multiple="multiple" size="8">';
					while ($row = pg_fetch_row($resultat_liste_collections)) {	
						echo '<option value="' . $row[0] . '">' . $row[0] . ' : ' . enleve_double_espace($row[1]) . '</option>';
					}	
					echo '</select>';
				?>
			</div>
			<div class="element"><div class="boite">Fonds (facultatif)</div>
				<?php
					$fonds_exclus = ajoute_guillemets_pour_requete($myIniFile["EXCLUSIONS"]["FONDS"]);
					if (!empty($fonds_exclus)) {
						$requete_liste_fonds = 'SELECT t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABFOND\' and t2.lang = \'FR\' AND t1.codeonly not in (' . $fonds_exclus . ') order by t1.codeonly';
					}
					else {
						$requete_liste_fonds = 'SELECT t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABFOND\' and t2.lang = \'FR\' order by t1.codeonly';
					}
					// echo $requete_liste_fonds . '<br />' ;
					$resultat_liste_fonds = pg_query($requete_liste_fonds) or die('Échec de la requête : ' . pg_last_error());
					if (!$resultat_liste_fonds) {
						echo"An error occurred.\n";
						exit;
					}
					echo '<select id="select5" name="fonds[]" multiple="multiple" size="8">';
					while ($row = pg_fetch_row($resultat_liste_fonds)) {	
						echo '<option value="' . $row[0] . '">' . $row[0] . ' : ' . enleve_double_espace($row[1]) . '</option>';
					}	
					echo '</select>';
				?>
			</div>
			<div class="element"><div class="boite">Statuts (obligatoire)</div>
				<?php
					$statuts_exclus = ajoute_guillemets_pour_requete($myIniFile["EXCLUSIONS"]["STATUTS"]);
					if (!empty($statuts_exclus)) {
						// $requete_liste_statuts = 'select distinct t1.status, t2.description from ca_dt_status as t1 left outer join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' AND t1.status not in (' . $statuts_exclus . ') order by t1.status';
						$requete_liste_statuts = 'select 0, string_agg(distinct status, \', \' order by status), \'all\' FROM ca_dt_status as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t2.lang = \'FR\' AND t1.status not in (' . $statuts_exclus . ') union select distinct rank() over(PARTITION BY t1.status ORDER BY t1.status), t1.status, t2.description from ca_dt_status as t1 left outer join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' AND t1.status not in (' . $statuts_exclus . ') order by 1, 2';
					}
					else {
						$requete_liste_statuts = 'select 0, string_agg(distinct status, \', \' order by status), \'all\' FROM ca_dt_status as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE  t2.lang = \'FR\' union select distinct rank() over(PARTITION BY t1.status ORDER BY t1.status), t1.status, t2.description from ca_dt_status as t1 left outer join sy_dt_description as t2 on t1.desc_id = t2.desc_id where t2.lang = \'FR\' order by 1, 2';
					}
					// echo $requete_liste_statuts . '<br />' ;
					$resultat_liste_statuts = pg_query($requete_liste_statuts) or die('Échec de la requête : ' . pg_last_error());
					if (!$resultat_liste_statuts) {
						echo"An error occurred.\n";
						exit;
					}
					echo '<select id="select6" name="statuts[]" multiple="multiple" size="8" required>';
					while ($row = pg_fetch_row($resultat_liste_statuts)) {	
						if ($row[2] == 'all') {
							echo '<option value="' . $row[1] . '">&nbsp;-- tous --&nbsp;</option>';
						}
						else {
							echo '<option value="' . $row[1] . '">' . $row[1] . ' : ' . enleve_double_espace($row[2]) . '</option>';
						}
					}	
					echo '</select>';
				?>
			</div>
			<div class="element"><div class="boite">Supports (facultatif)</div>
				<?php
					$supports_exclus = ajoute_guillemets_pour_requete($myIniFile["EXCLUSIONS"]["SUPPORTS"]);
					if (!empty($supports_exclus)) {
						$requete_liste_supports = 'SELECT t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABMED\' and t2.lang = \'FR\' AND t1.codeonly not in (' . $collections_exclues . ') order by t1.codeonly';
					}
					else {
						$requete_liste_supports = 'SELECT t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABMED\' and t2.lang = \'FR\' order by t1.codeonly';
					}								
					// echo $requete_liste_supports . '<br />' ;
					$resultat_liste_supports = pg_query($requete_liste_supports) or die('Échec de la requête : ' . pg_last_error());
					if (!$resultat_liste_supports) {
						echo"An error occurred.\n";
						exit;
					}
					echo '<select id="select7" name="supports[]" multiple="multiple" size="8">';
					while ($row = pg_fetch_row($resultat_liste_supports)) {	
						echo '<option value="' . $row[0] . '">' . $row[0] . ' : ' . enleve_double_espace($row[1]) . '</option>';
					}	
					echo '</select>';
				?>
			</div>
			<div class="element"><div class="boite">Formats (facultatif)</div>
				<?php
					$formats_exclus = ajoute_guillemets_pour_requete($myIniFile["EXCLUSIONS"]["FORMATS"]);
					if (!empty($formats_exclus)) {
						$requete_liste_formats = 'SELECT t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABFOR\' and t2.lang = \'FR\' AND t1.codeonly not in (' . $fonds_exclus . ') order by t1.codeonly';
					}
					else {
						$requete_liste_formats = 'SELECT t1.codeonly, t2.description FROM ca_dt_tables as t1 LEFT outer JOIN SY_DT_DESCRIPTION as t2 ON t1.DESC_ID = t2.DESC_ID WHERE t1.table_ = \'TABFOR\' and t2.lang = \'FR\' order by t1.codeonly';
					}
					// echo $requete_liste_catdoc . '<br />' ;
					$resultat_liste_formats = pg_query($requete_liste_formats) or die('Échec de la requête : ' . pg_last_error());
					if (!$resultat_liste_formats) {
						echo"An error occurred.\n";
						exit;
					}
					echo '<select id="select8" name="formats[]" multiple="multiple" size="8">';
					while ($row = pg_fetch_row($resultat_liste_formats)) {	
						echo '<option value="' . $row[0] . '">' . $row[0] . ' : ' . enleve_double_espace($row[1]) . '</option>';
					}	
					echo '</select>';
				?>
			</div>
			<div class="element"><div class="boite">Optionnel : cocher les cases pour que les valeurs soient prises en compte</div>
				<input type="checkbox" name="bool_date" value="bool_date" class="css-checkbox" id="checkboxG1" />
				<label for="checkboxG1" class="css-label">Date de création des exemplaires (minimum : 2006-04-23) </label>&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;entre le <input type="text" name="date_creation_debut" class="datepicker" placeholder=" AAAA-MM-JJ " /> et le <input type="text" name="date_creation_fin" class="datepicker" placeholder=" AAAA-MM-JJ " />
				<br />
				<input type="checkbox" name="bool_nombre_prets" value="bool_nombre_prets" class="css-checkbox" id="checkboxG2" />
				<label for="checkboxG2" class="css-label">Nombre de prêts  </label>
				<select name="comparaison" size="1" style="max-width:3em;">
					<option value="=">&nbsp;=&nbsp;</option>
					<option value="<">&nbsp;<&nbsp;</option>
					<option value=">">&nbsp;>&nbsp;</option>
				</select>
				<input type="number" name="nombre_prets" placeholder=" nb de prêts " onchange='alert("Ne pas oublier pas de cocher la case pour que la valeur soit prise en compte");' />
				<br />
				<input type="checkbox" name="bool_cote" value="bool_cote" class="css-checkbox" id="checkboxG3" />
				<label for="checkboxG3" class="css-label">Cote commençant par&nbsp;</label><input type="text" name="cote" placeholder=" cote " />		
				<hr />
				<div style="text-align: right;">
					<input type="reset" value="Effacer tout" class="bouton_bleu" />&nbsp;<input type="submit" value="Envoyer" class="bouton_bleu" />
				</div>
			</div>
		</div>
		</form>
		<?php
			include("credits.txt");
			disconnect_from_database();
		?>
	</body>
</html>
