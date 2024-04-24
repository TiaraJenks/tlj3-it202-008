<?php
if (!isset($pokemon)) {
    error_log("Using Pokemon partial without data");
    flash("Dev Alert: Pokemon called without data", "danger");
}
?>
<?php if (isset($pokemon)) : ?>
    <!--https://upload.wikimedia.org/wikipedia/commons/5/53/Pok%C3%A9_Ball_icon.svg -->
    <div class="card mx-auto" style="width: 18rem;">
        <?php if(isset($pokemon["username"])):?>
            <div class="card-header">
               Owned by: <?php se($pokemon, "username", "Unknown");?>
            </div>
        <?php endif;?>
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Pok%C3%A9_Ball_icon.svg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title"><?php se($pokemon, "name", "Unknown"); ?> (<?php se($pokemon, "id"); ?>)</h5>
            <div class="card-text">
                <ul class="list-group">
                    <li class="list-group-item">Name: <?php se($pokemon, "name", "Unknown"); ?></li>
                    <li class="list-group-item">Base Experience: <?php se($pokemon, "base_experience", "Unknown"); ?></li>
                    <li class="list-group-item">Weight: <?php se($pokemon, "weight", "Unknown"); ?></li>
                </ul>

            </div>

    <div class="card-body">
                <?php if (isset($pokemon["id"])) : ?>
                    <a class="btn btn-secondary" href="<?php echo get_url("pokemon.php?id=" . $pokemon["id"]); ?>">View</a>
                <?php endif; ?>
                <?php if (!isset($pokemon["user_id"]) || $pokemon["user_id"] === "N/A") : ?>
                    <?php
                    $id = isset($pokemon["id"]) ? $pokemon["id"] : (isset($_GET["id"]) ? $_GET["id"] : -1);
                    ?>
                    <a href="<?php echo get_url('api/purchase_pokemon.php?poke_id=' . $id); ?>" class="card-link">Purchase Pokemon</a>

                <?php else : ?>

                    <div class="bg-danger text-light text-center">Pokemon not available</div>

                <?php endif; ?>
            </div>

        </div>
    </div>
<?php endif; ?>