-- auto-generated definition
create table bilete
(
    id             int auto_increment
        primary key,
    user_id        int                                   not null,
    tip_bilet      varchar(100)                          not null,
    cantitate      int         default 1                 null,
    pret_total     decimal(10, 2)                        not null,
    data_achizitie datetime    default CURRENT_TIMESTAMP null,
    status_bilet   varchar(20) default 'Valid'           null,
    data_eveniment varchar(50)                           not null,
    constraint bilete_ibfk_1
        foreign key (user_id) references users (id)
            on delete cascade
);

create index user_id
    on bilete (user_id);

