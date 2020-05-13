#!/bin/bash

#Script to run automated sql queries

#Declaring mysql DB connection 

MASTER_DB_USER='neov'
MASTER_DB_PASSWD='neov123'
MASTER_DB_PORT=3132
MASTER_DB_HOST='192.168.183.122'
MASTER_DB_NAME='conges'

#mysql command to connect to database 'conges'
echo "******** debut initialisation conge EXPAT ************"
/usr/bin/mysql -u$MASTER_DB_USER -p$MASTER_DB_PASSWD -P$MASTER_DB_PORT -h$MASTER_DB_HOST -D$MASTER_DB_NAME  <<EOF 
call PRC_INIT_CONGE_EXPAT();
EOF
echo "******* fin initialisation conge EXPAT ***********"
