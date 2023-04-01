CREATE TABLE post
(
    id           SERIAL PRIMARY KEY NOT NULL,
    title        VARCHAR(100)       NOT NULL,
    text         TEXT               NOT NULL,
    status       VARCHAR(50)        NOT NULL,
    date_created TIMESTAMP          NOT NULL
);

CREATE TABLE sport (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	slug 		VARCHAR(255) NOT NULL,
	external_id CHAR(36) NOT NULL UNIQUE
);

CREATE TABLE tournament (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	slug 		VARCHAR(255) NOT NULL,
	external_id CHAR(36) NOT NULL UNIQUE,
	sport_id 	INTEGER NOT NULL REFERENCES sport(id) ON DELETE CASCADE
);

CREATE TABLE team (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	slug 		VARCHAR(255) NOT NULL,
	external_id CHAR(36) NOT NULL UNIQUE,
	sport_id 	INTEGER NOT NULL REFERENCES sport(id) ON DELETE CASCADE
);

CREATE TABLE player (
	id 			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	slug 		VARCHAR(255) NOT NULL,
	external_id CHAR(36) NOT NULL UNIQUE,
	team_id 	INTEGER NOT NULL REFERENCES team(id) ON DELETE CASCADE
);

CREATE TABLE event (
	id 				SERIAL PRIMARY KEY,
	external_id 	CHAR(36) NOT NULL UNIQUE,
	home_team_id 	INTEGER NOT NULL REFERENCES team(id) ON DELETE CASCADE,
	away_team_id 	INTEGER NOT NULL REFERENCES team(id) ON DELETE CASCADE,
    status          INTEGER NOT NULL,
    slug            VARCHAR(255) NOT NULL,
	start_date 		TIMESTAMP NOT NULL,
	home_score		INTEGER,
	away_score		INTEGER,
	tournament_id 	INTEGER NOT NULL REFERENCES tournament(id) ON DELETE CASCADE
);