create table if not exists students (
    id int auto_increment primary key,
    student_id varchar(20) not null unique,
    fullname varchar(100) not null,
    classroom varchar(50) not null
) character set utf8mb4;
