CREATE TABLE post
(
    id           SERIAL PRIMARY KEY NOT NULL,
    title        VARCHAR(100)       NOT NULL,
    text         TEXT               NOT NULL,
    status       VARCHAR(50)        NOT NULL,
    date_created TIMESTAMP          NOT NULL
);
