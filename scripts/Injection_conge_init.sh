#!/bin/bash

#Script to run automated sql queries

#Declaring mysql DB connection 

MASTER_DB_USER='neov'
MASTER_DB_PASSWD='neov123'
MASTER_DB_PORT=3132
MASTER_DB_HOST='192.168.183.122'
MASTER_DB_NAME='conges'
NOW=$(date +"%Y-%m-%d")

#mysql command to connect to database 'conges'
echo "$NOW:******** debut initialisation conge ************"
/usr/bin/mysql -u$MASTER_DB_USER -p$MASTER_DB_PASSWD -P$MASTER_DB_PORT -h$MASTER_DB_HOST -D$MASTER_DB_NAME  <<EOF 
call PRC_INJECTION_INIT();
EOF
echo "$NOW:******* fin initialisation conge ***********"
