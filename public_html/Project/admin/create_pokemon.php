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
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $name =  se($_POST, "name", "", false);
    $pokemon = [];
    if ($name) {
        if ($action === "fetch") {
            try {
                $result = fetch_pokemon($name);
                if ($result) {
                    $pokemon = $result;
                    $pokemon["is_api"] = 1;
                }
            } catch (Exception $e) {
                error_log("Pokemon Not Found" . var_export($e, true));
                flash("Pokemon Not Found ", "danger");
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

    //try/catch for displaying pokemon not found
    

    //insert data
    try {
        //optional options for debugging and duplicate handling
        $opts =
            ["debug" => true, "update_duplicate" => false, "columns_to_update" => []];
        $result = insert("IT202_S24_Pokemon", $pokemon, $opts);
        if (!$result) {
            flash("Unhandled error", "warning");
        } else {
            flash("Created record with id " . var_export($result, true), "success");
        }
    } catch (InvalidArgumentException $e1) {
        error_log("Invalid arg" . var_export($e1, true));
        flash("Invalid data passed", "danger");
    } catch (PDOException $e2) {
        if ($e2->errorInfo[1] == 1062) {
            flash("An entry for this symbol already exists for today", "warning");
        } else {
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    } catch (Exception $e3) {
        error_log("Invalid data records" . var_export($e3, true));
        flash("Invalid data records", "danger");
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
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }

        }
    }
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>