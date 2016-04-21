CREATE TABLE IF NOT EXISTS user(     
id int(10) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,     
username VARCHAR(24) NOT NULL UNIQUE,     
password VARCHAR(8) NOT NULL
);

show variables like '%time%'; 
 
 
timeBetweenEvictionRunsMillis = 20000 
#dbcp每2000秒进行一次connection的检 
minEvictableIdleTimeMillis = 28700 
#每次检验中将超过28700秒处于空闲的connection断开

#for test
set global wait_timeout=3; 
set global wait_timeout=28800;
