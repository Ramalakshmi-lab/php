<?php 

    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_USER_WARNING & ~E_USER_NOTICE);
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

    $col = $db -> station;
    $res = $col -> find();


    foreach( $res as $row){
        $stations[ $row['station_code'] ] = $row;
    }
    // echo "<pre>";
    // print_r($stations);
    $trains = [];

    $cnt = 0;
	$line = fgets( $fp,2048 );
	while( $line = fgets($fp,2048) ){
		$data = explode(",", $line);
		$trains[ $data[0] ] = [
			"name"=>$data[1],
			"source"=>$data[8],
			"dest"=>$data[10]
		];
	}

    // echo "<pre>";
    // print_r($trains);
    // print_r($stations);
    $col = $db->train;

	foreach( $trains as $train_code=>$details ){

        $cnt++;
        $res = $col -> insertOne(["_id"=> $cnt, "trains_code" => $train_code, "train_name" => $details['name'], "source_id"=>  $stations[ $details['source'] ]['_id'] , "dest_id"=> $stations[ $details['dest'] ]['_id'] ]);

	}

    echo "<pre>";
    $res = $col->find();
    foreach($res as $docs){
        print_r($docs);
    }


?>