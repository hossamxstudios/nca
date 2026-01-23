# New Cairo Archive System (NCA)

A comprehensive document archive management system built with Laravel 12, designed for managing clients, lands, and PDF documents with physical location tracking.

## 📊 Module Status & Features

### 1. 👥 Users Module (`/admin/users`)
| Feature | Status | Description |
|---------|--------|-------------|
| List Users | ✅ Complete | Table/Card view with pagination, search, filters |
| Create User | ✅ Complete | Modal with role assignment |
| Edit User | ✅ Complete | Modal with role update |
| Delete User | ✅ Complete | Soft delete with confirmation modal |
| Bulk Upload | ✅ Complete | CSV/XLSX import with validation |
| Bulk Delete | ✅ Complete | Multi-select with confirmation |
| Role Management | ✅ Complete | Spatie permissions integration |

**Mechanism:** Users are managed via `UserController`. Roles are assigned using Spatie Laravel Permission. Soft deletes enabled for data recovery.

---

### 2. 👤 Clients Module (`/admin/clients`)
| Feature | Status | Description |
|---------|--------|-------------|
| List Clients | ✅ Complete | Table/Card view with search, governorate filter |
| Create Client | ✅ Complete | Modal with lands, auto-generate client code |
| Edit Client | ✅ Complete | Modal with validation |
| Delete Client | ✅ Complete | Soft delete with confirmation modal |
| View Details | ✅ Complete | Full-screen modal showing all lands, files, sub-files |
| Upload File | ✅ Complete | Modal to upload PDF to client's land |
| File Viewer | ✅ Complete | Nested iframe modal for PDF viewing |
| Bulk Operations | ✅ Complete | Bulk delete, restore, force delete |
| Toast Notifications | ✅ Complete | iziToast RTL support for all actions |
| **Barcode Scanner Search** | ✅ Complete | Search files by barcode using external scanner or manual input |

**Mechanism:** 
- `ClientController` handles CRUD with JSON responses
- Lands are created inline during client creation
- Files are linked through lands
- View modal shows hierarchical data: Client → Lands → Files → Sub-files
- PDF files can be viewed in iframe without navigation

#### 📱 Barcode Scanner Search Feature (Added 2026-01-20)

**Overview:**
The Clients page now includes a dedicated barcode scanner search section that allows users to quickly find files and their associated clients by scanning a barcode using an external USB/Bluetooth barcode scanner device, or by manually entering the barcode.

**How It Works:**

1. **External Barcode Scanner Support:**
   - Connect any USB or Bluetooth barcode scanner to your computer
   - The scanner acts as a keyboard input device
   - When you scan a barcode, the scanner rapidly types the barcode characters followed by Enter
   - The system automatically detects scanner input (rapid keystroke detection < 50ms between characters)
   - Search is triggered automatically when Enter is received

2. **Manual Input:**
   - Type the barcode directly into the search field
   - Press Enter or click the "بحث" (Search) button
   - The barcode format is: `NCA-YYYYMMDD-XXXXX` (e.g., `NCA-20260120-45832`)

3. **Search Results:**
   - A modal displays comprehensive information:
     - **File Details:** Barcode, file name, type, page numbers, creation date
     - **Physical Location:** Room, Lane, Stand, Rack
     - **Client Information:** Name, client code, national ID, mobile, address
     - **Land Information:** Plot number, governorate, city, district, zone, area
   - Quick action button to view the full client profile

**UI Components:**

```
┌─────────────────────────────────────────────────────────────────┐
│  📊 البحث بالباركود (الماسح الضوئي)                              │
├─────────────────────────────────────────────────────────────────┤
│  [🔍] [امسح الباركود أو أدخله يدوياً...        ] [بحث]          │
│                                                                  │
│  ℹ️ استخدم جهاز الماسح الضوئي لمسح الباركود                     │
│     أو أدخل رقم الباركود يدوياً ثم اضغط Enter                   │
│                                                                  │
│                                    [✓ جاهز للمسح]               │
└─────────────────────────────────────────────────────────────────┘
```

**Keyboard Shortcuts:**
- **F2:** Focus on barcode input field from anywhere on the page
- **Enter:** Execute search when input is focused

**Status Indicators:**
- 🟢 **جاهز للمسح** (Ready to scan) - System is ready for barcode input
- 🟡 **جاري المسح...** (Scanning...) - Barcode is being processed
- 🟢 **تم العثور على الملف** (File found) - Search successful
- 🔴 **لم يتم العثور** (Not found) - No file matches the barcode

**API Endpoint:**

```
GET /admin/clients/search-barcode?barcode={barcode}
```

**Response (Success):**
```json
{
  "success": true,
  "file": {
    "id": 123,
    "barcode": "NCA-20260120-45832",
    "file_name": "عقد بيع",
    "is_main_file": true,
    "page_number": 1,
    "pages_count": 5,
    "room": { "name": "غرفة 1" },
    "lane": { "name": "ممر أ" },
    "stand": { "name": "ستاند 1" },
    "rack": { "name": "رف 3" },
    "land": {
      "plot_number": "A-123",
      "governorate": { "name": "القاهرة" },
      "city": { "name": "القاهرة الجديدة" },
      "client": {
        "name": "أحمد محمد",
        "client_code": "CLT-00001",
        "national_id": "12345678901234"
      }
    }
  },
  "client": { ... },
  "land": { ... },
  "message": "تم العثور على الملف بنجاح"
}
```

**Response (Not Found):**
```json
{
  "success": false,
  "message": "لم يتم العثور على ملف بهذا الباركود: NCA-20260120-00000"
}
```

**Files Modified:**
- `app/Http/Controllers/Admin/ClientController.php` - Added `searchByBarcode()` method
- `routes/web.php` - Added `/admin/clients/search-barcode` route
- `resources/views/dashboards/admin/pages/clients/index.blade.php` - Added barcode scanner UI section
- `resources/views/dashboards/admin/pages/clients/partials/scripts.blade.php` - Added barcode search JavaScript

**Technical Implementation:**

1. **Scanner Detection:**
   ```javascript
   const SCANNER_THRESHOLD = 50; // Max ms between keystrokes
   // External scanners type much faster than humans
   // If keystrokes arrive < 50ms apart, it's likely a scanner
   ```

2. **Auto-focus:**
   - Input field auto-focuses on page load
   - F2 keyboard shortcut for quick focus from anywhere

3. **Clear & Refocus:**
   - After each search, input is cleared and refocused
   - Allows continuous scanning without manual intervention

**Compatible Barcode Scanners:**
- Any USB barcode scanner that acts as keyboard input
- Bluetooth barcode scanners
- Mobile barcode scanner apps (that emulate keyboard)
- Scanners configured to append Enter key after scan

---

### 3. 🏠 Lands Module (Inline with Clients)
| Feature | Status | Description |
|---------|--------|-------------|
| Create Land | ✅ Complete | Created with client in modal |
| Edit Land | ✅ Complete | Via client edit |
| Delete Land | ✅ Complete | Cascade with client |
| Geographic Linking | ✅ Complete | Governorate → City cascading |

**Mechanism:** Lands belong to clients and are linked to geographic areas. Each land can have multiple files attached.

---

### 4. 🗺️ Geographic Areas Module (`/admin/geographic-areas`)
| Feature | Status | Description |
|---------|--------|-------------|
| List Governorates | ✅ Complete | Table with city/district counts |
| Create Governorate | ✅ Complete | Modal form |
| Edit Governorate | ✅ Complete | Modal form |
| Delete Governorate | ✅ Complete | Confirmation modal with warning |
| View Cities | ✅ Complete | Modal showing cities with accordion |
| Add City | ✅ Complete | Modal linked to governorate |
| Edit City | ✅ Complete | Modal form |
| Delete City | ✅ Complete | Confirmation modal |
| Add District | ✅ Complete | Modal linked to city |
| Delete District | ✅ Complete | Inline confirmation |

**Mechanism:**
- Hierarchical structure: Governorate → City → District → Zone → Area
- `GeographicAreaController` handles all levels
- Show modal displays cities with nested districts in accordion
- All operations return JSON for AJAX handling

---

### 5. 📁 Files Module (`/admin/files`)
| Feature | Status | Description |
|---------|--------|-------------|
| List Files | ✅ Complete | Table with status, client, pages count, **barcode** |
| Upload PDF | ✅ Complete | Modal with client/land selection |
| Item Selection | ✅ Complete | Checkboxes with page range (from/to) |
| Sub-file Creation | ✅ Complete | Cut pages to create sub-files |
| View File | ✅ Complete | Redirect to file details with barcode display |
| Delete File | ✅ Complete | Confirmation with soft delete |
| Processing Status | ✅ Complete | Shows processing/completed/failed |
| **Barcode System** | ✅ Complete | Auto-generated unique barcode per file |
| **Barcode Search** | ✅ Complete | Search files by scanning/entering barcode |

**Mechanism:**
- PDF files are uploaded via `FileController`
- When item checkbox is checked, from/to page fields appear
- `ProcessPdfJob` runs in background to:
  - Split PDF into individual pages
  - Create thumbnails using Imagick
  - Create sub-files from specified page ranges
- Files are stored using Spatie Media Library

#### 🔖 Barcode Feature (Added 2026-01-19)

**Overview:**
Each file uploaded to the system is automatically assigned a unique barcode. This barcode can be used to quickly identify and retrieve files using a barcode scanner or manual search.

**Barcode Format:**
```
NCA-YYYYMMDD-XXXXX
```
- `NCA` - System prefix (New Cairo Archive)
- `YYYYMMDD` - Date of file upload
- `XXXXX` - Random 5-digit unique identifier

**Example:** `NCA-20260119-45832`

**Features:**
1. **Auto-Generation:** Barcode is automatically generated when a file is uploaded
2. **Unique Constraint:** Database enforces uniqueness to prevent duplicates
3. **Search Integration:** Search box in files list searches by barcode, file name, or client name
4. **Display in Table:** Barcode shown as dark badge in files table
5. **Display in Modal:** Prominent barcode display with copy button in file details modal
6. **API Endpoint:** `/admin/files/barcode/{barcode}` returns file with client info

**API Usage:**
```javascript
// Search by barcode
fetch('/admin/files/barcode/NCA-20260119-45832')
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      console.log('File:', data.file);
      console.log('Client:', data.file.client);
      console.log('PDF URL:', data.pdf_url);
    }
  });
```

**Database:**
- Column: `files.barcode` (VARCHAR 20, UNIQUE, NULLABLE, INDEXED)
- Migration: `2026_01_19_165911_add_barcode_to_files_table.php`

**Model Methods:**
```php
// Generate new barcode
$barcode = File::generateBarcode();

// Find file by barcode
$file = File::findByBarcode('NCA-20260119-45832');

// Scope for searching
$files = File::byBarcode('NCA-2026')->get();
```

**Files Modified:**
- `app/Models/File.php` - Added barcode to fillable, generateBarcode(), findByBarcode(), scopeByBarcode()
- `app/Http/Controllers/Admin/FileController.php` - Added barcode generation in store(), findByBarcode() method, search includes barcode
- `routes/web.php` - Added `/admin/files/barcode/{barcode}` route
- `resources/views/dashboards/admin/pages/files/index.blade.php` - Added barcode column and display in modal

---

### 6. 📋 Items Module (`/admin/items`)
| Feature | Status | Description |
|---------|--------|-------------|
| List Items | ✅ Complete | Table with pagination |
| Create Item | ✅ Complete | Modal form |
| Edit Item | ✅ Complete | Modal form |
| Delete Item | ✅ Complete | Confirmation modal |

**Mechanism:** Items are document content types (57 types seeded). Used to tag file contents with page ranges.

---

### 7. 🏢 Physical Locations Module (`/admin/physical-locations`)
| Feature | Status | Description |
|---------|--------|-------------|
| Hierarchical Tree | ✅ Complete | Room → Lane → Stand → Rack |
| Create Room | ✅ Complete | Modal form |
| Add Lane/Stand/Rack | ✅ Complete | Nested modals |
| View Rack Files | ✅ Complete | Shows files in rack |
| Delete Location | ✅ Complete | Cascade delete |

**Mechanism:** Physical locations track where paper documents are stored. Hierarchical structure allows drilling down to specific rack.

---

### 8. 📥 Imports Module (`/admin/imports`)
| Feature | Status | Description |
|---------|--------|-------------|
| Upload Excel | ✅ Complete | CSV/XLSX with validation preview |
| Download Template | ✅ Complete | Generate template with headers |
| Validation Preview | ✅ Complete | Show errors before import |
| Execute Import | ✅ Complete | Background job processing |
| View Import Status | ✅ Complete | Progress and error tracking |

**Mechanism:**
- `ImportController` handles file upload and validation
- `ProcessImportJob` processes rows in background
- Templates generated dynamically using PhpSpreadsheet
- Supports: clients, lands, geographic areas

---

### 9. 🛡️ Roles & Permissions
| Role | Permissions |
|------|-------------|
| Super Admin | All permissions (full access) |
| Manager | CRUD on clients, lands, files; view users |
| Employee | View/create clients and files |
| Viewer | Read-only access |

**Mechanism:** Spatie Laravel Permission with 30+ permissions. Checked using `@can` directives in Blade and middleware.

---

## 🔧 Technical Architecture

### Controllers Pattern
- All controllers use non-resource pattern with explicit methods
- CRUD: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`
- Bulk: `bulkDelete`, `bulkRestore`, `bulkForceDelete`
- All return JSON for AJAX modal operations

### Validation
- Form Request classes for all store/update operations
- Arabic error messages
- Client-side + server-side validation

### Database
- Soft deletes on all managed models
- DB transactions with try-catch in all write operations
- Eager loading to prevent N+1 queries

### Background Jobs
- `ProcessPdfJob` - PDF splitting with Imagick
- `ProcessImportJob` - Excel import processing
- Queue configuration in `.env`

### Media Handling
- Spatie Media Library for all file attachments
- Collections: `documents`, `page_images`, `thumbnails`

---

## Changes Log

### 2026-01-16: Archive System Implementation

**Database Migrations Created:**
- `governorates`, `cities`, `districts`, `zones`, `areas` - Geographic hierarchy
- `clients` - Client management with national ID, codes, contact info
- `lands` - Land records linked to clients and geographic areas
- `rooms`, `lanes`, `stands`, `racks` - Physical location hierarchy
- `items` - Content types for document classification
- `files`, `file_items` - PDF files with page splitting and item tagging
- `imports` - Excel import tracking

**Models Created:**
- `Governorate`, `City`, `District`, `Zone`, `Area` - Geographic models
- `Client`, `Land` - Core business models
- `Room`, `Lane`, `Stand`, `Rack` - Physical location models
- `File`, `FileItem`, `Item` - Document management models
- `Import` - Import tracking model

**Controllers Created (Admin namespace):**
- `ClientController` - Full CRUD with bulk operations
- `LandController` - Land management with geographic cascading
- `FileController` - PDF upload and processing
- `PhysicalLocationController` - Hierarchical location management
- `GeographicAreaController` - Geographic area CRUD
- `ItemController` - Content type management
- `ImportController` - Excel import with validation

**Jobs Created:**
- `ProcessPdfJob` - Background PDF splitting using Imagick
- `ProcessImportJob` - Background Excel import processing

**Views Created:**
- `dashboards/admin/pages/clients/` - Client management with modals
- `dashboards/admin/pages/physical-locations/` - Hierarchical tree table
- `dashboards/admin/pages/dashboard/` - Admin dashboard with stats

**Seeders Created:**
- `PermissionSeeder` - 30+ permissions with 4 roles (Super Admin, Manager, Employee, Viewer)
- `GovernorateSeeder` - All Egyptian governorates with major cities
- `ItemSeeder` - 15 common document types

**Tests:**
- `ClientControllerTest` - 16 tests covering all CRUD and bulk operations (All Passing)

**Packages Added:**
- `phpoffice/phpspreadsheet` - Excel import/export support

### 2026-01-16: Additional Views, Tests & Imagick Configuration

**Additional Views Created:**
- `dashboards/admin/pages/geographic-areas/index.blade.php` - Hierarchical tree table for governorates, cities, districts, zones, areas
- `dashboards/admin/pages/items/index.blade.php` - Content types management with CRUD modals
- `dashboards/admin/pages/files/index.blade.php` - PDF file management with upload, processing status, and preview
- `dashboards/admin/pages/imports/index.blade.php` - Excel import with validation preview and sample file downloads

**Factories Created:**
- `ClientFactory`, `LandFactory`, `GovernorateFactory`, `CityFactory`, `DistrictFactory`
- `RoomFactory`, `LaneFactory`, `StandFactory`, `RackFactory`
- `ItemFactory`

**Tests Created:**
- `ClientControllerTest` - 16 tests (All Passing)
- `ItemControllerTest` - 10 tests (7 CRUD tests passing)
- `PhysicalLocationControllerTest` - 14 tests (11 CRUD tests passing)
- `GeographicAreaControllerTest` - 11 tests (8 CRUD tests passing)

**PDF Processing Configuration:**
- Created `config/pdf.php` for Imagick PDF processing settings
- Updated `ProcessPdfJob` to use configurable settings

**Environment Variables for PDF Processing:**
```env
PDF_RESOLUTION=150
PDF_FORMAT=jpg
PDF_QUALITY=85
PDF_MAX_PAGES=500
PDF_MEMORY_LIMIT=512
PDF_TIMEOUT=300
PDF_BACKGROUND=white
PDF_STORAGE_DISK=public
```

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
