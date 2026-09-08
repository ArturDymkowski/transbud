## Wersja demonstracyjna

Aplikacja działa pod adresem: [http://92.5.165.189/login](http://92.5.165.189/login)

Hosting: Oracle Cloud Free Tier.

# Transbud

Transbud to wewnętrzny system do zarządzania firmą transportową (spedycyjno-przewozową).
Pozwala prowadzić kartotekę kierowców, pojazdów i kontrahentów, planować i rozliczać
dostawy (zlecenia transportowe) wraz z ich kosztami i rentownością, a także zarządzać
użytkownikami systemu oraz ich rolami i uprawnieniami.

Aplikacja działa również jako publiczne demo — dane są okresowo resetowane, a część
akcji (tworzenie rekordów, wgrywanie plików) ma nałożone limity opisane w sekcji
[Tryb demo](#tryb-demo).

## Spis treści

- [Technologie](#technologie)
- [Wymagania](#wymagania)
- [Instalacja](#instalacja)
- [Konta demonstracyjne](#konta-demonstracyjne)
- [Rola super_admin](#rola-super_admin)
- [Funkcje systemu](#funkcje-systemu)
- [Tryb demo](#tryb-demo)
- [Testy i jakość kodu](#testy-i-jakość-kodu)

## Technologie

- **PHP 8.3+**, **Laravel 13**
- **Livewire 4** — interaktywne komponenty (tabele, formularze, widoki szczegółów) bez pisania własnego API/JS
- **Spatie Laravel Permission** — role i uprawnienia (`can:zasob.akcja`)
- **Spatie Laravel MediaLibrary** — załączniki (dokumenty kierowców, załączniki do dostaw) na prywatnych dyskach
- **Spatie Laravel Activitylog** — dziennik aktywności (kto, co i kiedy zmienił)
- **Vite 7 + Tailwind CSS 4** — build frontendu i stylowanie
- **Alpine.js** — drobne interakcje po stronie klienta
- **FullCalendar** — kalendarz dostaw
- Szablon graficzny oparty o **TailAdmin**
- **Pest 5** — testy (jednostkowe i funkcjonalne)
- **Laravel Pint** — code style; **Larastan/PHPStan** — analiza statyczna

## Wymagania

- PHP 8.3 lub nowszy wraz z rozszerzeniami wymaganymi przez Laravel/Composer (m.in. `pdo`,
  `mbstring`, `fileinfo`, `gd`/`imagick` — do miniatur w bibliotece mediów)
- Composer 2.x
- Node.js 20+ oraz npm
- Baza danych MySQL 8+ (lub MariaDB) — domyślna konfiguracja w `.env.example`; do samych
  testów wystarczy SQLite w pamięci, patrz `phpunit.xml`
- (Opcjonalnie) Redis — jeśli chcesz podmienić domyślne sterowniki sesji/cache/kolejki

## Instalacja

```bash
git clone https://github.com/ArturDymkowski/transbud transbud
cd transbud

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Skonfiguruj połączenie z bazą danych w pliku `.env` (domyślnie MySQL, baza `tailadmin_laravel`),
a następnie:

```bash
php artisan migrate --seed
```

Seedery utworzą komplet ról i uprawnień, konta demonstracyjne (patrz niżej) oraz przykładowe
dane: kierowców, pojazdy, kontrahentów, towary, jednostki i dostawy.

Uruchomienie aplikacji w trybie developerskim (serwer PHP + kolejka + logi na żywo + Vite,
równolegle w jednym poleceniu):

```bash
composer run dev
```

albo pojedynczo:

```bash
php artisan serve   # backend
npm run dev          # frontend (Vite)
```

Do produkcji frontend buduje się przez:

```bash
npm run build
```

## Konta demonstracyjne

Po wykonaniu `php artisan db:seed` (lub `migrate --seed`) dostępne są:

| Rola  | E-mail                | Hasło      |
|-------|------------------------|------------|
| Admin | `admin@transbud.com`   | `admin`    |
| User  | `user1@transbud.com`   | `password` |
| User  | `user2@transbud.com`   | `password` |
| User  | `user3@transbud.com`   | `password` |

Rola `Admin` ma domyślnie wszystkie uprawnienia, rola `User` — tylko podgląd (`*.view`)
kierowców, pojazdów, kontrahentów, adresów kontrahentów, towarów, jednostek i dostaw.

## Rola super_admin

Niezależnie od ról Spatie (Admin/User), konto może mieć ustawioną flagę `is_super_admin`
w tabeli `users`. Tylko taki użytkownik widzi w menu i ma dostęp do dwóch dodatkowych
zakładek:

- **Dziennik logowań** (`/login-audit-log`) — historia prób logowania do systemu
- **Dziennik aktywności** (`/activity-log`) — pełna historia zmian wykonanych w systemie
  (kto, co, kiedy i jaki rekord zmienił)

Domyślny seed nie ustawia tej flagi nikomu. Żeby ją włączyć:

1. Utwórz (lub wskaż istniejące) konto z rolą `Admin`.
2. Ustaw ręcznie kolumnę `is_super_admin` na `1` w tabeli `users` — bezpośrednio w bazie
   albo przez Tinkera:

   ```bash
   php artisan tinker
   >>> User::where('email', 'admin@transbud.com')->update(['is_super_admin' => true]);
   ```

`demo:reset` celowo pomija konta oznaczone jako `is_super_admin` (nie są usuwane przy
resecie danych demo).

## Funkcje systemu

### Dashboard
Ekran startowy z listą zbliżających się terminów ważności (uprawnienia kierowców, dowody
osobiste, przeglądy techniczne/ubezpieczenia/tachografy pojazdów), pogrupowanych kolorystycznie
według liczby dni pozostałych do wygaśnięcia (czerwony ≤ 7 dni, żółty ≤ 14 dni, zielony ≤ 30 dni).

### Kierowcy
Kartoteka kierowców: dane osobowe, terminy ważności prawa jazdy i dowodu osobistego,
przypisanie domyślnego ciągnika/naczepy, załączane dokumenty (skany dokumentów jako
prywatne pliki w bibliotece mediów). Podgląd kierowcy pokazuje też listę pojazdów, do
których jest przypisany.

### Pojazdy
Kartoteka pojazdów (ciągniki i naczepy) z numerem rejestracyjnym oraz terminami przeglądu
technicznego, ubezpieczenia i przeglądu tachografu; powiązanie z przypisanymi kierowcami.

### Kontrahenci i adresy kontrahentów
Baza kontrahentów (klientów/zleceniodawców) wraz z wieloma adresami załadunku/rozładunku
przypisanymi do każdego kontrahenta.

### Towary i jednostki
Słownik towarów przewożonych w dostawach oraz jednostek miary (np. szt., kg, palety),
powiązanych z towarami.

### Dostawy (zlecenia transportowe)
Najbardziej rozbudowany moduł systemu:

- Podstawowe dane zlecenia: kontrahent, adres, kwota frachtu, status.
- **Zestawy transportowe** — jedna dostawa może być realizowana przez wiele zestawów
  kierowca + ciągnik + naczepa, każdy z własnym czasem załadunku/rozładunku i statusem
  realizacji (historia zmian statusu jest zapisywana).
- **Koszty dostawy** — koszty bezpośrednie dostawy oraz koszty przypisane do
  konkretnego zestawu transportowego.
- **Rentowność** — automatyczne wyliczanie kosztu całkowitego, zysku i marży procentowej
  na podstawie frachtu i wprowadzonych kosztów (bez zapisywania wyników w bazie —
  liczone na bieżąco).
- **Kalendarz dostaw** (FullCalendar) oraz **planer godzinowy** — wizualne planowanie
  obłożenia kierowców i pojazdów w czasie.
- Załączniki (dokumenty) do dostawy, przechowywane jak w module kierowców.

### Użytkownicy, role i uprawnienia
Zarządzanie kontami użytkowników (w tym tworzenie kolejnych administratorów przez
administratora), rolami oraz przypisywanymi im uprawnieniami — pełny, zamodelowany
w Spatie Laravel Permission system RBAC oparty o konwencję `zasob.akcja`
(`view`/`create`/`edit`/`delete`).

### Dziennik aktywności i dziennik logowań *(tylko super_admin)*
Podgląd historii zmian w systemie (kto, kiedy i co zmienił) oraz historii logowań do
aplikacji — patrz [Rola super_admin](#rola-super_admin).

### Wielojęzyczność
Interfejs dostępny w języku polskim (domyślny) i angielskim, przełączany z poziomu menu.

## Tryb demo

Ponieważ aplikacja działa też jako publiczne demo, formularze mają dodatkowe zabezpieczenia
niezwiązane z regułami biznesowymi:

- limit liczby nowych rekordów tworzonych z jednego adresu IP w danym oknie czasowym,
- twardy limit maksymalnej liczby wierszy na model,
- łączny limit rozmiaru plików wgrywanych do bibliotek dokumentów kierowców/dostaw.

Wartości progów konfiguruje się zmiennymi środowiskowymi w `.env`
(`DEMO_RECORD_CREATION_MAX_ATTEMPTS`, `DEMO_RECORD_CREATION_DECAY_MINUTES`,
`DEMO_MAX_RECORDS`, `DEMO_MAX_DISK_MB` — patrz `config/demo.php`).

Polecenie `php artisan demo:reset` czyści i ponownie zasiewa wszystkie dane demo,
zachowując dziennik logowań oraz konta oznaczone jako `is_super_admin`. W środowisku
produkcyjnym uruchamiane jest automatycznie co noc o 3:00 (`routes/console.php`).

## Testy i jakość kodu

```bash
composer test                 # pełny zestaw testów (Pest)
php artisan test --filter=Nazwa
vendor/bin/pint                # code style (Laravel Pint)
vendor/bin/pint --dirty         # tylko zmienione pliki
```

Testy korzystają z bazy SQLite w pamięci (`phpunit.xml`) i nigdy nie dotykają bazy
deweloperskiej.
