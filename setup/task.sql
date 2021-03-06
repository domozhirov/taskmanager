CREATE TABLE task
(
    id       INT(4) UNSIGNED AUTO_INCREMENT,
    name     VARCHAR(30) NOT NULL,
    email    VARCHAR(50) NOT NULL,
    status   INT(1)      NOT NULL DEFAULT 0,
    text     TEXT        NOT NULL,
    created  TIMESTAMP   NOT NULL DEFAULT '0000-00-00 00:00:00',
    modified TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY `user` (name, email)
)
