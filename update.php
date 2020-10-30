<?php
if(!isset($_GET['name']) && !isset($_GET['id'])){
    $error[] = "Données imcomplete";
}

$row = 0;
$update = "";
//on stocke en variable le séparateur de csv utilisé
$separator = ";";

//on crée deux variables contenant les index des colonnes à lire / modifier
$idx_nom = 0;
$idx_rubrique = 1;

//on ouvre le fichier en écriture
if (($handle = fopen($_GET["name"].".csv", "r")) !== FALSE) 
{
	
	//on parcours le fichier ligne à ligne, en stockant dans un tableau les donnée
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) 
    {
    	//on ne commande qu'à la deuxième ligne, su la première contient les entêtes de colonnes
    	if ($row != 0)
    	{
            if($data[0]===$_GET["id"]){
                $data[3] = "TRUE";
            }
		}
		
		$update	.= implode($separator,$data)."\r\n";
		$row++;
	}
    
    fclose($handle);
}

//on ouvre le fichier en ecriture et on le met à jour
$ouvre=fopen($_GET["name"].".csv","w+");
fwrite($ouvre,$update);
fclose($ouvre);

header('content-type:application/json');
echo json_encode($_GET);