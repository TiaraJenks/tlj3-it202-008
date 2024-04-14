<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["name"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = [];
    $endpoint = "https://pokeapi.co/api/v2/pokemon/". $_GET["name"];
    $isRapidAPI = false;
    $rapidAPIHost = "alpha-vantage.p.rapidapi.com";
    $result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }
}'];*/
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        if ($result["response"] === "Not Found") {
            error_log("Pokemon not found in the API response");
            
        } else {
        $result = json_decode($result["response"], true);
        error_log("Decoded Response: " . var_export($result, true));
        }
    //Unsetting unwanted data
        foreach ($result as $key => $value) {
            if (!in_array($key, ["name", "base_experience", "weight"])) {
                unset($result[$key]);
            }
        }
        //Putting the data in JSON Pretty to view data easier here
        if (!empty($result)) {
            $json = json_encode($result, JSON_PRETTY_PRINT);
            file_put_contents('pokemon_info.json', $json);
        }

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

} else {
    $result = [];
}

}

?>
<div class="container-fluid">
    <h1>Pokemon Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Pokemon Name</label>
            <input name="name" />
            <input type="submit" value="Fetch Pokemon" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $stock) : ?>
                <pre>
                    <?php var_export($stock);?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");