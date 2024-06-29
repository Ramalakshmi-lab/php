<?php 

    set_time_limit(30000);
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

    $col1 = $db -> station;
    $res = $col1 -> find();

    foreach( $res as $row){
        $stations[ $row['station_code'] ] = $row;
    }

    // echo "<pre>";
    // print_r($stations);

    $col2 = $db -> train;
    $result = $col2->find();

    $trains = [];

    foreach( $result as $res1){
        $trains[ $res1['trains_code'] ] = $res1;
        // echo "<pre>";
        // print_r($res1);
    }

    // echo "<pre>";
    // print_r($trains);

    $col3 = $db->timetable;
    $cnt = 0;
    $line = fgets( $fp,2048 );
    while( $line = fgets( $fp,2048 ) ){

        $cnt++;
        $data = explode(",", $line);
        //print_r( $data );
        //exit;

        $results = $col3 -> insertOne(['_id'=> $cnt, 
                                        "train_id" => $trains [ $data[0] ]['_id'], 
                                        "station_id" => $stations[ $data[3] ]["_id"],
                                        "seq" => $data[2],
                                        "arrival" => $data[5],
                                        "departure" => $data[6],
                                        "distance" => $data[7] 
                                    ]);
    }

    echo "<pre>";
    $res = $col->find();
    foreach($res as $docs){
        print_r($docs);
    }

?>