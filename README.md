# MonetNails-Ecommerce

A beautiful and modern **e-commerce web application for a nail bar**, built with **PHP (MVC architecture)**, **MySQL**, **HTML**, **CSS**, and **JavaScript**. It supports service appointments, product sales, order tracking, and a full admin portal — everything a small nail bar business needs to run online.

<p align="center">
<img src="https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white" alt="PHP" />
<img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white" alt="MySQL" />
<img src="https://img.shields.io/badge/Apache-2.4-D22128?logo=apache&logoColor=white" alt="Apache" />
<img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License: MIT" />
</p>

---

## Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Getting Started / Setup Guide](#getting-started--setup-guide)
- [Prerequisites](#prerequisites)
- [Installation (XAMPP)](#installation-xampp)
- [Configuration](#configuration)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

---

## About the Project

This project focuses on designing and developing a comprehensive website for **"Monet Nails,"** a small business nail bar. It is a full-featured e-commerce platform that allows customers to:

- Browse nail services and retail products
- Book salon appointments online
- Add products to a shopping cart and check out
- Track their orders

It also includes a secure **Admin Portal** where the business owner can manage services, appointments, orders, and users. The application follows a clean **three-layer MVC-style architecture** (Presentation / Application / Data) for maintainability and scalability.

---

## Features

### Customer-Facing

- **Homepage** – branded landing page for Monet Nails
- **Shop / Product Catalog** – browse products with "BUY NOW" buttons
- **Product Detail Page** – single product view
- **Shopping Cart** – add, update, and remove items
- **Checkout & Payment** – checkout flow with payment form
- **Appointment Booking** – interactive booking calendar for nail services
- **User Accounts** – register, log in, and manage profile

### Admin Portal

- **Dashboard** – overview of business activity
- **Services Management** – create, update, and remove nail services
- **Appointments Management** – view, confirm, or cancel bookings
- **Orders Management** – track and update order status
- **Users Management** – manage customer, staff, and admin accounts

### Technical Highlights

- **MVC-style layered architecture** (Views / Controllers / Models)
- **PDO with prepared statements** for secure database access
- **Password hashing** for user credentials
- **Role-based access** (`customer`, `staff`, `admin`)
- **Timezone-synchronized sessions** for accurate scheduling

---

## Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8+ |
| Database | MySQL / MariaDB (via PDO) |
| Web Server | Apache (e.g., XAMPP) |
| Frontend | HTML5, CSS3, JavaScript |
| Architecture | MVC-style (Presentation / Application / Data layers) |

---

## Project Structure

```text
nail-bar-ecommerce/
│
├── admin/                   # Presentation Layer: Admin Portal
│   ├── includes/            # Admin-specific templates
│   │   ├── admin_header.php
│   │   └── admin_footer.php
│   ├── css/
│   ├── js/
│   ├── dashboard.php        # Admin dashboard
│   ├── services.php         # Services management
│   ├── appointments.php     # Appointments management
│   ├── orders.php           # Orders management
│   └── users.php            # Users management
│
├── views/                   # Presentation Layer: Customer-facing pages (renamed from public/)
│   ├── index.php            # Homepage
│   ├── shop.php             # Product catalog & "BUY NOW" buttons
│   ├── product_detail.php   # Single product view
│   ├── cart.php             # Shopping cart interface
│   ├── checkout.php         # Checkout & payment form
│   ├── booking.php          # Appointment calendar UI
│   ├── login.php
│   └── register.php
│
├── assets/                  # Presentation Layer: Static files
│   ├── css/
│   ├── js/
│   └── images/
│
├── uploads/                 # Presentation Layer: Dynamic image uploads (ensure write permissions)
│
├── includes/                # Presentation Layer: Shared UI templates
│   ├── header.php
│   └── footer.php
│
├── controllers/             # Application Layer: Business Logic
│   ├── AuthController.php
│   ├── CartController.php
│   ├── BookingController.php
│   ├── AdminController.php
│   └── PaymentController.php
│
├── models/                  # Data Layer: Database Abstraction
│   ├── User.php
│   ├── Service.php
│   └── Order.php
│
├── config/                  # Data Layer: Database connections & global settings
│   └── db.php               # PDO database connection
│
├── sql/                     # Data Layer: Database schema
│   └── schema.sql
│
├── index.php                # Entry Point / Front Controller (routes traffic to controllers/views)
├── .gitignore
└── README.md
```

---

## Database Schema

The database `monet_nails_db` (utf8mb4) contains four main tables:

| Table | Description |
| --- | --- |
| `users` | Customer, staff, and admin accounts (name, email, hashed password, role) |
| `services` | Nail bar services (name, description, price, duration in minutes) |
| `appointments` | Booked appointments linked to users and services, with status tracking (`pending`, `confirmed`, `completed`, `cancelled`) |
| `orders` | Product orders with total amount and status tracking (`pending`, `processing`, `shipped`, `delivered`, `cancelled`) |

The full schema is located in [`sql/schema.sql`](sql/schema.sql) and can be imported directly into MySQL.

---

## Getting Started / Setup Guide

Follow these steps to run the project locally.

### Prerequisites

Make sure you have the following installed:

- **PHP 8.0+** (with PDO MySQL extension enabled)
- **MySQL 8.0+** or **MariaDB**
- **Apache** web server — the easiest way is a bundle like **[XAMPP](https://www.apachefriends.org/)**, **WAMP**, or **MAMP**
- **Git** (to clone the repository)

> **Recommended:** XAMPP bundles Apache, MySQL, PHP, and phpMyAdmin in one installer for Windows, Linux, and macOS.

### Installation (XAMPP)

1. **Clone the repository** into your web server's document root:

```bash
   cd C:\xampp\htdocs        # on Windows
   # or /Applications/XAMPP/htdocs  on macOS
   # or /opt/lampp/htdocs           on Linux

   git clone https://github.com/Thisagi13/MonetNails-Ecommerce.git
   cd MonetNails-Ecommerce
```

2. **Create the database**

- Open **phpMyAdmin** at `http://localhost/phpmyadmin`
- Import the SQL file: **`sql/schema.sql`**
- This creates the database `monet_nails_db` with all required tables

Or via command line:

```bash
   mysql -u root -p < sql/schema.sql
```

3. **Configure the database connection**

Edit [`config/db.php`](config/db.php) and update the credentials to match your local MySQL setup:

```php
   $host = 'localhost';
   $db   = 'monet_nails_db';
   $user = 'root';      // your MySQL username
   $pass = '';          // your MySQL password
```

4. **Set write permissions** on the uploads folder so product/service images can be uploaded:

```bash
   # Linux / macOS
   chmod -R 775 uploads/
```

On Windows, ensure the `uploads/` folder is not read-only.

5. **Start the server** – launch Apache and MySQL from the XAMPP Control Panel.
6. **Open the application** in your browser:

```javascript
   http://localhost/MonetNails-Ecommerce/
```

### Configuration

| File | Purpose |
| --- | --- |
| `config/db.php` | Database credentials, PDO options, timezone sync |
| `sql/schema.sql` | Database schema (tables, relationships, enums) |
| `uploads/` | Folder for dynamically uploaded images (needs write access) |

---

## Usage

1. **Register** a new account via `views/register.php`, or log in via `views/login.php`.
2. As a **customer**:

- Browse the shop and add products to your cart
- Proceed through checkout and payment
- Book an appointment for a nail service via the booking page

3. As an **admin** (account with `admin` role):

- Access the admin portal under `admin/dashboard.php`
- Manage services, appointments, orders, and users

---

## Contributing

Contributions are welcome! If you'd like to improve this project:

1. **Fork** the repository
2. **Create** a feature branch: `git checkout -b feature/amazing-feature`
3. **Commit** your changes: `git commit -m "Add amazing feature"`
4. **Push** to the branch: `git push origin feature/amazing-feature`
5. **Open** a Pull Request

Please follow the existing folder conventions and keep controllers, models, and views cleanly separated.

---

## License

This project is licensed under the **MIT License** — see below for details.

```javascript
MIT License

Copyright (c) 2026 Thisagi13

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

> **Note:** Add a `LICENSE` file containing the text above to the root of the repository so GitHub detects and displays the license automatically.

---

## Acknowledgments

- Built as a small-business e-commerce solution for **Monet Nails** nail bar
- Designed with a clean, layered MVC-style architecture in PHP
