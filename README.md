# MonetNails-Ecommerce

A beautiful and modern e-commerce web application for a nail bar, supporting service appointments, product sales, order tracking, and an admin portal.

## File Structure

```text
nail-bar-ecommerce/
│
├── admin/                   # Presentation Layer: Admin Portal
│   ├── includes/            # Admin-specific templates
│   │   ├── admin_header.php
│   │   └── admin_footer.php
│   ├── css/
│   ├── js/
│   ├── dashboard.php        
│   ├── services.php         
│   ├── appointments.php     
│   ├── orders.php           
│   └── users.php            
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

## Features

- **Customer-facing Shop & Booking**: Browse services and products, add items to cart, book appointments, and complete payments.
- **Admin Dashboard**: Manage services, appointments, users, and orders seamlessly.
- **MVC Architecture**: Segregated views, controllers, models, and database configurations.
