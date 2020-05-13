#!/bin/bash

#Script to run automated sql queries

#Declaring mysql DB connection 

MASTER_DB_USER='neov'
MASTER_DB_PASSWD='neov123'
MASTER_DB_PORT=3132
MASTER_DB_HOST='192.168.183.122'
MASTER_DB_NAME='conges'
MASTER_DB_NAME_INIT='conge'
NOW=$(date +"%Y-%m-%d")

#mysql command to connect to database 'conges'
echo "$NOW:******** debut load bo_user ************"
/usr/bin/mysql -u$MASTER_DB_USER -p$MASTER_DB_PASSWD -P$MASTER_DB_PORT -h$MASTER_DB_HOST -D$MASTER_DB_NAME_INIT  <<EOF
call PRC_TRUNCATE_BOUSER_INIT();
EOF
cd /opt/conge/srcs
/usr/bin/php app/console telma:users:import
/usr/bin/mysql -u$MASTER_DB_USER -p$MASTER_DB_PASSWD -P$MASTER_DB_PORT -h$MASTER_DB_HOST -D$MASTER_DB_NAME  <<EOF 
call PRC_LOAD_BOUSER();
call PRC_LOAD_DIRECTION();
call PRC_ACTIVE_DELEGATE();
EOF
echo "$NOW:******* fin load bo_user ***********"
