create table players
(
	id int auto_increment
		primary key,
	name varchar(100) not null,
	height float not null,
	weight float not null,
	debut_date date not null,
	date_of_birth date not null,
	current_jersey_number int not null,
	current_team int null,
	constraint players___fk_current_team
		foreign key (current_team) references teams (id)
);

create index players__index_name
	on players (name);

