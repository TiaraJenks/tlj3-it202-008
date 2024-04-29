<?php
session_start();
require(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

if(isset($_GET["user_id"]) && isset($_GET["poke_id"])){
    $user_id = $_GET["user_id"];
    $poke_id = $_GET["poke_id"];
    $query = "DELETE FROM `IT202-S24-UserPokemons` WHERE user_id = :user_id AND poke_id = :poke_id";
    $db = getDB();
    try{
        $stmt = $db->prepare($query);
        $stmt->execute([":user_id" => $user_id, ":poke_id" => $poke_id]);
        flash("Successfully removed your Pokemon!", "success");
    }catch(PDOException $e){
        error_log("Error removing Pokemon association: " . var_export($e, true));
        flash("Error removing Pokemon association", "danger");
    }
}

redirect("my_pokemons.php");
?>

