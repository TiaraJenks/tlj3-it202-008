<?php
session_start();
require(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

$id = se($_GET, "id", -1, false);
if ($id < 1) {
    flash("Invalid id passed to delete", "danger");
    redirect("admin/list_pokemons.php");
}

try {
    $db = getDB();
    $query = "DELETE FROM `IT202-S24-UserPokemons` WHERE id = :user_pokemon_id";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":user_pokemon_id", $id);
    $stmt->execute();
    flash("Deleted association successfully", "success");
} catch (PDOException $e) {
    // Handle errors
    error_log("Delete relationship: " . var_export($e, true));
    flash("Potential error with delete relationship", "danger");
}

redirect("admin/pokemon_associations.php");
?>
