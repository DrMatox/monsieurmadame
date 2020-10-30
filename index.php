<?php
function readCSV($name){
    return file($name.".csv");
}

function formatCSV($name){
    $collection = readCSV($name);
    $html = "";
    foreach($collection as $line){
        $line = explode(";", $line);
        $status = (empty(trim($line[3])))?'<button data-name="'.$name.'" data-id="'.$line[0].'">add</button>':"&#10003;";
        $html .= "<tr id='".$name."-".$line[0]."'><td><img src='img/$name/$line[0].jpg'></td><td>$line[0]</td><td>$line[1] $line[2]</td><td>$status</td></tr>";
    }
    return $html;
}

function missing($name){
    $collection = readCSV($name);
    //print_r($collection);
    $html = "";
    foreach($collection as $line){
        $line = explode(";", $line);
        if(empty(trim($line[3]))){
            $html .= "<li>$line[1] $line[2]</li>";
        }
    }
    return $html;
}

function countMissing($name){
    $collection = readCSV($name);
    $total = 0;
    $compteur = 0;
    foreach($collection as $line){
        $total++;
        $line = explode(";", $line);
        if(empty(trim($line[3]))){
            $compteur++;
        }
    }
    return $compteur."/".$total;;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monsieur Madame</title>
    <style>
        body{
            display: flex;
        }
        table{
            border-collapse: collapse;
        }
        table td{
            border: 1px solid black;
            padding: 5px;
            
        }

        table td img{
            width: 250px;
            height: 250px;
        }

        td:nth-child(2){
            width: 40px;
        }

        td:nth-child(3){
            width: 400px;
        }

        td:nth-child(4){
            width: 40px;
            text-align: center;
        }
    </style>
</head>
<body>
<table>
    <thead>
        <tr>
            <th>Img</th>
            <th>N°</th>
            <th>Nom</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?= formatCSV("monsieur") ?>
        <?= formatCSV("madame") ?>
    </tbody>
</table>

<div>
        <h2>Liste Manquants</h2>
        <ul>
            <li>
                <li>Monsieur (<?= countMissing("monsieur")?>)</li>
                <ul><?= missing("monsieur") ?></ul>
            </li>
            <li>
                <li>Madame (<?= countMissing("madame")?>)</li>
                <ul><?= missing("madame") ?></ul>
            </li>
        </ul>
</div>

<script>
    let btns = document.querySelectorAll("button");
    btns.forEach(function(btn){
        btn.addEventListener('click', event => {
            let name = btn.dataset.name;
            let id = btn.dataset.id;
            let oReq = new XMLHttpRequest();
            oReq.onload = reqListener;
            oReq.open("get", "update.php?name="+name+"&id="+id);
            oReq.send();
        });
    });

    function reqListener () {
        if (this.readyState === 4) {
            let data = JSON.parse(this.responseText);
            let id = data.name+"-"+data.id;
            let tr = document.getElementById(id);
            tr.children[3].innerHTML = "&#10003;";
        }
        else {
            alert("une erreur est survenue");
        }

    }
</script>
</body>
</html>