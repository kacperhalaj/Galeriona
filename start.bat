@echo off
chcp 1250 >nul
echo ===========================================
echo    Uruchamianie projektu Galeriana
echo ===========================================
echo.

echo [1/9] Instalowanie zależności Composer...
call composer install

echo [2/9] Kopiowanie pliku .env...
if not exist .env (
    copy .env.example .env
    echo Plik .env został utworzony z .env.example
) else (
    echo Plik .env już istnieje
)

echo [3/9] Generowanie klucza aplikacji...
call php artisan key:generate

echo [4/9] Uruchamianie migracji...
call php artisan migrate

echo [5/9] Tworzenie konta administratora...
echo Czy chcesz utworzyć konto administratora? (t/n):
set /p create_admin=
if /i "%create_admin%"=="t" (
    echo.
    call php artisan make:admin
    echo.
) else (
    echo Pomijam tworzenie administratora.
    echo.
)

echo [6/9] Wypełnianie bazy danych danymi przykładowymi...
call php artisan db:seed

echo [7/9] Czyszczenie cache...
call php artisan cache:clear
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear

echo [8/9] Linkowanie storage...
call php artisan storage:link

echo [9/9] Uruchamianie serwera Laravel...
echo.
echo ===========================================
echo    Projekt zostanie uruchomiony na:
echo    http://localhost:8000
echo ===========================================
echo.
echo Aby zatrzymać serwer, naciśnij Ctrl+C
echo.

call php artisan serve --host=localhost --port=8000

pause
