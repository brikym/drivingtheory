# Procházení otázek - Nová funkcionalita

## Přehled
Byla vytvořena nová záložka "Procházení otázek" v aplikaci Autoškola E-testy, která umožňuje uživatelům procházet otázky podle kategorií.

## Funkce

### 1. Seznam kategorií (`/questions`)
- Zobrazuje všechny aktivní kategorie
- Každá kategorie obsahuje:
  - Název kategorie (v češtině)
  - Popis kategorie (pokud je k dispozici)
  - Počet otázek v kategorii
  - Tlačítko pro zobrazení otázek

### 2. Zobrazení otázek v kategorii (`/questions/category/{category}`)
- Zobrazuje všechny otázky v dané kategorii
- Každá otázka obsahuje:
  - Kód otázky
  - Text otázky
  - Počet bodů
  - **Mediální obsah (obrázky/videa) - pokud je k dispozici**
  - Seznam možných odpovědí
  - **Mediální obsah odpovědí (obrázky/videa) - pokud je k dispozici**

## Technické detaily

### Kontroler
- `QuestionController` - nový kontroler pro zobrazení kategorií a otázek
- Metody:
  - `index()` - zobrazí seznam kategorií
  - `showCategory()` - zobrazí otázky v konkrétní kategorii s mediálním obsahem

### Routy
- `GET /questions` - seznam kategorií
- `GET /questions/category/{category}` - otázky v kategorii

### Views
- `resources/views/questions/index.blade.php` - seznam kategorií
- `resources/views/questions/category.blade.php` - otázky v kategorii s mediálním obsahem

### Navigace
- Přidána nová záložka "Procházení otázek" do hlavní navigace
- Aktualizován welcome screen s odkazem na novou funkcionalnost

## Mediální obsah

### Otázky
- Automaticky se zobrazují obrázky a videa připojené k otázce
- Podporované formáty: obrázky (PNG, JPG, GIF) a videa (MP4)
- Mediální obsah se zobrazuje pod textem otázky

### Odpovědi
- Každá odpověď může mít vlastní mediální obsah
- Obrázky a videa se zobrazují pod textem odpovědi
- Obrázky odpovědí mají omezenou výšku (max-h-32) pro lepší layout

## Použití

1. **Spuštění aplikace:**
   ```bash
   php artisan serve
   ```

2. **Přístup k funkcionalnosti:**
   - Hlavní stránka: `http://localhost:8000/`
   - Seznam kategorií: `http://localhost:8000/questions`
   - Kliknutím na kategorii se zobrazí její otázky

3. **Navigace:**
   - Použijte tlačítko "Zpět na kategorie" pro návrat k seznamu kategorií
   - Hlavní navigace umožňuje přepínání mezi funkcemi

## Požadavky
- Laravel 10+
- Databáze s tabulkami: `categories`, `questions`, `question_categories`, `category_translations`, `question_translations`, `answers`, `answer_translations`, `media_contents`
- Aktivní kategorie a otázky v databázi
- Mediální soubory uložené v `storage/app/public/`

## Poznámky
- Aplikace automaticky načítá české překlady (`locale = 'cs'`)
- Zobrazují se pouze aktivní kategorie a otázky (`is_active = true`)
- **Mediální obsah se zobrazuje automaticky podle typu (obrázek/video)**
- **Odpovědi mohou mít vlastní mediální obsah**
- Design používá Tailwind CSS pro moderní vzhled
- Responzivní design pro všechny velikosti obrazovek
