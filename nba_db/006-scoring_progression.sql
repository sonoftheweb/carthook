create table scoring_progression
(
	id int auto_increment
		primary key,
	quarter_id int not null,
	time timestamp not null,
	points int not null,
	player_game_stats int not null,
	constraint scoring_progression___fk_quarters
		foreign key (quarter_id) references quarters (id)
			on delete cascade
);

