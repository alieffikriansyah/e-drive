@echo off
c:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE IF EXISTS e_drive"
c:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE e_drive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
c:\xampp\mysql\bin\mysql.exe -u root e_drive < c:\xampp\htdocs\e-drive\database\e_drive.sql
echo DONE
