<?php
$missing = countMissing();

function readCSV($name){
    return file("data".DIRECTORY_SEPARATOR.$name.".csv");
}

function missing($name){
    $collection = readCSV($name);
    $html = "";
    foreach($collection as $line){
        $line = explode(";", $line);
        if(empty(trim($line[3]))){
            $html .= "<li id='".$line[0]."-".strtolower($line[1])."'>$line[1] $line[2]</li>";
        }
    }
    return $html;
}

function countMissing(){
    $array = [];
    $collection = readCSV('collection');
    foreach($collection as $collectionline){
        $filename = explode(";", $collectionline);
        $filename[1] = str_replace(["\n", "\r", "\t"],"",$filename[1]);
        $file = readCSV($filename[1]);
        $total = 0;
        $compteur = 0;
        foreach($file as $line){
            $total++;
            $line = explode(";", $line);
            if(empty(trim($line[3]))){
                $compteur++;
            }
        }
        $array[] = array("name"=>$filename[1], "compteur"=> '<span id="c'.$filename[1].'">'.$compteur."</span>/".$total, "missing" => missing($filename[1]));

    }
    return $array;
}

function menu(){
    $collection = readCSV('collection');
    $html = "";
    foreach($collection as $line){
        $line = explode(";", $line);
        $html .= "<li><a data-link='{$line[1]}'>".ucfirst(str_replace(['_'], [' '], $line[1]))."</a></li>";
    }
    return $html;
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
            max-width: 100vw;
            max-height: 100vh;
        }

        header{
            height: 10vh;
        }

        nav ul{
            display: flex;
            margin: 0;
        }

        nav ul li{
            list-style: none;
            padding: 5px 10px;
            border: 1px solid lightgrey;
            border-radius: 5px 5px 0 0;
        }

        nav ul li:hover{
            background-color: lightgrey;
        }

        .container {
            height: 80vh;
            max-height: 80vh;
            overflow: auto;
            border: 1px solid lightgrey;
            display: grid;
            grid-template-columns: 50% 50%;
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
        .missing{
            margin-left: 15px;
        }
    </style>
</head>
<body>
<header>
    <h1>Monsieur Madame, ma collection</h1>
</header>
<nav>
    <ul>
        <?= menu() ?>
    </ul>
</nav>
<div class="container">
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
        </tbody>
    </table>
    <div class="missing">
        <div>
            <h2>Liste Manquants</h2>
            <ul>
                <?php foreach($missing as $m): ?>
                    <li><?= $m['name'] ?>(<?= $m['compteur']?>)</li>
                    <ul><?= $m['missing'] ?></ul>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<script>
    const containerTable = document.querySelector('.container table');
    const tbody = document.querySelector('.container table tbody');
    function updateBtn(){
        let btns = document.querySelectorAll("button");
        btns.forEach(function(btn){
        btn.addEventListener('click', event => {
            let name = btn.dataset.name;
            let id = btn.dataset.id;
            let oReq = new XMLHttpRequest();
            oReq.onload = reqListener;
            oReq.open("get", "update.php?name="+name+"&id="+id);
            oReq.send();
            let compteur = document.getElementById('c'+name);
            compteur.innerText = compteur.innerText - 1;
        });
    });
    }
    

    let links = document.querySelectorAll("nav ul li a");
    links.forEach(function(link){
        link.addEventListener('click', event => {
            event.preventDefault();
            requestCSV(link.dataset.link);
        })
    })

    function requestCSV(link){
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = handleStateChange;
        xhr.open("GET", "read.php?name="+link);
        xhr.send();
        function handleStateChange() {
        if (xhr.readyState == 4 &&
            xhr.status >= 200 &&
            xhr.status < 300) {
            showData(xhr.responseText);
        }
    }
    }

    function showData(data) {
        tbody.innerHTML = data;
        updateBtn();
      }

    function reqListener () {
        if (this.readyState === 4) {
            let data = JSON.parse(this.responseText);
            let id = data.name+"-"+data.id;
            let tr = document.getElementById(id);
            tr.children[3].innerHTML = "&#10003;";
            let lineToDelet = document.getElementById(data.id+'-'+data.name);
            lineToDelet.remove();
        }
        else {
            alert("une erreur est survenue");
        }

    }
    updateBtn();
</script>
</body>
</html>