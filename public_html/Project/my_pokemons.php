<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");


//build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Pokemon Name", "label" => "Pokemon Name", "include_margin"=>false],
    ["type" => "number", "name" => "base_experience", "placeholder" => "Pokemon Base Experience", "label" => "Pokemon Base Experience", "include_margin"=>false],
    ["type" => "number", "name" => "weight", "placeholder" => "Pokemon Weight", "label" => "Pokemon Weight", "include_margin"=>false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["name" => "name", "base_experience" => "base_experience", "weight" => "weight"], "include_margin"=>false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin"=>false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value"=>"10", "include_margin"=>false]
];
//error_log("Form data: " . var_export($form, true));


$query = "SELECT p.id, name, base_experience, weight, user_id FROM `IT202_S24_Pokemon` p
JOIN `IT202-S24-UserPokemons` up ON p.id = up.poke_id
WHERE user_id = :user_id";
$params = [":user_id"=>get_user_id()];

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
    //tell my sql I care about the data from table "p"
    if($sort === "base_experience" || $sort = "weight"){
        $sort = "p." . $sort;
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

$table = ["data" => $results, "title" => "List of Pokemons", "ignored_columns" => ["id"], "view_url"=> get_url("pokemon.php")]
?>
<div class="container-fluid">
    <h3>My Pokemons</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">
                <?php foreach ($form as $k => $v):?>
                    <div class="col-2">
                       <?php render_input($v);?>
                    </div>
                <?php endforeach;?>
                
        </div>
        <?php render_button(["text" => "Search","type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 rows-cols-xl-5 rows-cols-xxl-6 g-4">
    <?php foreach($results as $pokemon):?>
        <div class="col">
            <?php render_pokemon_card($pokemon);?>
        </div>
        <?php endforeach;?>
    </div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>