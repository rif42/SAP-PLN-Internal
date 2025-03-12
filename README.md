# SAP-PLN-Internal

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- SQLite

### PHP dan Composer
jika anda sudah punya php dan composer, skip langkah ini
jika belum punya, ikuti langkah dibawah:
- jalankan powershell atau terminal menggunakan admin permission
- jalankan perintah ini di terminal
  ```bash
  Set-ExecutionPolicy Bypass -Scope Process
  ```

- jalankan perintah ini untuk menginstall chocolatey - [referensi](https://chocolatey.org/install#individual)
  ```bash
  Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
  ```
  
- masih di terminal yang sama, jalankan untuk menginstall php
  ```bash
  choco install php
  ```
  
- jalankan untuk menginstall composer
  ```bash
  choco install composer
  ```
  
- jika instalasi sudah selesai, restart komputer

### SQLite
jika anda sudah punya sqlite, skip langkah ini
jika belum punya, ikuti langkah dibawah:
- download file `Precompiled Binaries for Windows` dari link ini - https://www.sqlite.org/download.html
- download `sqlite-shell-win32.zip` dan `sqlite-dll-win32.zip` file
- buat sebuah folder di `C:\sqlite` dan unzip kedua zip diatas ke dalam folder
- tekan windows button dan masukkan `environment variables` di kolom search
- pilih `edit the systme environment variables`
- di dalam window pop up, klik `Environment Variables`
- di user variables, cari variable `Path`, lalu double click
- klik `New` dan paste `C:\sqlite` lalu klik ok
- restart komputer

## 🔧 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/rif42/SAP-PLN-Internal.git
   cd SAP-PLN-Internal
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Create environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Create SQLite database:
   ```bash
   touch database/database.sqlite
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Seed the database (optional):
   ```bash
   php artisan db:seed
   ```

## 🏃‍♂️ Development

Run the development server:

```bash
php artisan serve
```
