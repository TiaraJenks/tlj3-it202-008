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
    var_dump($result);

    foreach($result as $index => $row) {
        foreach($row as $key => $value) {
            error_log(var_export($key, true). "=>" .var_export($value, true));
            
            if(!in_array($key, ["name", "base_experience", "weight"])) {
                unset($result[$index][$key]);
            }
        }
    }
    

    return $result;      
}
