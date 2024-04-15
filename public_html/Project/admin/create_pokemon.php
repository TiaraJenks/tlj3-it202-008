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
if(isset($_POST["action"])){
    $action = $_POST["action"];
    $name = strtolower(se($_POST, "name", "", false));
    if($name){
        if($action === "fetch"){
            $result = fetch_pokemon($name);
            error_log("Data from API" .var_export($result, true));
            //var_dump($result);
            if($result){
                $name = $result; //unsure if this is correct but since I didn't have $quote, I uses $name.
            }
        }else if($action === "create"){
            foreach($_POST as $key => $value){
                if(!in_array($k, ["name", "base_experience", "weight"])){
                    unset($_POST[$k]);
                }
                $name = $_POST; //unsure if this is correct
                error_log("Cleaned up POST: " . var_export($name, true));
            }

    }
}else{
    flash("You must provide a name for the Pokemon", "warning");
}
    //insert data
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
            <?php render_input(["type" => "text", "name" => "Name", "placeholder" => "Pokemon Name", "label" => "Pokemon Name", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "Base Experience", "placeholder" => "Pokemon Base Experience", "label" => "Pokemon Base Experience", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "Weight", "placeholder" => "Pokemon Weight", "label" => "Pokemon Weight", "rules" => ["required" => "required"]]); ?>

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