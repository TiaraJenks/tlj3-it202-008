<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php

//TODO handle pokemon fetch
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $name =  strtoupper(se($_POST, "name", "", false));
    $pokemon = [];
    if ($name) {
        if ($action === "fetch") {
            $result = fetch_pokemon($name);
            //error_log("Data from API" . var_export($result, true));
            if ($result) {
                $pokemon = $result;
            }
        } else if ($action === "create") {
            foreach ($_POST as $k => $v) {
                if (!in_array($k, ["name", "base_experience", "weight"])) {
                    unset($_POST[$k]);
                }
                $pokemon = $_POST;
                error_log("Cleaned up POST: " . var_export($pokemon, true));
            }
        }
    } else {
        flash("You must provide a name for the Pokemon.", "warning");
    }
    
    //insert data
    $db = getDB();
    $query = "INSERT INTO `IT202_S24_Pokemon` ";
    $columns = [];
    $params = [];

    //per record

    foreach($pokemon as $k => $v){
        array_push($columns, "$'k'");
        $params[":$k"] = $v;
    }
    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",", array_keys($params)) . ")";
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try{
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record " . $db->lastInsertId(), "success");
    } catch (PDOException $e){
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }

}

//TODO handle manual create pokemon
?>
<div class="container-fluid">
    <h3>Create or Fetch a Pokemon</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-dark" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-dark" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "search", "name" => "name", "placeholder" => "Pokemon Name", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit"]); ?>
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "text", "name" => "name", "placeholder" => "Pokemon Name", "label" => "Pokemon Name", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "base_experience", "placeholder" => "Pokemon Base Experience", "label" => "Pokemon Base Experience", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "weight", "placeholder" => "Pokemon Weight", "label" => "Pokemon Weight", "rules" => ["required" => "required"]]); ?>

            <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
            <?php render_button(["text" => "Create", "type" => "submit"]); ?>
        </form>
    </div>
</div>

<script> 
function switchTab(tab){
    let target = document.getElementById(tab);
    if(target){
        let eles = document.getElementsByClassName("tab-target");
        for(let ele of eles){
            ele.style.display = (ele.id === tab)?"none" : "block";
        }

    }
}
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>