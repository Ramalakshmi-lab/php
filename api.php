<?php 

    require('vendor/autoload.php');

    $con = new MongoDB\Client("mongodb://localhost:27017", [],  [
        "typeMap" => [
            "document" => "array",
            "object" => "array",
            "root" => "array"
        ]
    ]);

    $db = $con -> trains;

    if(isset($_POST["action"]) && $_POST["action"] == "get_stations"){

        $col = $db -> station;

        $keyword = $_POST['keyword'];

        $regex = new MongoDB\BSON\Regex ($keyword,'i');
        // $res = $col -> find(['station_name'=> $regex]);
        // $res = $col -> find(['station_name'=> ['$regex' => $regex]]);
        $res = $col -> find(['$or' => [
                                        ['station_name' => ['$regex' => $regex]], 
                                        ['station_code' => ['$regex' => $regex]]
                                    ]
                            ], ['limit'=> 10]);
        // var_dump($regex->getPattern());
        $stations = [];
        foreach($res as $row)
        {
            $stations[] = $row;
        }
        // echo "<pre>";
        // print_r($stations);
        echo json_encode($stations);
    }

    if(isset($_POST['action']) && $_POST['action'] == "getDetails"){
        
        $col1 = $db->timetable;

        $source_id = (int)$_POST["source_stations"];
        $destination_id = (int)$_POST["destination_station"];

        $res = $col1 -> find(["station_id"=>$source_id]);
        // echo "<pre>";
        // foreach($res as $row){
        //     print_r($row);
        // }
        // print_r($res);
        $trains = [];
        $col2 = $db->train;
        foreach($res as $row){
            $result = $col1->findOne(['$and'=>
                                    [
                                        ["station_id"=>$destination_id],
                                        ["train_id"=>$row["train_id"]]
                                    ]
                                ]);
            if($result)
            {
                if( $row['seq']<$result['seq'] ){
                    $train_rec = $db->train->findOne(['_id'=>$result['train_id']]);
                    $result['train'] = $train_rec;
                    $source= $db->station->findOne(['_id'=>$train_rec["source_id"]]);
                    $result["source_station"] = $source['station_name'];
                    $source= $db->station->findOne(['_id'=>$train_rec["dest_id"]]);
                    $result["destination_station"] = $source['station_name'];
                    $trains[] = $result;
                }
            }
           
        }
        // print_r($trains);


    
        echo json_encode($trains);
    }

?>