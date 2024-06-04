create table plakat_orte
(
    id          int auto_increment
        primary key,
    description longtext             null,
    longitude   varchar(255)         null,
    latitude    varchar(255)         null,
    created_by  varchar(255)         null,
    CREATED_AT  datetime             not null comment '(DC2TYPE:DATETIME_IMMUTABLE)',
    active      tinyint(1) default 1 not null,
    address     varchar(255)         null,
    district    varchar(255)         null,
    DELETED_AT  datetime             null comment '(DC2TYPE:DATETIME_IMMUTABLE)',
    uuid        char(36)             null comment '(DC2Type:uuid)'
);
