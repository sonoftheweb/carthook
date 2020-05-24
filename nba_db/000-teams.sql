create table teams
(
	id int auto_increment
		primary key,
	team_name varchar(200) not null,
	team_nickname varchar(200) null
);

create index teams__index_name_nick
	on teams (team_name, team_nickname);

