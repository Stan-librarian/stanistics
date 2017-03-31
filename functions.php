<?php
	function connect_to_database() // connexion à la base postgresql
		{
			$myIniFile = parse_ini_file ("config.ini", TRUE);
			$host = $myIniFile["DATABASE"]["HOST"];
			$port = $myIniFile["DATABASE"]["PORT"];
			$dbname = $myIniFile["DATABASE"]["DATABASE"];
			$user = $myIniFile["DATABASE"]["USER"];
			$password = $myIniFile["DATABASE"]["PASSWORD"];
			$connection_string ='host=' . $host . ' port=' . $port .' dbname=' . $dbname . ' user=' . $user .' password=' . $password;
			$db_connection = pg_connect($connection_string) or die('Connexion impossible : ' . pg_last_error());
			return $db_connection;
		}
	
	function disconnect_from_database() // déconnexion de la base postgresql
	// pg_close ([ resource $connection ] )
	// Lorsque $connection n'est pas présent, la connexion par défaut est utilisée. La connexion par défaut est la dernière connexion faite par pg_connect() ou pg_pconnect(). 
		{
			pg_close();
		}
		
	function ajoute_guillemets_pour_requete($entree) 
		{
			if(isset($entree) && !empty($entree)) {
				$entree = str_replace(', ', ',', $entree);
				$entree = str_replace(',', '\',\'', $entree);
				$entree = '\'' . $entree . '\'';
				return $entree;
			}
		}
		
	function effacecsv() // efface les fichiers avec l'extension "csv" créés plus de 24 heures auparavant
		{
			// source : http://www.developpez.net/forums/d666798/php/langage/fichiers/petit-script-permet-supprimer-fichiers-d-dossier/
			$rep=opendir(".");
			$i=0;
			while($file = readdir($rep)){
				// if(pathinfo($file)['extension'] == 'csv'){
				if(substr($file, strlen($file)-3, 3) == 'csv'){
					$age =  time() - filectime($file); // âge en secondes - NB : 1 jour = 60*60*24 = 86400 secondes
					// echo 'Fichier : ' . $file . ' -> age : ' . $age . ' s ; extension : ' . substr($file, strlen($file)-3, 3) ;
					// echo '<br />';
					if($age > 86400){
						unlink($file);
						$i++;
					}
				}
			}
			// affichage du nb de fichiers supprimés :
			// if($i>1){$text=$i." fichiers ont été supprimés";}
			// elseif($i==1){$text="1 fichier a été supprimé";}
			// elseif($i==0){$text="Aucun fichier n'a été supprimé";}
			// echo $text .'<br />';
		}
			
	function ajoute_guillemets($entree) 
		{
			if(isset($entree) && !empty($entree)) {
				$entree = "'".str_replace(', ', '\', \'', $entree)."'";
				return $entree;
			}
		}
		
	function enleve_double_espace($entree) 
		{
			if(isset($entree) && !empty($entree)) {
				$entree = preg_replace('!\s+!', ' ', $entree);
				return $entree;
			}
		}
			
	function diacritiques($chaine) 
		{
			if(isset($chaine) && !empty($chaine) && ($chaine != "0")) {
			//diacritiques : éèêàâêîôûÉÈçÀÇ etc
			$chaine = str_replace('Ã¢', 'â', $chaine);
			$chaine = str_replace('Ã¢', 'â', $chaine);
			$chaine = str_replace('Ã¡', 'á', $chaine);
			$chaine = str_replace('Ã¤', 'ä', $chaine);
			$chaine = str_replace('Ã ', 'à', $chaine);
			$chaine = str_replace('Ã€', 'À', $chaine);
			$chaine = str_replace('Ã?', 'À', $chaine);
			$chaine = str_replace('Ã©', 'é', $chaine);
			$chaine = str_replace('Ã¨', 'è', $chaine);
			$chaine = str_replace('Ãª', 'ê', $chaine);
			$chaine = str_replace('Ã«', 'ë', $chaine);
			$chaine = str_replace('Ã‰', 'É', $chaine);
			$chaine = str_replace('Ãˆ', 'È', $chaine);
			$chaine = str_replace('Ã®', 'î', $chaine);
			$chaine = str_replace('Ã¯', 'ï', $chaine);
			$chaine = str_replace('Ã­', 'í', $chaine);
			$chaine = str_replace('Ã´', 'ô', $chaine);
			$chaine = str_replace('Ã¶', 'ö', $chaine);
			$chaine = str_replace('Ã¶', 'ö', $chaine);
			$chaine = str_replace('Ã»', 'û', $chaine);
			$chaine = str_replace('Ã¹', 'ù', $chaine);
			$chaine = str_replace('Ãº', 'ú', $chaine);
			$chaine = str_replace('Ã¼', 'ü', $chaine);
			$chaine = str_replace('Ã§', 'ç', $chaine);
			$chaine = str_replace('Ã‡', 'Ç', $chaine);
			$chaine = str_replace('Å?', 'Œ', $chaine);
			$chaine = str_replace('Ã?', 'À', $chaine);
			$chaine = str_replace('\u0152', 'Œ', $chaine);
			$chaine = str_replace('\u0153', 'œ', $chaine);
			$chaine = str_replace('Ã?', 'É', $chaine);
			return $chaine;
			}
		}
			
		function startsWith($haystack, $needle)
			{
				 $length = strlen($needle);
				 return (substr($haystack, 0, $length) === $needle);
			}

		function endsWith($haystack, $needle)
			{
				$length = strlen($needle);
				if ($length == 0) {
					return true;
				}

				return (substr($haystack, -$length) === $needle);
			}
?>