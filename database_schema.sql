-- AllMine Coffee POS Database Schema

-- Shop Settings Table
CREATE TABLE shop_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(255) UNIQUE NOT NULL,
    value TEXT
);

-- Insert default shop name
INSERT INTO shop_settings (key_name, value) VALUES ('shop_name', 'AllMine Coffee POS');

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) DEFAULT 0.00,
    category_id INT NOT NULL,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Sizes Table
CREATE TABLE sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    sort_order INT DEFAULT 0
);

-- Insert default sizes
INSERT INTO sizes (name, sort_order) VALUES 
('S', 1),
('M', 2),
('L', 3);

-- Product Prices Table (for size-based pricing)
CREATE TABLE product_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_size (product_id, size_id)
);

-- Toppings Table
CREATE TABLE toppings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);

-- Members Table
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    points INT DEFAULT 0
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    queue_number VARCHAR(20) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'promptpay') NOT NULL,
    member_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id)
);

-- Order Items Table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    size VARCHAR(50),
    sweetness VARCHAR(20) DEFAULT '100%',
    toppings_json TEXT,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name) VALUES 
('Coffee'),
('Tea'),
('Smoothies'),
('Pastries'),
('Sandwiches');

-- Insert sample products
INSERT INTO products (name, category_id, image, is_active) VALUES
('Espresso', 1, 'espresso.jpg', TRUE),
('Cappuccino', 1, 'cappuccino.jpg', TRUE),
('Latte', 1, 'latte.jpg', TRUE),
('Mocha', 1, 'mocha.jpg', TRUE),
('Green Tea', 2, 'green-tea.jpg', TRUE),
('Thai Tea', 2, 'thai-tea.jpg', TRUE),
('Strawberry Smoothie', 3, 'strawberry-smoothie.jpg', TRUE),
('Banana Smoothie', 3, 'banana-smoothie.jpg', TRUE),
('Croissant', 4, 'croissant.jpg', TRUE),
('Blueberry Muffin', 4, 'muffin.jpg', TRUE),
('Turkey Sandwich', 5, 'sandwich.jpg', TRUE),
('Ham & Cheese', 5, 'ham-cheese.jpg', TRUE);

-- Insert sample product prices
INSERT INTO product_prices (product_id, size_id, price) VALUES
(1, 1, 60.00), -- Espresso S
(1, 2, 70.00), -- Espresso M
(1, 3, 80.00), -- Espresso L
(2, 1, 75.00), -- Cappuccino S
(2, 2, 85.00), -- Cappuccino M
(2, 3, 95.00), -- Cappuccino L
(3, 1, 75.00), -- Latte S
(3, 2, 85.00), -- Latte M
(3, 3, 95.00), -- Latte L
(4, 1, 85.00), -- Mocha S
(4, 2, 95.00), -- Mocha M
(4, 3, 105.00), -- Mocha L
(5, 1, 60.00), -- Green Tea S
(5, 2, 70.00), -- Green Tea M
(5, 3, 80.00), -- Green Tea L
(6, 1, 65.00), -- Thai Tea S
(6, 2, 75.00), -- Thai Tea M
(6, 3, 85.00), -- Thai Tea L
(7, 1, 90.00), -- Strawberry Smoothie S
(7, 2, 100.00), -- Strawberry Smoothie M
(7, 3, 110.00), -- Strawberry Smoothie L
(8, 1, 90.00), -- Banana Smoothie S
(8, 2, 100.00), -- Banana Smoothie M
(8, 3, 110.00), -- Banana Smoothie L
(9, 1, 45.00), -- Croissant S (default)
(10, 1, 50.00), -- Blueberry Muffin S (default)
(11, 1, 120.00), -- Turkey Sandwich S (default)
(12, 1, 110.00); -- Ham & Cheese S (default)

-- Insert sample toppings
INSERT INTO toppings (name, price) VALUES
('Whipped Cream', 10.00),
('Extra Shot', 15.00),
('Caramel Syrup', 10.00),
('Vanilla Syrup', 10.00),
('Hazelnut Syrup', 10.00),
('Oat Milk', 10.00),
('Almond Milk', 10.00);