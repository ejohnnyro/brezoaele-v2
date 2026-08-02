# Brezoaele.ro — Portal Web Civic & Economic Local

**Versiunea:** 2.0 (Theme `brezoaele-v2`)
**Site live:** [https://brezoaele.ro](https://brezoaele.ro)
**GitHub Theme Repository:** [https://github.com/ejohnnyro/brezoaele-v2](https://github.com/ejohnnyro/brezoaele-v2)

---

## 📌 Despre Proiect

**Brezoaele.ro** este un portal civic și economic independent dedicat comunei **Brezoaele, județul Dâmbovița**. Proiectul conectează administrația locală, cetățenii activi, antreprenorii locali și investitorii printr-o platformă digitală completă.

Tema WordPress `brezoaele-v2` este dezvoltată complet custom — fără page builders și fără template-uri cumpărate — bazată exclusiv pe PHP, CSS vanilla și JavaScript vanilla cu Leaflet.js pentru hărți interactive.

---

## 🏗️ Arhitectura Proiectului

```
brezoaele.ro/
├── brezoaele-v2/           # Tema WordPress custom (principalul repo)
│   ├── functions.php       # CPT-uri, taxonomii, hooks, seeding categorii
│   ├── header.php / footer.php
│   ├── front-page.php      # Pagina principală
│   ├── archive-anunt.php   # Piața Locală (grilă anunțuri + filtre categorii)
│   ├── single-anunt.php    # Pagina individuală anunț cu WhatsApp
│   ├── page-adauga-anunt.php     # Formular adăugare anunț (Gratuit / Premium)
│   ├── page-solicita-adaugare-afacere.php  # Formular afacere pe hartă (OP/Cash/Card)
│   ├── page-contul-meu.php       # Panou utilizator (Google OAuth)
│   ├── template-harta-servicii.php    # Harta Leaflet.js
│   ├── inc/class-auth.php         # Sistem autentificare Google OAuth
│   └── style.css
│
├── plugins/
│   ├── brezoaele-payments/         # Plugin custom plăți EuPlatesc + OP + Cash
│   │   └── includes/
│   │       ├── class-euplatesc.php
│   │       ├── class-orders-db.php
│   │       ├── class-admin-settings.php  # Panoul admin comenzi + Marchează Achitată
│   │       ├── class-expiration-cron.php
│   │       └── class-invoice-downloader.php
│   └── ai-article-generator/       # Plugin generare articole cu AI (Gemini/Claude)
│
├── deploy_theme.py         # Script FTP deploy → ftp.brezoaele.ro
├── development-plan.md     # Plan dezvoltare (arhivă istorică)
├── DOCUMENTATIE_TEHNICA.md # Documentație tehnică detaliată
└── .env.example            # Template variabile de mediu
```

---

## 🚀 Funcționalități Principale

### 🏪 Piața Locală de Anunțuri (`/anunturi/`)
- **10 categorii ierarhice** cu subcategorii: Auto, Imobiliare, Agricultură, Utilaje, Servicii, Produse, Evenimente, Angajări, Animale, Diverse.
- **Pachete Gratuit & ⭐ PREMIUM** (10 LEI / 30 zile):
  - Premium: Fixat în capul listei (Sticky Top), ecuson vizual, până la 10 fotografii HD.
  - Gratuit: 30 zile, maxim 3 fotografii.
- **Compresie & redimensionare automată** imagini la max 1080×1080px (aspect ratio păstrat, JPEG 85%).
- **Buton WhatsApp** cu mesaj pre-definit: *"Buna ziua! Sunt interesat de anunțul [Titlu] pe Brezoaele.ro"*.

### 🗺️ Harta Serviciilor Locale (`/solicita-adaugare-afacere/`)
- Formular de înregistrare afacere cu **Pin Picker interactiv Leaflet** (click/drag pin pe hartă pentru selectarea coordonatelor).
- Galerie foto: Logo/Imagine Reprezentativă + până la **5 fotografii suplimentare de galerie**.
- **3 Metode de Plată** (149 LEI / an):
  - 💳 **Card Online** via EuPlatesc.ro (activare instant).
  - 🏦 **Ordin de Plată (OP)** — IBAN: `RO70BTRLRONCRT0CK9121401` (Banca Transilvania, ECOMPLEX.RO SRL).
  - 💵 **Cash / Numerar** — instrucțiuni afișate pe ecran + email de confirmare.
- **Card beneficii** vizibil în coloana dreapta pe desktop.

### 👤 Sistem de Conturi Utilizatori
- Autentificare **Google OAuth** (fără parolă).
- Panou personal: anunțuri proprii, comenzi & facturi.

### 💳 Plugin Plăți (`brezoaele-payments`)
- Integrare **EuPlatesc.ro** (card online).
- Suport comenzi **OP și Cash** cu confirmare email automată.
- WP Admin: **`✅ Marchează ca Achitată`** — activare 1-click afacere/anunț.
- Curățare automată comenzi `pending` mai vechi de 7 zile (cron zilnic).

### 🤖 Plugin Generare Articole AI (`ai-article-generator`)
- Generare articole jurnalistice cu Gemini / Claude / OpenAI.
- Fallback automat pe toți furnizorii disponibili.

---

## ⚙️ Deploy & Configurare

### Variabile de Mediu (`.env`)
Copiază `.env.example` în `.env` și completează:

```
FTP_HOST=ftp.brezoaele.ro
FTP_USER=...
FTP_PASS=...
```

### Deploy pe Server
```bash
python3 deploy_theme.py      # Sincronizează tema + plugin-ul de plăți pe FTP
```

---

## 🧾 Facturare — ECOMPLEX.RO SRL
Toate plățile sunt facturate de **ECOMPLEX.RO SRL**, societate neplătitoare de TVA (TVA 0%). Facturile sunt deductibile ca cheltuieli fără TVA deductibil.

---

## 📄 Documentație Adițională
- [Documentație Tehnică Completă](DOCUMENTATIE_TEHNICA.md)
- [Plan de Dezvoltare (Arhivă)](development-plan.md)
