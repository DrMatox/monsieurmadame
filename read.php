<?php

$name = $_GET['name'];
$collection = file("data".DIRECTORY_SEPARATOR.$name.".csv");
$html = "";
foreach($collection as $line){
    $line = explode(";", $line);
    $status = (empty(trim($line[3])))?'<button data-name="'.$name.'" data-id="'.$line[0].'">add</button>':"&#10003;";
    $html .= "<tr id='".$name."-".$line[0]."'><td><img src='img/$name/$line[0].jpg'></td><td>$line[0]</td><td>$line[1] $line[2]</td><td>$status</td></tr>";
}
echo $html;