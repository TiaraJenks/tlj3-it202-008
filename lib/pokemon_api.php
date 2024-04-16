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
    var_dump($result);


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

    /*tried to add new stuff from testApi.php (as seen in API Integration-Admin video)
     but it seems I didnt need a lot of the stuff I had, besides the db,
     so I'm confused what to add after everything above.*/

    //Pushing to database
    if (!empty($result)) {
        $db = getDB();
        
        // Check if the Pokémon already exists in the database
        $stmt_check = $db->prepare("SELECT COUNT(*) FROM IT202_S24_Pokemon WHERE name = :name");
        $stmt_check->bindParam(":name", $result["name"]);
        $stmt_check->execute();
        $existing_count = $stmt_check->fetchColumn();
        
        if ($existing_count == 0) {
            // if the Pokémon doesn't exist, perform the insertion
            $stmt_insert = $db->prepare("INSERT INTO IT202_S24_Pokemon (name, base_experience, weight) VALUES (:name, :base_experience, :weight)");
            $stmt_insert->bindParam(":name", $result["name"]);
            $stmt_insert->bindParam(":base_experience", $result["base_experience"]);
            $stmt_insert->bindParam(":weight", $result["weight"]);
            $stmt_insert->execute();
        } else {
            // If the Pokémon already exists, you may choose to ignore the insertion or handle it differently
            flash ("Pokemon already exists in the database.", 'danger');
        }
    }else {
        flash("Pokemon not found.", 'danger');
    }

    return $result;      

}