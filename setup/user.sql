CREATE TABLE user
(
    id       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    login    VARCHAR(50)      NOT NULL DEFAULT '',
    name     VARCHAR(50)      NOT NULL DEFAULT '',
    email    VARCHAR(50)               DEFAULT '',
    password VARCHAR(32)      NOT NULL DEFAULT '',
    access   INT(1)           NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY email (email),
    UNIQUE KEY login_index (login),
    KEY login (login)
);

INSERT INTO user (login, name, email, password, access)
VALUES ('admin', 'Administrator', 'admin@taskmanager.com', '123', 10);
