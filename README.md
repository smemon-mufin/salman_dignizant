# Salman Dignizant — Windows (XAMPP) Setup Guide

## 1. Clone the Repository

```sh
git clone https://github.com/smemon-mufin/salman_dignizant.git
cd salman_dignizant
```

## 2. Install Dependencies

- **XAMPP** (PHP ≥ 7.2, PHP 8+ recommended)
- **Composer** ([getcomposer.org](https://getcomposer.org/))
- **PostgreSQL** (Install for Windows)
- Enable `pdo_pgsql` in `C:\xampp\php\php.ini`:
  ```
  extension=pdo_pgsql
  ```
  Restart Apache after saving.

```sh
composer install
```

## 3. Configure Database

Edit `config/db.php`:
```php
$dsn = "pgsql:host=localhost;port=5432;dbname=sam;user=postgres;password=salman_user";
```
Create DB:
```sql
CREATE DATABASE sam;
```

## 4. Create Tables

Use pgAdmin or `psql`:
```sql
-- Users table
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(32) NOT NULL,
  name VARCHAR(255),
  email VARCHAR(255)
);

-- Projects table
CREATE TABLE projects (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  deadline DATE,
  created_by INTEGER REFERENCES users(id)
);

-- Project Members table
CREATE TABLE project_members (
  project_id INTEGER REFERENCES projects(id) ON DELETE CASCADE,
  user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
  PRIMARY KEY (project_id, user_id)
);

-- Tasks table
CREATE TABLE tasks (
  id SERIAL PRIMARY KEY,
  project_id INTEGER REFERENCES projects(id) ON DELETE CASCADE,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  status VARCHAR(32),
  priority VARCHAR(32),
  assigned_to INTEGER REFERENCES users(id),
  deadline DATE
);
```

## 5. Authentication Setup

- Logic: `config/auth.php`
- Sessions for login/logout
- Create users manually in DB (hash password using PHP `password_hash()`)

## 6. Start WebSocket Server

Install Ratchet:
```sh
composer require cboden/ratchet
```
Start server (in `public/`):
```sh
php server.php
```
Runs at: `ws://localhost:8081`

## 7. Serve the Application (Using XAMPP)

Place `salman_dignizant` folder in `C:\xampp\htdocs\`.

Access:
```
http://localhost/salman_dignizant/public/
```

Or use PHP built-in server:
```sh
php -S localhost:8000 -t public
```

## 8. Access the Application

Open:
```
http://localhost/salman_dignizant/public/
```

## 9. WebSocket Client Integration

Example (`assets/websocket.js`):
```js
const ws = new WebSocket('ws://localhost:8081');

ws.onopen = () => {
  ws.send(JSON.stringify({
    type: 'join',
    userId: 1,
    projectId: 2
  }));
};
```

## Additional Notes

- **UI Files**:
  - Projects: `public/projects.php`
  - Tasks: `public/tasks.php`
- **AJAX Endpoints**:
  - `public/ajax_projects.php`
  - `public/ajax_tasks.php`
- **WebSocket Live Updates**:
  - Sent from `public/server.php`
  - Handled by frontend in `assets/websocket.js`

---

**Troubleshooting & Windows Tips**

- Restart Apache after changing PHP extensions.
- Use CMD/Git Bash as administrator if you face permission issues.
- Allow port `8081` in Windows Firewall for WebSocket.
- Test DB credentials with pgAdmin or `psql`.
