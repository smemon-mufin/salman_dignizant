# salman_dignizant

## Setup Steps

1. **Clone the repository**
    ```sh
    git clone https://github.com/smemon-mufin/salman_dignizant.git
    cd salman_dignizant
    ```

2. **Install dependencies**
    - Make sure you have PHP (>=7.2), Composer, and PostgreSQL installed.
    - Install PHP dependencies:
      ```sh
      composer install
      ```
    - Required PHP extensions: `pdo_pgsql`

3. **Configure Database**
    - The app uses a PostgreSQL database. Database connection settings are in `config/db.php`:
      ```
      $dsn = "pgsql:host=localhost;port=5432;dbname=sam;user=postgres;password=salman_user";
      ```
    - Create the database (`sam`) and set up the schema (see below).

4. **Setup Database Schema**

    ```
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

    -- Project members
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

5. **Configure Authentication**
    - Auth logic is in `config/auth.php`. Sessions are used to manage logins.
    - Default user creation is manual via SQL.

6. **Start the WebSocket Server**
    - The project uses [Ratchet](http://socketo.me/) (install via Composer) for PHP WebSocket server.
    - To start the server:
      ```sh
      php public/server.php
      ```
    - The server listens on port `8081`.

7. **Serve the Project**
    - Serve the `public/` directory using Apache, Nginx, or PHP built-in server:
      ```sh
      php -S localhost:8000 -t public
      ```

8. **Access the app**
    - Go to [http://localhost:8000](http://localhost:8000) in your browser.

## WebSocket Server Instructions

- The WebSocket server is implemented in `public/server.php` using Ratchet.
- It handles real-time notifications for project/task events (status changes, comments, assignments).
- Clients connect via:
    ```js
    const ws = new WebSocket('ws://localhost:8081');
    ```
- Typical message structure:
    ```js
    ws.send(JSON.stringify({
      type: 'join',      // or 'leave', 'status', 'comment', 'assign'
      userId: <user_id>,
      projectId: <project_id>,
      ...
    }));
    ```
- See `assets/websocket.js` for browser-side code and usage.

## Additional Notes

- Project and task management UI is in `public/projects.php` and `public/tasks.php`.
- AJAX endpoints for CRUD are in `public/ajax_projects.php` and `public/ajax_tasks.php`.
- Real-time updates for tasks/comments are handled by WebSocket messages.
- For development, ensure all dependencies and database are properly configured.
