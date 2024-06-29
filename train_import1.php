<?php 

    require("vendor/autoload.php");

    $con = new MongoDB\Client("mongodb://localhost:27017", [],  [
        "typeMap" => [
            "document" => "array",
            "object" => "array",
            "root" => "array"
        ]
    ]);

    $db = $con -> trains;

    $file = "Train_details_22122017.csv";

    $fp = fopen($file, "r");

    $stations = [];

    $line = fgets($fp,2048);

    $cnt = 0;

    while( $line = fgets($fp, 2048) )
    {
        $data = explode(",", $line);
        $stations[ $data[3] ] = $data[4];
    }

    
    $col = $db->station;

    foreach( $stations as $station_code => $station_name)
    {
        $cnt++;
        $res = $col ->insertOne(["_id"=> $cnt, "station_code"=> $station_code, "station_name"=>$station_name]);

    }
    
    echo "<pre>";
    $res = $col->find();
    foreach($res as $docs){
        print_r($docs);
    }
?>

