-- Sweet Bite Bakery database
-- How to use: open phpMyAdmin (http://localhost/phpmyadmin) -> Import -> choose this file -> Go.
-- Default admin login:  username "admin"  /  password "admin123"  (please change it after the first login)

CREATE DATABASE IF NOT EXISTS sweetbite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sweetbite_db;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS gallery;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS admins;

-- ---------------------------------------------------------
-- Tables
-- ---------------------------------------------------------

CREATE TABLE admins (
    admin_id      INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name     VARCHAR(100) NOT NULL,
    last_login    DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(80) NOT NULL,
    description   VARCHAR(255) NULL,
    display_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    product_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_id  INT NOT NULL,
    name         VARCHAR(120) NOT NULL,
    description  TEXT NOT NULL,
    price_from   DECIMAL(10,2) NOT NULL,
    price_unit   VARCHAR(30) NOT NULL DEFAULT '',
    sizes        VARCHAR(255) NULL,
    flavours     VARCHAR(255) NULL,
    lead_time    VARCHAR(100) NULL,
    image        VARCHAR(255) NULL,
    is_available TINYINT NOT NULL DEFAULT 1,
    is_featured  TINYINT NOT NULL DEFAULT 0,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE gallery (
    gallery_id INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(120) NOT NULL,
    image      VARCHAR(255) NOT NULL,
    occasion   VARCHAR(40) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
    order_id        INT AUTO_INCREMENT PRIMARY KEY,
    customer_name   VARCHAR(100) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    email           VARCHAR(120) NULL,
    product_id      INT NULL,
    item_name       VARCHAR(120) NOT NULL,
    size            VARCHAR(60) NULL,
    flavour         VARCHAR(60) NULL,
    cake_message    VARCHAR(120) NULL,
    design_notes    TEXT NULL,
    reference_image VARCHAR(255) NULL,
    required_date   DATE NOT NULL,
    required_time   VARCHAR(20) NULL,
    delivery_type   VARCHAR(20) NOT NULL DEFAULT 'pickup',
    address         VARCHAR(255) NULL,
    status          VARCHAR(20) NOT NULL DEFAULT 'New',
    admin_note      TEXT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    phone      VARCHAR(20) NULL,
    email      VARCHAR(120) NULL,
    message    TEXT NOT NULL,
    is_read    TINYINT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reviews (
    review_id     INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    rating        TINYINT NOT NULL DEFAULT 5,
    comment       VARCHAR(400) NOT NULL,
    is_visible    TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
    setting_key   VARCHAR(60) PRIMARY KEY,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Starter data (replace with the real bakery details later)
-- ---------------------------------------------------------

INSERT INTO admins (username, password_hash, full_name) VALUES
('admin', '$2y$10$GrLBtAy28DpLH5z0boG54uR6uhmSnA7BRh/8nQGp0frN0RNrcDV2e', 'Bakery Owner');

INSERT INTO categories (category_name, description, display_order) VALUES
('Birthday Cakes', 'Fresh cakes for every birthday, made to your design', 1),
('Wedding Cakes', 'Elegant tiered cakes for your big day', 2),
('Cupcakes', 'Soft, creamy cupcakes for parties and gifts', 3),
('Pastries', 'Flaky pastries and tarts baked every morning', 4),
('Fresh Bread', 'Soft bread and rolls, fresh from the oven', 5);

INSERT INTO products (category_id, name, description, price_from, price_unit, sizes, flavours, lead_time, image, is_available, is_featured) VALUES
(1, 'Chocolate Fudge Cake', 'Rich, moist chocolate sponge layered with chocolate fudge and finished with a glossy drip.', 3200, 'per lb', '1 lb, 2 lb, 3 lb, 5 lb', 'Chocolate, Chocolate Fudge, Mocha', 'Order 2 days before', 'choc-fudge-cake.jpg', 1, 1),
(1, 'Vanilla Birthday Cake', 'Light vanilla sponge with soft butter cream, decorated with colourful sprinkles. Perfect for kids.', 2800, 'per lb', '1 lb, 2 lb, 3 lb, 5 lb', 'Vanilla, Strawberry, Orange', 'Order 2 days before', 'vanilla-birthday-cake.jpg', 1, 1),
(1, 'Ribbon Cake', 'The Sri Lankan favourite. Pink and white sponge layers with smooth butter icing.', 2600, 'per lb', '1 lb, 2 lb, 3 lb', 'Vanilla Ribbon', 'Order 1 day before', 'ribbon-cake.jpg', 1, 1),
(1, 'Red Velvet Cake', 'Velvety red sponge with cream cheese frosting, a great choice for anniversaries.', 3600, 'per lb', '1 lb, 2 lb, 3 lb', 'Red Velvet', 'Order 2 days before', 'red-velvet-cake.jpg', 1, 0),
(1, 'Butter Cake', 'Classic golden butter cake, soft and rich. Great with a cup of tea.', 2200, 'per lb', '1 lb, 2 lb, 3 lb', 'Butter, Lemon', 'Order 1 day before', 'butter-cake.jpg', 1, 0),
(2, 'Two-Tier Wedding Cake', 'Two elegant tiers with fresh flower decoration. Serves about 40 guests.', 18000, 'per cake', '40 to 60 servings', 'Vanilla, Chocolate, Fruit', 'Order 2 weeks before', 'two-tier-wedding-cake.jpg', 1, 1),
(2, 'Three-Tier Wedding Cake', 'Our grand wedding cake with three tiers and gold detailing. Serves 80 to 120 guests.', 32000, 'per cake', '80 to 120 servings', 'Vanilla, Chocolate, Fruit', 'Order 3 weeks before', 'three-tier-wedding-cake.jpg', 1, 0),
(3, 'Vanilla Cupcakes', 'Box of 6 soft vanilla cupcakes with whipped cream swirls.', 1500, 'per box of 6', 'Box of 6, Box of 12', 'Vanilla, Strawberry', 'Order 1 day before', 'vanilla-cupcakes.jpg', 1, 1),
(3, 'Chocolate Cupcakes', 'Box of 6 chocolate cupcakes topped with chocolate cream and a cherry.', 1650, 'per box of 6', 'Box of 6, Box of 12', 'Chocolate, Mocha', 'Order 1 day before', 'chocolate-cupcakes.jpg', 1, 0),
(4, 'Butter Croissant', 'Flaky, golden croissant baked fresh every morning.', 280, 'each', 'Single piece', 'Butter', 'Ready daily', 'butter-croissant.jpg', 1, 0),
(4, 'Fruit Tart', 'Crisp pastry shell filled with custard and topped with fresh fruit.', 350, 'each', 'Single piece', 'Mixed Fruit', 'Ready daily', 'fruit-tart.jpg', 1, 0),
(5, 'Milk Bread Loaf', 'Soft, fluffy sliced bread baked fresh every day.', 220, 'per loaf', '450 g loaf', 'Plain', 'Ready daily', 'milk-bread.jpg', 1, 0),
(5, 'Bread Rolls', 'Pack of 6 soft rolls, perfect for breakfast or party snacks.', 300, 'per pack of 6', 'Pack of 6', 'Plain', 'Ready daily', 'bread-rolls.jpg', 1, 0);

INSERT INTO gallery (title, image, occasion) VALUES
('Blue Kids Birthday Cake', 'kids-birthday.jpg', 'Kids'),
('Pink Princess Cake', 'princess-cake.jpg', 'Kids'),
('Anniversary Red Velvet', 'anniversary-cake.jpg', 'Anniversary'),
('Three-Tier Wedding Cake', 'wedding-tiers.jpg', 'Wedding'),
('Chocolate Drip Cake', 'chocolate-drip.jpg', 'Birthday'),
('Floral Birthday Cake', 'floral-cake.jpg', 'Birthday'),
('Party Cupcake Set', 'party-cupcakes.jpg', 'Cupcakes'),
('Christmas Special Cake', 'eid-christmas-cake.jpg', 'Festival');

INSERT INTO reviews (customer_name, rating, comment, is_visible) VALUES
('Nimali Perera', 5, 'Ordered a birthday cake for my daughter and it was exactly like the picture. Everyone loved it!', 1),
('Kasun Fernando', 5, 'Our wedding cake was beautiful and tasted amazing. Delivered on time to the hotel.', 1),
('Shanika Jayasinghe', 4, 'Very soft ribbon cake and fair prices. Easy to order on WhatsApp.', 1),
('Rajesh Kumar', 5, 'The croissants and bread are always fresh. My family buys here every weekend.', 1);

INSERT INTO settings (setting_key, setting_value) VALUES
('shop_name', 'Sweet Bite Bakery'),
('tagline', 'Freshly baked with love in Colombo'),
('address', 'No. 25, Galle Road, Colombo 03, Sri Lanka'),
('phone', '011 234 5678'),
('whatsapp', '0771234567'),
('email', 'hello@sweetbite.lk'),
('facebook', 'https://www.facebook.com/'),
('hours_weekdays', 'Monday to Saturday: 7:00 AM - 8:00 PM'),
('hours_sunday', 'Sunday: 8:00 AM - 6:00 PM'),
('hours_note', 'Opening hours may change on Poya days and public holidays. Please call before visiting.'),
('banner_on', '1'),
('banner_text', 'Avurudu and Christmas special cakes now open for booking. Order early!'),
('delivery_areas', 'Colombo 1-15, Dehiwala, Mount Lavinia, Rajagiriya, Nugegoda, Battaramulla'),
('map_query', 'Galle Road, Colombo 03, Sri Lanka');
