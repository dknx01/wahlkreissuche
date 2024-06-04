create table sessions
(
    sess_id       varbinary(256) not null
        primary key,
    sess_data     blob           not null,
    sess_lifetime int unsigned   not null,
    sess_time     int unsigned   not null
)
    collate = utf8mb4_bin;

create index sessions_sess_lifetime_idx
    on sessions (sess_lifetime);

