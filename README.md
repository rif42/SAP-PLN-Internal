# SAP-PLN-Internal

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- SQLite

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
