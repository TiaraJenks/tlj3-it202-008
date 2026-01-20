<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
?>

<?php

//TODO handle pokemon fetch
if(isset($_POST["name"])){
    /* anything commented off may be deleted
    $action = $_POST["action"];
    $name = strtolower(se($_POST, "name", "", false));
    $pokemon = []; //may change name
    if($name){
        if($action === "fetch"){
            $result = fetch_pokemon($name);
            error_log("Data from API" .var_export($result, true));
            //var_dump($result);
            if($result){
                $pokemon = $result; 
            }
        }else if($action === "create"){*/
            foreach($_POST as $k => $v){
                if(!in_array($k, ["name", "base_experience", "weight"])){
                    unset($_POST[$k]);
                }
                $pokemon = $_POST; //unsure if this is correct
                error_log("Cleaned up POST: " . var_export($pokemon, true));
            }

       /* }
    }else{
        flash("You must provide a name for the Pokemon", "warning");
    }*/

    //insert data
    $db = getDB();
    $query = "UPDATE `IT202_S24_Pokemon` SET ";
    //$query = "WHERE 'id' = :id";
    $params = [];
    $columns = [];
    //per record
    foreach ($pokemon as $k=>$v){
        //array_push($columns, "'$k");
        if($params){
            $query .= ",";
        }
        //be sure $k is trusted as this is a source of SQL injection
        $query .= "$k=:$k";
        $params[":$k"] = $v;
        
    }
    $params[":id"] = $_GET["id"];
    $query .= " WHERE `id` = :id";
    
    //$query .= "(" . join(",", $columns) . ")";
    //$query .= "VALUES (" . join(",", array_keys($params)) . ")";
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try{
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record " . $db->lastInsertId(), "success");
        } catch(PDOException $e){
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }

}

$id = se($_GET, "id", -1, false);
$pokemon = [];
if($id > -1){
    //fetch
    $db = getDB();
    $query = "SELECT name, base_experience, weight FROM `IT202_S24_Pokemon` WHERE id = :id";
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

if($pokemon){
    $form = [
        ["type" => "text", "name" => "name", "placeholder" => "Pokemon Name", "label" => "Pokemon Name", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "base_experience", "placeholder" => "Pokemon Base Experience", "label" => "Pokemon Base Experience", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "weight", "placeholder" => "Pokemon Weight", "label" => "Pokemon Weight", "rules" => ["required" => "required"]]
    ];
    $keys = array_keys($pokemon);
    //error_log("keys " . var_export($keys, true));
    
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
    <h3>Edit a Pokemon</h3>
    <a href="<?php echo get_url("admin/list_pokemons.php"); ?>" class="btn btn-secondary">Back</a>
    <form method="POST">
        <?php foreach($form as $k=>$v){
            render_input($v);

        }?>

            <?php render_button(["text" => "Create", "type" => "submit", "text"=>"Update"]); ?>
        </form>
    
</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>