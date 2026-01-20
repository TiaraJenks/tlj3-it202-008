CREATE TABLE IF NOT EXISTS  `IT202-S24-UserPokemons`
(
    `id`         int auto_increment not null,
    `user_id`    int,
    `poke_id`  int,
    `is_active`  TINYINT(1) default 1,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`poke_id`) REFERENCES `IT202_S24_Pokemon`(`id`),
    UNIQUE KEY (`poke_id`) -- my relationship is one to many
)