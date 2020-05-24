create table quarters
(
	id int auto_increment
		primary key,
	home_points int not null,
	away_points int not null,
	game_id int not null,
	quarter_length int default 12 not null comment 'seconds per quarter (plus extra time, for sports like football)',
	constraint quarters___fk_games
		foreign key (game_id) references games (id)
			on delete cascade
);

