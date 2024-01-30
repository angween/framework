@echo off

REM Get the current directory path
for %%* in (.) do set "current_folder=%%~nx*"

REM Time Formatting (same as before)
set hour=%time:~0,2%
if "%hour:~0,1%" == " " set hour=0%hour:~1,1%
set min=%time:~3,2%
if "%min:~0,1%" == " " set min=0%min:~1,1%

REM Date Formatting (same as before)
set datetimef=%date:~-4%%date:~4,2%%date:~7,2%_%hour%%min%.sql

echo *** Membuat backup %current_folder%
echo *** Silahkan masukan password MySQL/MariaDB nya untuk user root

REM Using the obtained folder name as the database name
REM "C:\wamp64\bin\mariadb\mariadb11.2.2\bin\mysqldump.exe" -u root -p %current_folder% > "C:\Users\ASUS\OneDrive\BackupDB\%current_folder%_%datetimef%"
"C:\wamp64\bin\mariadb\mariadb11.2.2\bin\mysqldump.exe" -u root -p %current_folder% > "%current_folder%.sql"

echo *** Selesai, tekan sembarang tombol.
pause > nul
echo on