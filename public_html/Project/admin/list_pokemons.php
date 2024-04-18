<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

//build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Pokemon Name", "label" => "Pokemon Name", "include_margin"=>false],
    ["type" => "number", "name" => "base_experience", "placeholder" => "Pokemon Base Experience", "label" => "Pokemon Base Experience", "include_margin"=>false],
    ["type" => "number", "name" => "weight", "placeholder" => "Pokemon Weight", "label" => "Pokemon Weight", "include_margin"=>false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["name" => "name", "base_experience" => "base_experience", "weight" => "weight"], "include_margin"=>false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin"=>false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value"=>"10", "include_margin"=>false]
];
error_log("Form data: " . var_export($form, true));


$query = "SELECT id, name, base_experience, weight FROM `IT202_S24_Pokemon` WHERE 1=1";
$params = [];

if (count($_GET) > 0) {
    $keys = array_keys($_GET);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }
    //name
    $name = se($_GET, "name", "", false);
    if(!empty($name)){
        $query .= " AND name like :name";
        $params[":name"] = "%$name%";
    }
    //base experience
    $base_experience = se($_GET, "base_experience", "", false);
    if(!empty($base_experience && $base_experience > -1)){
        $query .= " AND base_experience = :base_experience";
        $params[":base_experience"] = $base_experience;
    }
    //weight
    $weight = se($_GET, "weight", "", false);
    if(!empty($weight) && $weight > -1){
        $query .= " AND weight = :weight";
        $params[":weight"] = $weight;
    }
    //sort and order
    $sort = se($_GET, "sort", "", false); 
    if(!in_array($sort, ["name", "base_experience", "weight"])){
        $sort = "base_experience";
    }
    $order = se($_GET, "order", "", false); 
    if(!in_array($order, ["asc", "desc"])){
        $order = "desc";
    }
    //IMPORTANT make sure you fully validate/trust $sort and $order (sql injection possibility)   
    $query .= " ORDER BY $sort $order";

    try{
        $limit = (int)se($_GET, "limit", "10", false);
    }catch(Exception $e){
        $limit = 10;
    }
    //IMPORTANT make sure you fully validate/trust $limit (sql injection possibility)
    $query .= " LIMIT $limit";

}

$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching pokemon" . var_export($e, true));
    flash("Unhandled Error Occurred", "danger");
}

$table = ["data" => $results, "title" => "List of Pokemons", "ignored_columns" => ["id"], "edit_url" => get_url("admin/edit_pokemon.php"), "delete_url" => get_url("admin/delete_pokemon.php"), "view_url"=> get_url("admin/view_pokemon.php")]
?>
<div class="container-fluid">
    <h3>Pokemon List</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">
                <?php foreach ($form as $k => $v):?>
                    <div class="col-2">
                       <?php render_input($v);?>
                    </div>
                <?php endforeach;?>
                
        </div>
        <?php render_button(["text" => "Search","type" => "submit", "text" => "Filter"]); ?>
    </form>
    <?php render_table($table); ?>
</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>