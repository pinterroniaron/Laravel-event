# Új projekt létrehozás

**composer create-project laravel/laravel 01_event-manager**

v.

**_laravel new 01_event-manager_**

futtatás:

**php artisan dev**
**_php artisan serve_**

# Adatbázis és Eloquent ORM

## Migráció létrehozása

**php artisan make:migration create_events_table**

$table->id() → automatikus, auto-increment id oszlop (primary key)

$table->timestamps() → két oszlopot ad hozzá: created_at és updated_at, amiket Eloquent automatikusan kezel

**php artisan migrate**

## A modell létrehozása

**php artisan make:model Event**

Ez Laravel egyik biztonsági mechanizmusa, a mass assignment protection. Alapból Eloquent nem engedi egyszerre több mezőt feltölteni (Event::create([...])), csak azokat, amiket kifejezetten engedélyezünk a #[Fillable()] attribútumban ($fillable tömbben). Enélkül a create() hívás hibát dobna.

## Factory írása (dummy adatokhoz)

**php artisan make:factory EventFactory --model=Event**

A fake() helper a Faker könyvtárat hívja — ez generál véletlenszerű, de értelmes kinézetű szöveget, dátumot stb.

## Seeder írása

**php artisan make:seeder EventSeeder**

**php artisan db:seed**
Ez lefuttatja a DatabaseSeeder-t, ami meghívja az EventSeeder-t, ami 12 darab Event-et hoz létre a EventFactory receptje alapján.

## Kontroller bevezetése (closure helyett)

**php artisan make:controller EventController**

### Dátum konvertálás

- Eloquent csak a created_at / updated_at mezőket alakítja automatikusan Carbon objektummá — ez a $timestamps mechanizmus, ami minden modellnél alapból be van kapcsolva
- Minden egyéb dátum-oszlopot (mint az event_date) explicit meg kell mondani, hogy datetime-ként kezelje — enélkül az attribútum sima string-ként érkezik a query-ből, ezért nincs rajta format() metódus

```
protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }
```

## Mi a különbség Factory és Seeder között?

|             | Factory                                               | Seeder                                                 |
| ----------- | ----------------------------------------------------- | ------------------------------------------------------ |
| Mi ez?      | "Recept" egyetlen darab hamis modellhez               | "Vezénylés" — mikor, hányat, milyen sorrendben hívjunk |
| Hova kerül? | database/factories/                                   | database/seeders/                                      |
| Példa       | "egy Event-nek legyen véletlen címe, leírása, dátuma" | "hozz létre 20 Event-et"                               |

## A Form Request osztály

**php artisan make:request StoreEventRequest**

- Miért külön osztály, nem a kontrollerben validálunk? — mert a validációs szabályok gyakran hosszúak, és ha a kontrollerben lennének, elnyomnák a tényleges logikát. A Form Request kiszervezi ezt egy önálló, felelősséggel bíró helyre.
- authorize() — most true-t adunk vissza (bárki létrehozhat eseményt), de ez az a hely, ahol később jogosultság-ellenőrzés kerülhet (pl. return auth()->user()->isAdmin();)
- 'after:now' — ez a validátor beépített szabálya, ami pontosan a feladat kérését teljesíti: a dátum nem lehet a jelenben vagy múltban
- A messages() metódus opcionális — nélküle Laravel angol alapértelmezett hibaszövegeket adna ("The title field is required.") — mivel magyar UI-t építünk, érdemes ezeket felülírni

A kontrollerben:

- StoreEventRequest $request — észrevehető, hogy nem a sima Request-et injektáljuk, hanem a saját Form Request osztályunkat. Laravel ekkor automatikusan lefuttatja a validációt, mielőtt a store() metódus törzse egyáltalán futna. Ha a validáció elbukik, a felhasználó automatikusan visszakerül az űrlapra, a hibákkal együtt — mindezt egy sor kód nélkül a kontrollerben!
- $request->validated() — ez csak a validált mezőket adja vissza tömbként, tehát biztonságos egyenesen átadni az Event::create()-nek (ez összekapcsolódik a korábban tanult #[Fillable]-lel: a create() csak a fillable mezőket engedi be, a validated() pedig csak a validált mezőket adja — két védelmi réteg)
- redirect()->route(...)->with('success', ...) — ez egy flash session üzenetet hoz létre, ami csak a következő egy kérés életidejéig él — pont addig, hogy a redirect után megjelenjen egyszer

Az űrlapon (blade fájlban):
|Elem |Miért kell|
|-----|----|
|@csrf |Beszúr egy rejtett tokent, ami igazolja, hogy a form tényleg a saját oldalunkról érkezett. Ha kihagyjuk, Laravel 419 Page Expired hibával elutasítja a beküldést.|
|value="{{ old('title') }}"| Ha a validáció elbukik, a felhasználó visszakerül az űrlapra — az old() visszaadja, mit írt be legutóbb, hogy ne kelljen mindent újra beírnia|
|@error('title') ... @enderror| Csak akkor jelenik meg, ha az adott mezőnél van validációs hiba — a $message automatikusan az adott mező hibaszövegét tartalmazza|
|@error('title') border-red-500 @enderror| Ugyanaz a direktíva a class attribútumban is használható — a mező kerete piros lesz hiba esetén|
|type="datetime-local"| Natív böngésző dátum-idő választó, nem kell külön JS-plugin|

## Új oszlop a migrációhoz

**php artisan make:migration add_poster_path_to_events_table --table=events**

**php artisan migrate**

**_Miért Schema::table, nem Schema::create?_**
create egy új táblát hoz létre, table egy meglévőt módosít. Ez az az elv, amiért Laravelben minden változtatáshoz — még egyetlen oszlop hozzáadásához is — külön migrációt írunk: így a migrációk története pontosan visszaadja, hogyan alakult a séma az idők során, és bárki, aki később csatlakozik a projekthez, végig tudja futtatni ugyanezt a történetet a saját gépén.

**_Fontos: miért poster_path, nem poster?_**
Az oszlopban nem magát a képfájlt tároljuk (az adatbázis erre rossz hely lenne), hanem csak egy elérési utat (stringet), ami megmutatja, hol található a fájl a lemezen. A név ezt tükrözi.

## A storage és public kapcsolat: a szimbolikus link

**php artisan storage:link**

Laravelben a feltöltött fájlok alapból nem a public/ mappába kerülnek, hanem a storage/app/public/ mappába. Ennek oka: a storage/ mappa nem publikusan elérhető a webről — ez egy tudatos biztonsági döntés (ide kerülhetnek pl. naplófájlok, cache, privát dokumentumok is).

Ahhoz, hogy a böngésző mégis el tudja érni a feltöltött képeket, egy szimbolikus linket kell létrehozni, ami összeköti a kettőt.

storage/app/public/posters/kep.jpg ← ide mentjük ténylegesen
↕ (szimbolikus link)
public/storage/posters/kep.jpg ← innen éri el a böngésző

Ezt a lépést csak egyszer kell futtatni projektenként (de minden új fejlesztői gépen újra kell, mert a szimbolikus link nem kerül be a git repóba).

A kontrollerben:

- $request->hasFile('poster') — mivel a poster opcionális, először ellenőrizzük, egyáltalán érkezett-e fájl, mielőtt bármit csinálnánk vele
- ->store('posters', 'public') — ez a Laravel Storage rendszerének egyik "varázslata": az első paraméter a almappa neve a diszken belül, a második a disk neve (a config/filesystems.php-ban definiált public disk, ami pont a korábban linkelt storage/app/public-ra mutat)
- A store() visszatér egy relatív útvonallal, pl. "posters/aBc123XyZ.jpg" — Laravel automatikusan generál egyedi fájlnevet, hogy elkerülje az ütközéseket, ha két felhasználó ugyanazzal a névvel tölt fel képet
- Ezt az útvonalat mentjük a poster_path oszlopba — nem a teljes URL-t, csak a relatív utat. Az URL-t majd megjelenítéskor állítjuk össze.

A modell osztályban (Event):

Attribute::make(get: ...)
A metódus neve (posterUrl) camelCase-ben van megadva, de a modellen elérve snake_case-re alakul: $event->poster_url

# Elnevezési konvenciók Laravelben

Ez a segédlet azt foglalja össze, hogy egy Laravel projektben **mit hogyan nevezünk el**. A szabály egyszerű:

> **Kód szinten (fájlnév, változó, osztály, mező, route) mindig angolul nevezünk el — a felhasználónak megjelenő szöveg (UI, gombfelirat, cím) viszont magyarul lehet.**

Ez nem csak stílus kérdése: a Laravel keretrendszer _bizonyos helyeken számít is rá_, hogy a nevek angol, egyes szám / többes szám szabályokat követnek (pl. modell → tábla összerendelés).

---

## 1. A négy leggyakoribb formátum

| Formátum       | Hogy néz ki                        | Mire használjuk Laravelben                         |
| -------------- | ---------------------------------- | -------------------------------------------------- |
| **PascalCase** | `EventController`, `Event`         | Osztályok, modellek, kontrollerek                  |
| **camelCase**  | `startDate`, `getUserName()`       | Változók, függvény-/metódusnevek (PHP oldalon)     |
| **snake_case** | `start_date`, `created_at`         | Adatbázis táblák, adatbázis oszlopok               |
| **kebab-case** | `/event-manager`, `password-reset` | URL-ek, route-ok, fájlnevek Blade-ben (opcionális) |

---

## 2. Konkrét példák a projektünkből (Event Manager)

### Route-ok → **kebab-case**, angolul

```php
Route::get('/events', ...);
Route::get('/events/create', ...);
Route::get('/event-categories', ...);   // ne: /esemeny-kategoriak
```

### View fájlok → angolul, kisbetűvel

```
resources/views/events.blade.php
resources/views/events/create.blade.php
resources/views/about.blade.php
```

❌ Kerülendő: `resources/views/rendezvenyek.blade.php`

### Kontrollerek és modellek → **PascalCase**, egyes szám

```php
class EventController extends Controller { ... }
class Event extends Model { ... }
```

❌ Kerülendő: `class Esemeny`, `class EsemenyekController`

### Adatbázis táblák → **snake_case**, többes szám

```
events
event_categories
ticket_types
```

❌ Kerülendő: `rendezvenyek`, `esemeny_kategoriak`

### Adatbázis oszlopok / model attribútumok → **snake_case**

```php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->dateTime('start_date');
    $table->string('location');
    $table->timestamps();
});
```

❌ Kerülendő: `cim`, `leiras`, `datum`, `helyszin`

### Változók PHP-ban → **camelCase**, angolul

```php
$upcomingEvents = Event::where('start_date', '>', now())->get();
$totalTicketCount = $event->tickets->count();
```

❌ Kerülendő: `$kozelgo_esemenyek`, `$osszes_jegy`

---

## 3. Mi marad magyarul?

Minden, amit **a végfelhasználó lát a böngészőben**:

```blade
<h1>Rendezvények</h1>
<p>Itt fognak megjelenni a közelgő eseményeink.</p>
<button>Jegy vásárlása</button>
```

Ide tartozik:

- oldalcímek, `<h1>`, bekezdések
- gombfeliratok, menüpontok
- hibaüzenetek, amiket a felhasználó lát (pl. validációs üzenetek szövege)
- e-mail sablonok szövege

---

## 4. Gyakori hibák, amikre érdemes figyelni számonkéréskor

- Vegyes elnevezés egy fájlon belül (pl. `$esemeny->start_date`) — legyen következetes
- Magyar ékezetes karakter fájlnévben vagy változónévben (`résztvevő`, `árajánlat`) — soha ne forduljon elő kódban
- Egyes/többes szám felcserélése: modell **egyes szám** (`Event`), tábla **többes szám** (`events`)
- Route név és view név véletlen szétcsúszása (pl. `/events` route, de `event.blade.php` fájl — legyen `events.blade.php`, ha a route is többes számú)
