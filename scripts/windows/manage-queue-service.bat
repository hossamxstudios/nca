@echo off
REM ============================================
REM NCA3 Queue Worker - Service Management
REM ============================================
REM Run this script as Administrator!

setlocal enabledelayedexpansion

set "SERVICE_NAME=NCA3-Queue"
set "NSSM_PATH=%~dp0nssm.exe"

REM Check if running as admin
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo ERROR: Please run as Administrator!
    pause
    exit /b 1
)

:menu
cls
echo.
echo ============================================
echo   NCA3 Queue Service Manager
echo ============================================
echo.
echo   Service: %SERVICE_NAME%
echo.
echo   [1] Check Status
echo   [2] Start Service
echo   [3] Stop Service
echo   [4] Restart Service
echo   [5] View Output Log
echo   [6] View Error Log
echo   [7] Clear Logs
echo   [8] Edit Service Config (NSSM GUI)
echo   [9] Remove Service
echo   [0] Exit
echo.
echo ============================================
echo.

set /p choice="Enter your choice (0-9): "

if "%choice%"=="1" goto status
if "%choice%"=="2" goto start
if "%choice%"=="3" goto stop
if "%choice%"=="4" goto restart
if "%choice%"=="5" goto viewlog
if "%choice%"=="6" goto viewerror
if "%choice%"=="7" goto clearlogs
if "%choice%"=="8" goto edit
if "%choice%"=="9" goto remove
if "%choice%"=="0" goto exit

echo Invalid choice!
timeout /t 2 >nul
goto menu

:status
echo.
echo Checking service status...
echo.
"%NSSM_PATH%" status "%SERVICE_NAME%"
echo.
sc query "%SERVICE_NAME%" | findstr "STATE"
echo.
pause
goto menu

:start
echo.
echo Starting service...
"%NSSM_PATH%" start "%SERVICE_NAME%"
echo.
pause
goto menu

:stop
echo.
echo Stopping service...
"%NSSM_PATH%" stop "%SERVICE_NAME%"
echo.
pause
goto menu

:restart
echo.
echo Restarting service...
"%NSSM_PATH%" restart "%SERVICE_NAME%"
echo.
pause
goto menu

:viewlog
echo.
echo Opening output log...
set "LOG_PATH=%~dp0..\..\storage\logs\queue-worker.log"
if exist "%LOG_PATH%" (
    notepad "%LOG_PATH%"
) else (
    echo Log file not found!
    pause
)
goto menu

:viewerror
echo.
echo Opening error log...
set "LOG_PATH=%~dp0..\..\storage\logs\queue-error.log"
if exist "%LOG_PATH%" (
    notepad "%LOG_PATH%"
) else (
    echo Log file not found!
    pause
)
goto menu

:clearlogs
echo.
echo Clearing logs...
set "LOG_PATH=%~dp0..\..\storage\logs"
del /q "%LOG_PATH%\queue-worker.log" 2>nul
del /q "%LOG_PATH%\queue-error.log" 2>nul
echo Logs cleared!
echo.
pause
goto menu

:edit
echo.
echo Opening NSSM configuration GUI...
"%NSSM_PATH%" edit "%SERVICE_NAME%"
goto menu

:remove
echo.
echo WARNING: This will remove the service completely!
set /p confirm="Are you sure? (y/n): "
if /i "%confirm%"=="y" (
    "%NSSM_PATH%" stop "%SERVICE_NAME%" >nul 2>&1
    "%NSSM_PATH%" remove "%SERVICE_NAME%" confirm
    echo Service removed!
)
echo.
pause
goto menu

:exit
echo.
echo Goodbye!
exit /b 0
