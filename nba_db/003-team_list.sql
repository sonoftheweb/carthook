create table team_list
(
	`int` int auto_increment
		primary key,
	team_id int not null,
	player_id int not null,
	date_enlisted date not null,
	constraint team_list___fk_players
		foreign key (player_id) references players (id)
			on delete cascade,
	constraint team_list___fk_teams
		foreign key (team_id) references teams (id)
			on delete cascade
);

