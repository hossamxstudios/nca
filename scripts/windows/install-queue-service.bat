@echo off
REM ============================================
REM NCA3 Queue Worker - NSSM Installation Script
REM ============================================
REM Run this script as Administrator!

setlocal enabledelayedexpansion

REM Configuration - UPDATE THESE PATHS!
set "PHP_PATH=C:\xampp\php\php.exe"
set "PROJECT_PATH=%~dp0..\.."
set "SERVICE_NAME=NCA3-Queue"
set "NSSM_PATH=%~dp0nssm.exe"

REM Check if running as admin
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo ========================================
    echo   ERROR: Please run as Administrator!
    echo ========================================
    echo.
    echo Right-click this file and select
    echo "Run as administrator"
    echo.
    pause
    exit /b 1
)

REM Check if NSSM exists
if not exist "%NSSM_PATH%" (
    echo.
    echo ========================================
    echo   NSSM not found!
    echo ========================================
    echo.
    echo Please download NSSM from:
    echo https://nssm.cc/download
    echo.
    echo Extract nssm.exe to:
    echo %~dp0
    echo.
    pause
    exit /b 1
)

REM Check if PHP exists
if not exist "%PHP_PATH%" (
    echo.
    echo ========================================
    echo   PHP not found at: %PHP_PATH%
    echo ========================================
    echo.
    echo Please update PHP_PATH in this script
    echo.
    pause
    exit /b 1
)

REM Get absolute project path
pushd "%PROJECT_PATH%"
set "PROJECT_PATH=%CD%"
popd

echo.
echo ============================================
echo   NCA3 Queue Service Installer
echo ============================================
echo.
echo PHP Path:     %PHP_PATH%
echo Project Path: %PROJECT_PATH%
echo Service Name: %SERVICE_NAME%
echo.

REM Check if service already exists
sc query "%SERVICE_NAME%" >nul 2>&1
if %errorLevel% equ 0 (
    echo Service already exists. Removing old service...
    "%NSSM_PATH%" stop "%SERVICE_NAME%" >nul 2>&1
    "%NSSM_PATH%" remove "%SERVICE_NAME%" confirm
    timeout /t 2 >nul
)

echo Installing service...
echo.

REM Install the service
"%NSSM_PATH%" install "%SERVICE_NAME%" "%PHP_PATH%"

REM Configure service parameters
"%NSSM_PATH%" set "%SERVICE_NAME%" AppParameters "artisan queue:work --sleep=3 --tries=3 --max-time=3600 --memory=256"
"%NSSM_PATH%" set "%SERVICE_NAME%" AppDirectory "%PROJECT_PATH%"

REM Configure logging
"%NSSM_PATH%" set "%SERVICE_NAME%" AppStdout "%PROJECT_PATH%\storage\logs\queue-worker.log"
"%NSSM_PATH%" set "%SERVICE_NAME%" AppStderr "%PROJECT_PATH%\storage\logs\queue-error.log"
"%NSSM_PATH%" set "%SERVICE_NAME%" AppStdoutCreationDisposition 4
"%NSSM_PATH%" set "%SERVICE_NAME%" AppStderrCreationDisposition 4
"%NSSM_PATH%" set "%SERVICE_NAME%" AppRotateFiles 1
"%NSSM_PATH%" set "%SERVICE_NAME%" AppRotateBytes 5242880

REM Configure restart on failure
"%NSSM_PATH%" set "%SERVICE_NAME%" AppThrottle 5000
"%NSSM_PATH%" set "%SERVICE_NAME%" AppExit Default Restart
"%NSSM_PATH%" set "%SERVICE_NAME%" AppRestartDelay 5000

REM Set service description
"%NSSM_PATH%" set "%SERVICE_NAME%" DisplayName "NCA3 Queue Worker"
"%NSSM_PATH%" set "%SERVICE_NAME%" Description "Laravel Queue Worker for NCA3 Archive System"
"%NSSM_PATH%" set "%SERVICE_NAME%" Start SERVICE_AUTO_START

echo.
echo ============================================
echo   Starting service...
echo ============================================
echo.

"%NSSM_PATH%" start "%SERVICE_NAME%"

timeout /t 2 >nul

REM Check service status
"%NSSM_PATH%" status "%SERVICE_NAME%"

echo.
echo ============================================
echo   Installation Complete!
echo ============================================
echo.
echo Service "%SERVICE_NAME%" has been installed and started.
echo.
echo Useful commands:
echo   - Check status:  nssm status %SERVICE_NAME%
echo   - Stop service:  nssm stop %SERVICE_NAME%
echo   - Start service: nssm start %SERVICE_NAME%
echo   - Restart:       nssm restart %SERVICE_NAME%
echo   - Remove:        nssm remove %SERVICE_NAME%
echo.
echo Log files:
echo   - Output: %PROJECT_PATH%\storage\logs\queue-worker.log
echo   - Errors: %PROJECT_PATH%\storage\logs\queue-error.log
echo.
pause
