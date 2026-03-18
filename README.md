# Freelancer Management System (Core PHP + MySQL)

## Tech Stack
- Frontend: HTML, Tailwind CSS, Vanilla JS
- Backend: Core PHP (no framework)
- Database: MySQL

## Features
- Authentication with session-based login/register
- Roles: admin, client
- Admin dashboard metrics
- Client dashboard project and payment history
- Client CRUD (add, edit, delete)
- Project management (assign client, status updates)
- Manual payment tracking (paid/unpaid)
- Internal messaging (freelancer <-> client)
- Settings (profile update, password change)

## Folder Structure
- config.php
- database.sql
- index.html
- about.html
- services.html
- contact.html
- login.php
- register.php
- logout.php
- dashboard.php
- clients.php
- add-client.php
- edit-client.php
- projects.php
- add-project.php
- payments.php
- messages.php
- settings.php
- includes/
  - auth.php
  - flash.php
- partials/
  - header.php
  - sidebar.php
  - topbar.php
  - footer.php
- assets/
  - css/custom.css
  - js/app.js

## Setup Instructions
1. Create MySQL database and tables:
   - Import `database.sql` in phpMyAdmin or MySQL CLI.
2. Configure database credentials in `config.php`:
   - DB_HOST, DB_NAME, DB_USER, DB_PASS
3. Put project in web server root (XAMPP/WAMP/Laragon):
   - Example: `htdocs/freelancer-management-system`
4. Start Apache and MySQL.
5. Open in browser:
   - `http://localhost/freelancer-management-system/login.php`

## Demo Accounts (from database.sql)
- Admin:
  - email: admin@example.com
  - password: password123
- Client:
  - email: client@example.com
  - password: password123

## Notes
- This project intentionally avoids frameworks.
- Tailwind is loaded through CDN for simplicity.
