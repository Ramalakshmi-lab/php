<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js"></script> -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <title>Document</title>
</head>
<body>
    <div class="container" style="margin-top:50px;">
        <div style="display:flex; justify-content:space-evenly; border: 1px #94908f black; box-shadow: 2px 2px 2px 2px #d9d5d4; padding:10px;">
            <div style="display:flex; ">
                <div style="display:flex; justify-content:space-evenly;">
                    <div><label class="form-label">Source Station</label></div>
                    <div><input type = "text" class="form-control" id="source" onkeyup="source_station(event)"></div>
                </div>
                <div style="display:flex; justify-content:space-evenly;">
                    <div><label class="form-label">Destination Station</label></div>
                    <div><input type="text" id="destination" class="form-control" onkeyup="source_station(event)"></div>
                </div>
            </div>
            <div>
                <input type="submit" value="Search Trains" onclick="search_trians()" class="btn btn-success">
            </div>
        </div>
    </div>
    <table class="table table-bordered" style="margin-top:50px;" id="trains_table">
        <tr>
            <th onclick="sortData(event)" id="train_code">Train</td>
            <th onclick="sortData(event)" id="train_name">Train Name</td>
            <th onclick="sortData(event)" id="arrival">Arrival</td>
            <th onclick="sortData(event)" id="departure">Departure</td>
            <td onclick="sortData(event)" id="distance">Distance</td>
        </tr>
        <tbody id="trains_list" >
        </tbody>
    </table>
    <script>
        var macthed_trains=[];
        var source_sta;
        var destination_sta;
        function source_station(event){
            if(event.target.value ==''){
                return false;
            }
            $.ajax({
                url: "api.php",
                method: "POST",
                data: {
                    action: "get_stations",
                    keyword: event.target.value
                },
                success: function(response){
                    console.log(response);
                    var stations = JSON.parse(response);
                    var stationList = [];
                    console.log("----",stations.length,"-----");
                    for (let i = 0; i < stations.length; i++) {
                        stationList.push({
                            label: `${stations[i]['station_code']} ${stations[i]['station_name']}`,
                            value: stations[i]['station_code'],
                            id: stations[i]['_id']
                        });
                    }

                    console.log(stationList);
                    var ent = event.target.id;
                    console.log(ent);
                    // debugger;
                    $('#' + ent).autocomplete({
                        source: stationList,
                        select: function (event, ui) {
                            if (ent == "source") {
                                source_sta = ui.item.id;
                                console.log('Source Station ID:', source_sta);
                            } else if (ent == "destination") {
                                destination_sta = ui.item.id;
                                console.log('Destination Station ID:', destination_sta);
                            }
                        },
                    });
                }
            })
        }

        function search_trians() {
             var details = {
                    source_stations: source_sta,
                    destination_station: destination_sta,
                    action: "getDetails"
                };
                console.log( details);

                $.ajax({
                    url: "api.php",
                    type: "POST",
                    data: details,
                    success: function(response) {
                        console.log(response);
                        matched_trains = JSON.parse(response);
                        generate_trains_list();
                        
                }
            });
        } 

        function generate_trains_list(){
            var str = "";
            for(var i=0;i<matched_trains.length;i++){
                str = str + `<tr>
                    <td>`+matched_trains[i]['train']['trains_code']+`</td>
                    <td>`+matched_trains[i]['train']['train_name']+`</td>
                    <td>`+matched_trains[i]['arrival']+`</td>
                    <td>`+matched_trains[i]['departure']+`</td>
                    <td>`+matched_trains[i]['distance']+`</td>
                </tr>`;
            }
            document.getElementById("trains_list").innerHTML = str;
        }
    </script>
</body>
</html>
