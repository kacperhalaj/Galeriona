# Projekt AI1

[📁 Repozytorium projektu](https://github.com/JakubowskyM/Galeriona)

[📌 Tablica projektowa](https://github.com/users/JakubowskyM/projects/1)

---


### Sprzedaż dzieł sztuki

**Galeriona** to aplikacja internetowa służąca do sprzedaży i zakupu dzieł sztuki, zarówno dla kolekcjonerów, jak i twórców. Projekt obsługuje pełny cykl sprzedaży, od prezentacji oferty, poprzez koszyk, aż po zakup i zarządzanie historią transakcji. System zawiera dedykowane panele dla administratora, sprzedawcy i klienta, a także mechanizmy promocji, lojalności oraz zabezpieczeń.


---

### Zespół A2

| Profil | Rola |
| ------ | ------ |
| [JakubowskyM](https://github.com/JakubowskyM) | lider zespołu |
| [Gaabcio](https://github.com/Gaabcio) | członek zespołu |
| [kacperhalaj](https://github.com/kacperhalaj) | członek zespołu |
| [KacperL1ga](https://github.com/KacperL1ga) | członek zespołu |
---


## Opis projektu

### 🔐 Autoryzacja i użytkownicy
- Logowanie i rejestracja użytkowników z captchą.
- Przypomnienie hasła (token wyświetlany w konsoli).
- TOTP (dwuskładnikowa autoryzacja).
- Profile użytkowników: administrator, klient, sprzedawca.
- Podział na dedykowane panele: admin, klient, sprzedawca.
- CRUD użytkowników w panelu administratora.
- Zarządzanie własnymi danymi w panelu użytkownika.
- Możliwość podążania za ofertami ulubionych sprzedawców.
- System funduszy użytkownika (doładowania, zakupy, wpływy).
- Trofea / levele dla lojalnych klientów z progres barami.

### 🛒 Zakupy i sprzedaż
- Kupno dzieł sztuki przez zalogowanego klienta.
- Koszyk (obsługa wielu produktów w jednym zamówieniu).
- Wybór adresu do zamówienia.
- System promocji (np. zniżki przy zakupie wielu dzieł).
- Losowanie dzieł bezcennych dla najbardziej hojnych klientów.
- Sugerowanie rozmiaru paczki na podstawie wymiarów dzieł.
- Generowanie miesięcznego zestawienia sprzedaży (PDF, mpdf).

### 🖼️ Dzieła sztuki
- Przeglądanie ofert (nawet bez logowania).
- Filtrowanie, sortowanie, paginacja.
- CRUD dzieł sztuki (admin i sprzedawca).
- Obsługa dzieł bezcennych.
- Dzieła sprzedawców — zarządzanie w panelu.
- Każde dzieło ma rozmiary (szer., wys., gł.).

### 📊 Statystyki i historia
- Historia sprzedaży (sprzedawca).
- Historia zakupów (klient).
- Statystyki i wykresy dotyczące transakcji.

### ⚙️ Inne
- Obsługa błędów HTTP (dedykowane widoki).
- CRUD zasobów: klientów, sprzedawców, adresów i kategorii (panel admina).
- Zarządzanie adresami przez klientów.
- Skrypt startowy `start.bat` do automatycznej instalacji.

### Narzędzia i technologie
- **PHP 8.x**
- **Laravel 10**
- **Composer**
- **MySQL 8**
- **XAMPP**
- **Blade (Laravel Templates)**
- HTML, CSS, JavaScript

### Uruchomienie aplikacji

### Wymagania:

- Zainstalowany [Composer](https://getcomposer.org/)
- Zainstalowany [XAMPP](https://www.apachefriends.org/index.html)
- Włączone rozszerzenia w `php.ini`: `gd`, `pdo`, `openssl`, `mbstring`, `tokenizer`, `xml`

```
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve

```

Przykładowi użytkownicy aplikacji:
* administrator: Nazwe podaje i hasło podaje uzytkownik podczas uruchamiania start.bat
* użytkownik (klient): anna@example.com 12345678
* użytkownik (klient): piotr@example.com 12345678
* użytkownik (klient): maria@example.com 12345678
* użytkownik (sprzedawca): jakub@gallery.com 12345678
* użytkownik (sprzedawca): magdalena@gallery.com 12345678

### Baza danych

![Diagram ERD](./docs-img/erd.png)

## Widoki aplikacji 

![Strona główna](./docs-img/homepage_notloggedin.png)
*Strona główna użytkownika niezalogowanego*

![Strona główna](./docs-img/login.png)
*Logowanie*

![Strona główna](./docs-img/register.png)
*Rejestracja*

## Widoki klienta (kupującego) 

![Strona główna](./docs-img/client/homepage_client_loggedin.png)
*Strona główna użytkownika zalogowanego - klienta*

![Obserwowani sprzedawcy](./docs-img/client/followed_artists.png)
*Sekcja strony głównej od przeglądania ofert obserwowanych sprzedawców*

![Koszyk klienta bez rabatów](./docs-img/client/cart.png)
*Koszyk klienta bez naliczonych rabatów*

![Koszyk klienta z rabatami](./docs-img/client/cart_discount.png)
*Koszyk klienta bez naliczonych rabatów*

![Panel klient](./docs-img/client/panel.png)
*Panel klient z widokiem do wszystkich funkcjonalności*

![Panel klient](./docs-img/client/edit.png)
*Panel klient z widokiem do edycji swoich danych*

![Zamówienia](./docs-img/client/orders.png)
*Panel klient z widokiem do swoich wszystkich zakupów*

![Obserwowani artyści](./docs-img/client/followed.png)
*Panel klient z widokiem do obserwowanych sprzedawców*

![Portfel](./docs-img/client/wallet.png)
*Panel klient z widokiem do wglądu swojego portfela na platformie z historią operacji na portfelu*

![Doładowywanie portfela](./docs-img/client/wallet_top_up.png)
*Panel klient z widokiem do doładowania swojego portfela wraz z wyborem formy doładowania*

![Darowizny](./docs-img/client/donations.png)
*Panel klient z widokiem do darowizn i historii darowizn*

![Adresy zamówień](./docs-img/client/address.png)
*Panel klient z widokiem do zarządzania adresami*

![Adresy zamówień](./docs-img/client/address_edit.png)
*Panel klient z widokiem do edycji wybranego adresu*

![Adresy zamówień](./docs-img/client/address_add.png)
*Panel klient z widokiem do dodania nowego adresu*

![Statystyki zakupów klienta](./docs-img/client/stats_1.png)
*Panel klient z widokiem do statystyk*

![Statystyki zakupów klienta](./docs-img/client/stats_2.png)
*Panel klient z widokiem do statystyk*

## Widoki artysty (sprzedającego)

![Strona główna](./docs-img/seller/homepage_seller_loggedin.png)
*Strona główna użytkownika zalogowanego - artysty*

![Panel artysty](./docs-img/seller/panel.png)
*Panel artysty z widokiem do wszystkich funckjonalności*

![Zarządzanie kontem](./docs-img/seller/edit.png)
*Panel artysty z widokiem do edycji szczegółów swojego konta*

![Dodawanie dzieła](./docs-img/seller/add_artwork.png)
*Panel artysty z widokiem do dodania nowego dzieła sztuki*

![Zarządzanie dziełami](./docs-img/seller/manage_artwork.png)
*Panel artysty z widokiem do listy swoich dzieł sztuki*

![Zarządzanie dziełami](./docs-img/seller/details.png)
*Panel artysty z widokiem do szczegółów danego dzieła sztuki*

![Finanse artysty](./docs-img/seller/finance_details.png)
*Panel artysty z widokiem do finansów artysty*

![Finanse artysty](./docs-img/seller/zestawienie_sprzedazy_2025_06.pdf)
*Przykładowy automatycznie generowany pdf z miesiąca czerwiec*

![Obserwujący](./docs-img/seller/followers.png)
*Panel artysty z widokiem do obserwujących*

![Statystyki sprzedaży](./docs-img/seller/stats.png)
*Panel artysty z widokiem do obserwująstatystyk sprzedaży*

![Statystyki zakupów artysty](./docs-img/client/stats_2.png)
*Panel artysty z widokiem do statystyk*

## Widoki admina

![Strona główna](./docs-img/admin/homepage_admin_loggedin.png)
*Strona główna użytkownika zalogowanego - artysty*

![Panel admina](./docs-img/admin/panel.png)
*Panel admina z widokiem do wszystkich funckjonalności*

![Zarządzanie adminem](./docs-img/admin/edit.png)
*Panel admina z widokiem do edycji sczegółów konta*

![Zarządzanie klientami (kupującymi)](./docs-img/admin/client_manage.png)
*Panel admina z widokiem do zarządzania kupującymi*

![Zarządzanie klientami (kupującymi)](./docs-img/admin/client_manage.png)
*Szczegółowe dane wybrnaego kupującego*

![Zarządzanie adresami](./docs-img/admin/address_manage.png)
*Panel admina z widokiem do zarządzania adresami wybranego użytkownika z listy*

![Zarządzanie adresami](./docs-img/admin/address_edit.png)
*Panel admina z widokiem do zmianu wybraengo adresu danego użytkownika*

![Zarządzanie artystami (sprzedającymi)](./docs-img/admin/sellers_manage.png)
*Panel admina z widokiem do zarządzania artystami*

![Zarządzanie artystami (sprzedajacymi)](./docs-img/admin/seller_manage.png)
*Szczegółowe dane wybranego artysty*

![Zarządzanie dziełami sztuki](./docs-img/admin/artworks_manage.png)
*Panel admina z widokiem do zarządzania dziełami sztuki*

![Zarządzanie dziełami sztuki](./docs-img/admin/artwork_details.png)
*Szczegółowe dane wybranego dzieła sztuki*

![Zarządzanie zamówieniami](./docs-img/admin/orders_manage.png)
*Panel admina z widokiem do zarządzania zamówieniami*

![Zarządzanie zamówieniami](./docs-img/admin/order_details.png)
*Szczegółowe dane wybranego zamówienia*

![Zarządzanie kategoriami](./docs-img/admin/category_manage.png)
*Panel admina z widokiem do zarządzania kategoriami dzieł sztuki*

![Statystyki](./docs-img/admin/stats_1.png)
*Panel admina z widokiem do statystyk platformy*

![Statystyki](./docs-img/admin/stats_2.png)
*Panel admina z widokiem do statystyk platformy*
