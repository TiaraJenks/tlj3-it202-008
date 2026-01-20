<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
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
    redirect("admin/list_pokemons.php");
}


//TODO handle manual create pokemon
?>
<div class="container-fluid">
    <h3>Pokemon: <?php se($pokemon, "name", "Unknown");?></h3>
    <div>
        <a href="<?php echo get_url("admin/list_pokemons.php"); ?>" class="btn btn-secondary">Back</a>
    </div>    
    
    <?php render_pokemon_card($pokemon);?>
    
</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>