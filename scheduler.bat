@echo off
cd /d C:\laragon\www\Odontrex
php artisan schedule:run >> storage\logs\scheduler.log 2>&1
