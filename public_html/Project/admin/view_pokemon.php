<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php

$id = se($_GET, "id", -1, false);

$pokemon = [];
if($id > -1){
    //fetch
    $db = getDB();
    $query = "SELECT name, base_experience, weight, created, modified FROM `IT202_S24_Pokemon` WHERE id = :id";
    try{
        $stmt = $db->prepare($query);
        $stmt->execute([":id"=>$id]);
        $r = $stmt->fetch();
        if($r){
            $pokemon = $r;
        }
    }
    catch(PDOException $e){
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
}
else{
    flash("Invalid id passed", "danger");
    die(header("Location: " . get_url("admin/list_pokemons.php")));
}

if($pokemon){
    $form = [
        ["type" => "text", "name" => "name", "placeholder" => "Pokemon Name", "label" => "Pokemon Name", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "base_experience", "placeholder" => "Pokemon Base Experience", "label" => "Pokemon Base Experience", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "weight", "placeholder" => "Pokemon Weight", "label" => "Pokemon Weight", "rules" => ["required" => "required"]]
    ];
    $keys = array_keys($pokemon);
    //error_log("keys " . var_export($keys, true));
    
    /*
    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }*/
    
    foreach($form as $k=>$v){
        //error_log("Form data" . var_export($v, true));
        if(in_array($v["name"], $keys)){
           //error_log("IN ARRAY");
            $form[$k]["value"] = $pokemon[$v["name"]];
            //error_log("Value: " . var_export($v, true));
        }
    }

    
    //error_log("Form full data" . var_export($form, true));
}
//TODO handle manual create pokemon
?>
<div class="container-fluid">
    <h3>Pokemon: <?php se($pokemon, "name", "Unknown");?></h3>
    <a href="<?php echo get_url("admin/list_pokemons.php"); ?>" class="btn btn-secondary">Back</a>
    <form method="POST">
        <?php foreach($form as $k=>$v){
            render_input($v);

        }?>

        </form>
    
</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>