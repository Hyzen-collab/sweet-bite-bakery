# Sweet Bite Bakery Website

A responsive bakery website for Sweet Bite Bakery, Colombo, Sri Lanka.
Built with HTML, CSS, Bootstrap 5, JavaScript, PHP and MySQL (XAMPP).

All pictures, Bootstrap and scripts are stored inside the `assets` folder.
The website does not load any picture or file from the internet.
(The only outside things are the WhatsApp and Facebook links, and the Google Map on the Contact page.)

## How to run it on XAMPP

1. Install XAMPP and start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Copy the whole `sweetbite` folder into `C:\xampp\htdocs\`.
3. Open http://localhost/phpmyadmin, click **Import**, choose `database/sweetbite_db.sql` and click **Go**.
   This creates the database `sweetbite_db` with sample products.
4. Open the website: http://localhost/sweetbite/
5. Open the admin panel: http://localhost/sweetbite/admin/login.php

**Default admin login:** username `admin`, password `admin123`.
Please change the password in *Admin > Settings* after your first login.

## Settings you may need to change

Open `includes/config.php`.

| Setting | When to change it |
|---|---|
| `DB_USER`, `DB_PASS` | If your MySQL has a password (XAMPP default is `root` with no password) |
| `BASE_URL` | If the folder name is not `sweetbite`, or the site is in the web root (use `''`) |
| `.htaccess` line `ErrorDocument 404` | Change `/sweetbite/404.php` if the folder name is different |

Shop details (address, phone, WhatsApp, opening hours, banner) are changed from **Admin > Settings**, not in the code.
The sample address and phone numbers are placeholders. Please replace them with the real ones.

## Folder structure

```
sweetbite/
├── index.php  about.php  products.php  product.php  gallery.php
├── prices.php  order.php  contact.php  faq.php  thank-you.php  404.php
├── includes/            config, database connection, helper functions, header, footer
├── admin/               login, dashboard, products, categories, gallery, orders, messages, reviews, settings
│   └── includes/        admin login check, admin header and footer
├── assets/
│   ├── css/             style.css (website), admin.css (admin panel)
│   ├── js/              main.js
│   ├── images/          logo, hero, about, products, gallery pictures
│   └── vendor/bootstrap Bootstrap 5 (saved locally)
├── uploads/             pictures uploaded from the admin panel / order form
└── database/            sweetbite_db.sql
```

## What the website has

**Public pages:** Home, About, Products (filter and search), Product details, Cake Designs gallery,
Price list, Custom cake order form (with photo upload), Contact (form, hours, map), FAQ.

**Admin panel:** Dashboard, manage products and categories, manage gallery photos, view and update
orders (status and notes, WhatsApp reply button), contact messages, reviews, shop settings, change password.

**Sri Lanka details:** prices in Rs., Sri Lankan phone number checks (077 123 4567 / +94 format),
WhatsApp click-to-chat, Sri Lanka time zone, delivery areas around Colombo, festival banner.

## Security notes

- Passwords are stored with `password_hash`.
- All database queries use prepared statements.
- All output is escaped to stop XSS, and forms use a CSRF token.
- Uploads accept only JPG, PNG and WEBP up to 2 MB, and PHP files cannot run inside `uploads/`.
- The order and contact forms have a hidden spam trap field.
- Admin logs out after 30 minutes without activity, and wrong passwords are slowed down.

## Before going live

- Change the admin password and replace the sample products, prices, photos and contact details.
- To change a picture later, replace the file in `assets/images/` and keep the same file name, or upload a new one from the admin panel.
- Use HTTPS on the hosting, and make weekly backups of the database.
