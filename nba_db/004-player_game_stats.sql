create table player_game_stats
(
	id int auto_increment
		primary key,
	player_id int not null,
	game_id int not null,
	fouls int default 0 not null,
	steals int default 0 not null,
	dunks int default 0 not null,
	three_shots int default 0 not null,
	two_shots int default 0 null,
	constraint player_game_stats___fk_games
		foreign key (game_id) references games (id)
			on delete cascade,
	constraint player_game_stats___fk_players
		foreign key (player_id) references players (id)
			on delete cascade
);

