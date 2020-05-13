#!/bin/bash

#Script to run automated sql queries

#Declaring mysql DB connection 

MASTER_DB_USER='root'
MASTER_DB_PASSWD=''
MASTER_DB_PORT=3306
MASTER_DB_HOST='localhost'
MASTER_DB_NAME='grh'
NOW=$(date +"%Y-%m-%d")

#mysql command to connect to database 'conges'
echo "$NOW:******** debut injection conge mensuel ************"
#/usr/bin/mysql -u$MASTER_DB_USER -p$MASTER_DB_PASSWD -P$MASTER_DB_PORT -h$MASTER_DB_HOST -D$MASTER_DB_NAME  <<EOF 
/C/wamp/bin/mysql/mysql5.6.17/bin/mysql -u$MASTER_DB_USER -p$MASTER_DB_PASSWD -P$MASTER_DB_PORT -h$MASTER_DB_HOST -D$MASTER_DB_NAME  <<EOF
call PRC_INJECTION_CONGE_MENSUEL();
EOF
echo "$NOW:******** fin injection conge mensuel ***********"
