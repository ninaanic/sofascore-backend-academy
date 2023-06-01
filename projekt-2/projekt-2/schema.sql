CREATE TABLE country (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL
);

CREATE TABLE score (
    id          SERIAL PRIMARY KEY,
    total       INTEGER,
    period1     INTEGER,
    period2     INTEGER,
    period3     INTEGER,
    period4     INTEGER,
    overtime    INTEGER
);

CREATE TABLE sport (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	slug 		VARCHAR(255) NOT NULL,
	external_id INTEGER NOT NULL UNIQUE     -- id u Provideru
);

CREATE TABLE tournament (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	slug 		VARCHAR(255) NOT NULL,
	external_id INTEGER NOT NULL UNIQUE,
    number_of_competitors INTEGER, 
    head_to_head_count INTEGER,
	sport_id 	INTEGER NOT NULL REFERENCES sport(id) ON DELETE CASCADE, 
    country_id INTEGER NOT NULL REFERENCES country(id) ON DELETE CASCADE
);

CREATE TABLE team (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	manager_name VARCHAR(255),
    venue       VARCHAR(255),
	external_id INTEGER NOT NULL UNIQUE,
	sport_id 	INTEGER NOT NULL REFERENCES sport(id) ON DELETE CASCADE,
    country_id INTEGER NOT NULL REFERENCES country(id) ON DELETE CASCADE
);

CREATE TABLE player (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	slug 		VARCHAR(255) NOT NULL,
    position 	VARCHAR(255),
    date_of_birth TIMESTAMP,
	external_id INTEGER NOT NULL UNIQUE,
    sport_id 	INTEGER NOT NULL REFERENCES sport(id) ON DELETE CASCADE,
	team_id 	INTEGER NOT NULL REFERENCES team(id) ON DELETE CASCADE,
    country_id INTEGER NOT NULL REFERENCES country(id) ON DELETE CASCADE
);

CREATE TABLE event (
	id 				SERIAL PRIMARY KEY,
	slug            VARCHAR(255) NOT NULL,
	start_date 		TIMESTAMP NOT NULL,
    status          VARCHAR(255) NOT NULL, -- enum
    winner_code     VARCHAR(255), 
    round           INTEGER NOT NULL,
	external_id 	INTEGER NOT NULL UNIQUE,
    home_score_id	INTEGER NOT NULL REFERENCES score(id) ON DELETE CASCADE,
	away_score_id	INTEGER NOT NULL REFERENCES score(id) ON DELETE CASCADE,
	home_team_id 	INTEGER NOT NULL REFERENCES team(id) ON DELETE CASCADE,
	away_team_id 	INTEGER NOT NULL REFERENCES team(id) ON DELETE CASCADE,
	tournament_id 	INTEGER NOT NULL REFERENCES tournament(id) ON DELETE CASCADE
);

CREATE TABLE standings (
	id 				SERIAL PRIMARY KEY,
	position 		INTEGER NOT NULL,
	matches 		INTEGER NOT NULL,
	wins 			INTEGER NOT NULL,
	looses	 		INTEGER NOT NULL,
	draws	 		INTEGER NOT NULL,
	scores_for 		INTEGER NOT NULL,
	scores_against 	INTEGER NOT NULL,
	points			INTEGER NOT NULL,
	tournament_id 	INTEGER NOT NULL REFERENCES tournament(id) ON DELETE CASCADE,
	team_id 		INTEGER NOT NULL REFERENCES team(id) ON DELETE CASCADE
);	