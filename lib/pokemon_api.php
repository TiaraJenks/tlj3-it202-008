<?php

function fetch_pokemon($name){
    $data = [];
    $endpoint = "https://pokeapi.co/api/v2/pokemon/". $name;
    $isRapidAPI = false;
    $rapidAPIHost = "alpha-vantage.p.rapidapi.com";
    $result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
    //error_log("decoded res: " . var_export($result, true));
    //var_dump($result);
    //check if $result doesn't have data
    if(empty($result)){
        throw new Exception("Pokemon not found");
    }


    foreach($result as $index => $row) {
            //echo "$index=> $row";
            if(!in_array($index, ["name", "base_experience", "weight"])) {
                unset($result[$index]);
            }
        }
    echo "<pre>";
    //var_dump($result);
    echo "<pre>";
    error_log("transform resp: " . var_export($result, true)); 
        //die();


    return $result;      

}