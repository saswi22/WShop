# Setup Panduan - Hospital Profile Website

## Daftar Isi
1. [Backend Laravel Setup](#backend-laravel-setup)
2. [Frontend Vue Setup](#frontend-vue-setup)
3. [Database Migration](#database-migration)
4. [API Endpoints](#api-endpoints)
5. [Fitur & Penjelasan](#fitur--penjelasan)

---

## Backend Laravel Setup

### 1. Install Dependencies
```bash
cd laravel-json-api
composer install
```

### 2. Environment Configuration
Buat file `.env` dari `.env.example`:
```bash
cp .env.example .env
php artisan key:generate
```

Setup database di `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rumah_sakit
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations
```bash
php artisan migrate
```

File migrations yang dibuat:
- `2025_11_25_000001_create_doctors_table.php`
- `2025_11_25_000002_create_services_table.php`
- `2025_11_25_000003_create_schedules_table.php`
- `2025_11_25_000004_create_galleries_table.php`
- `2025_11_25_000005_create_news_table.php`
- `2025_11_25_000006_create_careers_table.php`
- `2025_11_25_000007_create_contacts_table.php`

### 4. Create Admin User
```bash
php artisan tinker
> App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')])
> exit
```

### 5. Start Laravel Server
```bash
php artisan serve
```

Server akan jalan di `http://localhost:8000`

---

## Frontend Vue Setup

### 1. Install Dependencies
```bash
cd vue-argon-dashboard-laravel
npm install
```

### 2. Configure API Base URL
Edit `src/main.js` untuk setup axios:
```javascript
import axios from 'axios'

const axiosInstance = axios.create({
  baseURL: 'http://localhost:8000'
})

// Add token to requests
axiosInstance.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

app.config.globalProperties.$axios = axiosInstance
```

### 3. Update Router
Replace `src/router/index.js` dengan content dari `src/router/hospital-router.js`

### 4. Start Development Server
```bash
npm run dev
```

Server akan jalan di `http://localhost:5173`

---

## Database Migration

### Table Structure

**doctors** table:
```
id (Primary Key)
name (string)
specialization (string)
phone (string)
email (string)
bio (text)
photo (string)
education (string)
experience (string)
license_number (string)
is_active (boolean)
timestamps
```

**services** table:
```
id (Primary Key)
name (string)
description (text)
icon (string)
image (string)
details (text)
is_active (boolean)
timestamps
```

**schedules** table:
```
id (Primary Key)
doctor_id (Foreign Key)
day (enum: Monday-Sunday)
start_time (time)
end_time (time)
location (string)
is_active (boolean)
timestamps
```

**galleries** table:
```
id (Primary Key)
title (string)
description (text)
image (string)
category (string)
is_active (boolean)
timestamps
```

**news** table:
```
id (Primary Key)
title (string)
slug (string, unique)
content (text)
featured_image (string)
user_id (Foreign Key)
category (string)
excerpt (text)
is_published (boolean)
published_at (timestamp)
timestamps
```

**careers** table:
```
id (Primary Key)
title (string)
slug (string, unique)
position (string)
description (text)
requirements (text)
benefits (text)
department (string)
job_type (string: Full-time, Part-time, Contract)
location (string)
is_active (boolean)
deadline (timestamp)
timestamps
```

**contacts** table:
```
id (Primary Key)
name (string)
email (string)
phone (string)
subject (string)
message (text)
status (enum: unread, read, replied)
timestamps
```

---

## API Endpoints

### Public Endpoints (No Authentication Required)

#### Doctors
```
GET    /api/v2/doctors              - List all doctors
GET    /api/v2/doctors/{id}         - Get doctor details
```

#### Services
```
GET    /api/v2/services             - List all services
GET    /api/v2/services/{id}        - Get service details
```

#### Schedules
```
GET    /api/v2/schedules            - List all schedules
GET    /api/v2/schedules/{id}       - Get schedule details
```

#### Galleries
```
GET    /api/v2/galleries            - List all galleries
GET    /api/v2/galleries/{id}       - Get gallery details
```

#### News
```
GET    /api/v2/news                 - List all published news
GET    /api/v2/news/{id}            - Get news details
```

#### Careers
```
GET    /api/v2/careers              - List all active career openings
GET    /api/v2/careers/{id}         - Get career details
```

#### Contacts
```
POST   /api/v2/contacts             - Submit contact message
```

### Protected Endpoints (Requires Authentication)

#### Doctors
```
POST   /api/v2/doctors              - Create new doctor
PUT    /api/v2/doctors/{id}         - Update doctor
DELETE /api/v2/doctors/{id}         - Delete doctor
```

#### Services
```
POST   /api/v2/services             - Create new service
PUT    /api/v2/services/{id}        - Update service
DELETE /api/v2/services/{id}        - Delete service
```

#### Schedules
```
POST   /api/v2/schedules            - Create new schedule
PUT    /api/v2/schedules/{id}       - Update schedule
DELETE /api/v2/schedules/{id}       - Delete schedule
```

#### Galleries
```
POST   /api/v2/galleries            - Create new gallery
PUT    /api/v2/galleries/{id}       - Update gallery
DELETE /api/v2/galleries/{id}       - Delete gallery
```

#### News
```
POST   /api/v2/news                 - Create new news
PUT    /api/v2/news/{id}            - Update news
DELETE /api/v2/news/{id}            - Delete news
```

#### Careers
```
POST   /api/v2/careers              - Create new career opening
PUT    /api/v2/careers/{id}         - Update career
DELETE /api/v2/careers/{id}         - Delete career
```

#### Contacts
```
GET    /api/v2/contacts             - List all contacts
GET    /api/v2/contacts/{id}        - Get contact details
PUT    /api/v2/contacts/{id}        - Update contact status
DELETE /api/v2/contacts/{id}        - Delete contact
```

#### Authentication
```
POST   /api/v2/login                - User login
POST   /api/v2/register             - User registration
POST   /api/v2/logout               - User logout
POST   /api/v2/password-forgot      - Forgot password
POST   /api/v2/password-reset       - Reset password
```

---

## Fitur & Penjelasan

### 👥 User Dashboard (Public)
- **Halaman Beranda**: Tampilan utama dengan stats dan preview layanan
- **Daftar Dokter**: Lihat semua dokter dengan filter spesialisasi
- **Jadwal Praktik**: Lihat jadwal praktik dokter
- **Layanan Medis**: Daftar semua layanan yang tersedia
- **Berita & Artikel**: Baca berita terbaru
- **Lowongan Karir**: Lihat dan apply lowongan karir
- **Hubungi Kami**: Form kontak untuk mengirim pesan

### 🔐 Admin Dashboard (Protected)
Login dengan kredensial:
- Email: `admin@example.com`
- Password: `password`

#### Admin Features:
1. **Manajemen Dokter**: CRUD dokter, lihat jadwal
2. **Manajemen Layanan**: CRUD layanan medis
3. **Manajemen Berita**: CRUD berita, publikasi/draft
4. **Manajemen Karir**: CRUD lowongan kerja
5. **Manajemen Pesan**: Lihat pesan masuk, ubah status
6. **Dashboard**: Overview statistik

### 📱 Responsive Design
- Bootstrap 5 untuk styling
- Mobile-friendly interface
- Smooth navigation dan transitions

---

## File Struktur yang Dibuat

### Backend (Laravel)
```
app/
├── Http/Controllers/Api/
│   ├── DoctorController.php
│   ├── ServiceController.php
│   ├── ScheduleController.php
│   ├── GalleryController.php
│   ├── NewsController.php
│   ├── CareerController.php
│   └── ContactController.php
└── Models/
    ├── Doctor.php
    ├── Service.php
    ├── Schedule.php
    ├── Gallery.php
    ├── News.php
    ├── Career.php
    └── Contact.php

database/migrations/
├── 2025_11_25_000001_create_doctors_table.php
├── 2025_11_25_000002_create_services_table.php
├── 2025_11_25_000003_create_schedules_table.php
├── 2025_11_25_000004_create_galleries_table.php
├── 2025_11_25_000005_create_news_table.php
├── 2025_11_25_000006_create_careers_table.php
└── 2025_11_25_000007_create_contacts_table.php

routes/
└── api.php (updated dengan semua endpoints)
```

### Frontend (Vue)
```
src/views/
├── UserDashboard.vue (Layout user)
├── HomePage.vue
├── DoctorsPage.vue
├── ServicesPage.vue
├── NewsPage.vue
├── CareersPage.vue
├── ContactPage.vue
├── AdminLogin.vue
├── AdminDashboard.vue (Layout admin)
├── AdminDoctors.vue
├── AdminServices.vue
├── AdminNews.vue
├── AdminCareers.vue
└── AdminContacts.vue

router/
└── hospital-router.js (Updated routes configuration)
```

---

## Next Steps

1. **Setup Database**: Run migrations
2. **Create Admin User**: Gunakan tinker command
3. **Upload Assets**: Tambahkan foto/gambar untuk dokter, layanan, dll
4. **Customize Content**: Edit informasi rumah sakit (alamat, jam operasional, dll)
5. **Deploy**: Siapkan server production

---

## Notes
- Pastikan CORS configuration sudah benar di Laravel
- Token authentication menggunakan Laravel default auth system
- File uploads bisa diintegrasikan dengan storage filesystem
- Email notifications bisa dikonfigurasi di config/mail.php

