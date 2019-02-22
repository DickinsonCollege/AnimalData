#!/bin/bash

echo "Welcome to the AnimalData installation script.  Press ^C (Control-C)"
echo "to exit the installation at any point."
echo ""

echo "AnimalData is licensed under the Gnu Public License, version 3.0.  A"
echo "copy of this license is available in file gpl-3.0.txt in this directory."
echo "Do you agree to the terms of the license agreement?  (y/n)"
read AGREE
if [ $AGREE = Y ]; then
  AGREE=y
fi

if [ $AGREE != y ]; then
   echo "Exiting AnimalData installation."
   exit 0
fi


echo "Enter username for MySQL admin account (not stored after installation exits): "
read ADMINUSER
echo "Enter password for MySQL admin account (not stored after installation exits):"
read ADMINPASS
echo ""
echo "Creating databases and database user ..."
MYSQLHOST="localhost"
FARMDB=`tr -cd '[:alnum:]' < /dev/urandom | fold -w10 | head -n1`
FARMUSER=`tr -cd '[:alnum:]' < /dev/urandom | fold -w10 | head -n1`
FARMPASS=`tr -cd '[:alnum:]' < /dev/urandom | fold -w10 | head -n1`
      
mysql -u $ADMINUSER -p$ADMINPASS -Bse "create database $FARMDB;" || { 
       echo "Database creation failed.  Exiting AnimalData install!"; exit 1; }
echo "Enter username for initial AnimalData user account:";
read FIRSTUSER
echo "Enter password for initial AnimalData user account:";
read FIRSTPASS
FIRSTPASS=`php -r "print crypt('$FIRSTPASS', '123salt');"`

mysql -u $ADMINUSER -p$ADMINPASS -Bse "use $FARMDB; source tables.txt;
       insert into users values('$FIRSTUSER', 1, 1);
       insert into ext_users values('$FIRSTUSER', '$FIRSTPASS');" || { 
       echo "Setting up database failed.  Exiting AnimalData install!"; exit 1; }

echo "Database table creation successful!"

mysql -u $ADMINUSER -p$ADMINPASS -Bse "create user $FARMUSER identified by '$FARMPASS';" || { 
       echo "Database user creation failed.  Exiting AnimalData install!"; exit 1; }
mysql -u $ADMINUSER -p$ADMINPASS -Bse "use $FARMDB; 
       grant select, delete, insert, update, show view, lock tables on $FARMDB.* to $FARMUSER;" || { 
       echo "Granting privileges to user failed.  Exiting AnimalData install!"; exit 1; }
echo "Database user creation successful!"

echo "Configuring files - this will take a few moments."

#for file in `find -name '.svn'`; do
#   rm -rf $file
#done

for file in "recur.php" "validate.php"; do
   sed -i "s/critterpass/$FARMPASS/" $file || { echo "Error configuring files.  Exiting AnimalData install";
        exit 1; }
   sed -i "s/critterdb/$FARMDB/" $file || { echo "Error configuring files.  Exiting AnimalData install";
        exit 1; }
   sed -i "s/critter/$FARMUSER/" $file || { echo "Error configuring files.  Exiting AnimalData install";
        exit 1; }
done

for file in "index.html" "default.html" "redirect.html"; do
   sed -i "s%url=login\.php%url=extlogin.php%" $file || { echo "Error configuring files.  Exiting AnimalData install";
        exit 1; }
done

rm -f guest.php
rm -f login.php

echo "Adjusting file permissions"

mkdir -p files/
chmod 777 files/
chmod 755 .
chmod 644 *.php
chmod 644 *.html

echo "AnimalData Installation Complete!"
