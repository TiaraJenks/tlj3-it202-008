<?php
// Note: We need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

// Attempt to apply associations
if (isset($_POST["users"]) && isset($_POST["poke"])) {
    $user_ids = $_POST["users"];
    $pokemon_ids = $_POST["poke"];
    if (empty($user_ids) || empty($pokemon_ids)) {
        flash("Both users and pokemons need to be selected", "warning");
    } else {
        // For simplicity, this will be a tad inefficient
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO `IT202-S24-UserPokemons` (user_id, poke_id) VALUES (:uid, :pid)");
        foreach ($user_ids as $uid) {
            foreach ($pokemon_ids as $pid) {
                try {
                    $stmt->execute([":uid" => $uid, ":pid" => $pid]);
                    flash("Updated Association", "success");
                } catch (PDOException $e) {
                    //flash(var_export($e->errorInfo, true), "danger");
                    $stmt2 = $db->prepare("DELETE FROM `IT202-S24-UserPokemons` WHERE user_id = :uid AND poke_id = :pid");
                    $stmt2->execute([":uid" => $uid, ":pid" => $pid]);
                    flash("Deleted association", "success");
                    
                }
            }
        }
    }
}

// Search for users by username
$users = [];
$username = "";
if (isset($_POST["username"])) {
    $username = se($_POST, "username", "", false);
    if (!empty($username)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT u.id, u.username, 
        (SELECT GROUP_CONCAT(p.name) 
            FROM `IT202-S24-UserPokemons` up 
            JOIN `IT202_S24_Pokemon` p ON up.poke_id = p.id 
            WHERE up.user_id = u.id) AS pokemon_name
            FROM Users u WHERE u.username LIKE :username LIMIT 25");
        try {
            $stmt->execute([":username" => "%$username%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $users = $results;
                error_log("A user has emerged: " . var_export($users, true));
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username must not be empty", "warning");
    }
}
// Search for pokemons
$pokemon = [];
$pokemon_name = "";
if (isset($_POST["pokemon_name"])) {
    $pokemon_name = se($_POST, "pokemon_name", "", false);
    if (!empty($pokemon_name)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, name FROM `IT202_S24_Pokemon` WHERE name LIKE :name LIMIT 25");
        try {
            $stmt->execute([":name" => "%$pokemon_name%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $pokemon = $results;
                error_log("A pokemon has emerged: " . var_export($pokemon, true));
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Pokemon must not be empty", "warning");
    }
}
?>

<div class="container-fluid">
    <h1>Assign Associations</h1>
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "username", "placeholder" => "Username Search", "value" => $username]); ?>
        <?php render_input(["type" => "search", "name" => "pokemon_name", "placeholder" => "Pokemon Search", "value" => $pokemon_name]); ?>
        <?php render_button(["text" => "Search", "type" => "submit"]); ?>
    </form>
    <form method="POST">
        <?php if (isset($username) && !empty($username)) : ?>
            <input type="hidden" name="username" value="<?php se($username, false); ?>" />
        <?php endif; ?>
        <table class="table">
            <thead>
                <th>Users</th>
                <th>Pokemon to Assign</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table class="table">
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td>
                                        <?php render_input(["type" => "checkbox","id" => "user_" . se($user, 'id', "", false), "name" => "users[]", "label" => se($user, "username", "", false), "value" => se($user, 'id', "", false)]);?>
                                        
                                    </td>
                                    <td><?php se($user, "pokemon_name", "No Pokemons"); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                    <td>
                        <?php foreach ($pokemon as $poke) : ?>
                            <div>
                                <?php render_input(["type" => "checkbox","id" => "poke_" . se($poke, 'id', "", false), "name" => "poke[]", "label" => se($poke, "name", "", false), "value" => se($poke, 'id', "", false)]);?>

                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php render_button(["text" => "Toggle Entity", "type" => "submit", "color" => "secondary"]); ?>
    </form>

   
</div>

<?php
// Note: We need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>
