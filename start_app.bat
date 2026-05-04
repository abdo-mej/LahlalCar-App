@echo off
cd /d %~dp0public
echo Starting LahlalCar FR PRO on http://127.0.0.1:8080
echo If database is not installed, open http://127.0.0.1:8080/install.php
php -S 127.0.0.1:8080
pause
