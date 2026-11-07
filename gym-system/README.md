# 🏋️ GymPro — Gym Management System
## Complete Setup Guide

---

## 📋 Requirements

| Tool    | Version       | Download |
|---------|---------------|----------|
| XAMPP   | 8.x or later  | https://www.apachefriends.org |
| Browser | Chrome/Firefox| — |

---

## 🚀 Step-by-Step Installation

### STEP 1 — Install & Start XAMPP

1. Download XAMPP from https://www.apachefriends.org/es/index.html
2. Install it (default path: `C:\xampp` on Windows, `/opt/lampp` on Linux/Mac)
3. Open **XAMPP Control Panel**
4. Click **Start** next to **Apache**
5. Click **Start** next to **MySQL**
6. Both status lights should turn **green**

---

### STEP 2 — Place the Project Folder

Copy the entire `gym-system` folder into XAMPP's web root:

**Windows:**
```
C:\xampp\htdocs\gym-system\
```

**Linux/Mac:**
```
/opt/lampp/htdocs/gym-system/
```

✅ After this step, your folder structure should look like:
```
htdocs/
  gym-system/
    index.php
    login.php
    register.php
    dashboard_client.php
    dashboard_admin.php
    plans.php
    branches.php
    logout.php
    .htaccess
    database.sql
    config/
    controllers/
    includes/
    views/
    public/
```

---

### STEP 3 — Create the Database

1. Open your browser and go to:
   ```
   http://localhost/phpmyadmin
   ```

2. Click **"New"** in the left sidebar (or go to **SQL** tab)

3. Click the **"SQL"** tab at the top

4. Click **"Choose File"** or paste the contents of `database.sql`
   - OR: go to **Import** tab → **Choose File** → select `gym-system/database.sql`
   - Click **"Go"**

5. ✅ You should see: *"Import has been successfully finished"*

6. In the left sidebar you will now see the **`gym_system`** database with all tables.

---

### STEP 4 — (Optional) Check DB Credentials

Open `config/database.php` and verify:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // Change this if MySQL has a password
define('DB_NAME', 'gym_system');
```

> ⚠️ On fresh XAMPP installs, MySQL root password is **empty** by default.
> If you set a password, update `DB_PASS` accordingly.

---

### STEP 5 — Open the Application

Open your browser and navigate to:
```
http://localhost/gym-system/
```

---

## 🔑 Demo Login Credentials

| Role    | Email              | Password   |
|---------|--------------------|------------|
| Admin   | admin@gym.com      | `password` |
| Client  | john@email.com     | `password` |
| Client  | maria@email.com    | `password` |

---

## 🗂️ Full Folder Structure

```
gym-system/
│
├── index.php                   ← Homepage (public)
├── login.php                   ← Login page
├── register.php                ← Registration page
├── plans.php                   ← Public plans listing
├── branches.php                ← Public branches listing
├── dashboard_client.php        ← Client dashboard (protected)
├── dashboard_admin.php         ← Admin dashboard (protected)
├── logout.php                  ← Session destroy + redirect
├── .htaccess                   ← Apache config / security rules
├── database.sql                ← Full DB schema + sample data
│
├── config/
│   └── database.php            ← PDO connection (change creds here)
│
├── includes/
│   ├── session.php             ← Session helpers, auth guards
│   ├── header.php              ← Shared HTML head + navbar
│   └── footer.php              ← Shared footer + scripts
│
├── controllers/
│   ├── auth.php                ← Login, register, logout logic
│   ├── users.php               ← User CRUD + profile helpers
│   ├── plans.php               ← Plan CRUD + subscribe logic
│   ├── branches.php            ← Branch CRUD
│   ├── notifications.php       ← Notification CRUD + send/read
│   └── reports.php             ← Sales & analytics queries
│
├── views/
│   ├── client/
│   │   ├── profile.php         ← Edit personal data
│   │   ├── digital_id.php      ← Digital membership card
│   │   ├── subscription.php    ← View/change plan
│   │   ├── payments.php        ← Payment history
│   │   ├── notifications.php   ← Read notifications
│   │   └── subscribe.php       ← POST handler for plan change
│   │
│   └── admin/
│       ├── users.php           ← CRUD users
│       ├── plans.php           ← CRUD plans
│       ├── branches.php        ← CRUD branches
│       ├── notifications.php   ← CRUD + send notifications
│       └── reports.php         ← Sales charts + analytics
│
└── public/
    ├── css/
    │   └── style.css           ← Main stylesheet (dark theme)
    ├── js/
    │   └── main.js             ← JS helpers + Chart.js wrappers
    └── assets/
        ├── logo.svg
        ├── hero-bg.svg
        └── branch-placeholder.svg
```

---

## 🗄️ Database Tables

| Table               | Purpose                              |
|---------------------|--------------------------------------|
| `roles`             | admin / client roles                 |
| `users`             | All user accounts                    |
| `plans`             | Membership plans                     |
| `benefits`          | Individual plan benefits             |
| `plan_benefits`     | N:N → plans ↔ benefits               |
| `cards`             | Payment cards                        |
| `branches`          | Gym locations                        |
| `subscriptions`     | Active/past user subscriptions       |
| `payments`          | Payment transaction history          |
| `notifications`     | Admin-created announcements          |
| `notifications_sent`| N:N → users ↔ notifications + read   |

---

## ⚙️ Features Summary

### Public (No Login)
- Home page with hero, features, plan preview, branches
- Full plans listing with comparison table
- Full branches listing
- Registration with optional plan + card
- Login with role detection

### Client Panel
- Dashboard with stats, subscription status, recent payments
- Profile editor (name, phone, birthday, password)
- Digital membership ID card (printable)
- Subscription manager (change plan)
- Full payment history with totals
- Notifications inbox with read/unread

### Admin Panel
- Dashboard with KPIs + revenue & plan charts
- Full user management (view, edit, deactivate)
- Full plan management (create, edit, deactivate)
- Full branch management (create, edit, deactivate)
- Notification system (create, edit, delete — auto-sent to all clients)
- Reports page: monthly revenue bar chart, transactions line chart, plan pie chart, full breakdown table

---

## 🔒 Security Notes

- Passwords hashed with **bcrypt** (PHP `password_hash`)
- All DB queries use **PDO prepared statements** (SQL injection protected)
- Session-based auth with **role guards** on every protected page
- `.htaccess` blocks direct access to `config/`, `controllers/`, `includes/`
- Soft-delete pattern for users/plans/branches (no data loss)

---

## 🛠️ Troubleshooting

| Problem | Solution |
|---------|----------|
| Blank page / 500 error | Enable error display: add `ini_set('display_errors',1);` to top of `index.php` temporarily |
| "Database connection failed" | Check `config/database.php` credentials; ensure MySQL is running in XAMPP |
| "Page not found" | Make sure folder is named exactly `gym-system` in `htdocs/` |
| Login fails with correct password | Re-import `database.sql` — the hash must match exactly |
| Charts not showing | Ensure internet connection (Chart.js loaded from CDN) |

---

## 📞 Tech Stack

- **Frontend:** HTML5, CSS3 (custom dark theme), Bootstrap 5.3, Bootstrap Icons 1.11
- **Fonts:** Bebas Neue + DM Sans (Google Fonts)
- **Charts:** Chart.js 4.4 (CDN)
- **Backend:** PHP 8+ (procedural, PDO)
- **Database:** MySQL / MariaDB (XAMPP)
- **Server:** Apache (XAMPP)
