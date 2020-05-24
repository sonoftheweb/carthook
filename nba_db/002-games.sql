create table games
(
	id int auto_increment
		primary key,
	home_team int not null,
	away_team int not null,
	date datetime not null,
	venue text not null,
	total_points int default 0 not null,
	constraint games___fk_away_teams
		foreign key (away_team) references teams (id)
			on delete cascade,
	constraint games___fk_home_teams
		foreign key (home_team) references teams (id)
			on delete cascade
);

