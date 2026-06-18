-- HerCraft Hub production schema (InfinityFree / shared hosting)
-- Database must already exist in your hosting control panel.

CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(150) NOT NULL,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  role          ENUM('buyer', 'seller', 'admin') NOT NULL DEFAULT 'buyer',
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  is_verified   TINYINT(1) NOT NULL DEFAULT 0,
  location      VARCHAR(150) NULL,
  bio           TEXT NULL,
  profile_image VARCHAR(255) NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL UNIQUE,
  description   VARCHAR(255) NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  seller_id       INT NOT NULL,
  category_id     INT NOT NULL,
  title           VARCHAR(100) NOT NULL,
  description     TEXT NOT NULL,
  price           DECIMAL(10, 2) NOT NULL,
  image           VARCHAR(255) NULL,
  condition_type  ENUM('New', 'Like New', 'Good', 'Fair') NOT NULL DEFAULT 'New',
  location        VARCHAR(150) NULL,
  is_active       TINYINT(1) NOT NULL DEFAULT 1,
  is_sold         TINYINT(1) NOT NULL DEFAULT 0,
  quantity        INT NOT NULL DEFAULT 1,
  created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_seller
    FOREIGN KEY (seller_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_products_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS wishlists (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NOT NULL,
  product_id    INT NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_wishlist_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_wishlist_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT uq_wishlist UNIQUE (user_id, product_id)
);

CREATE TABLE IF NOT EXISTS sales (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  product_id       INT NOT NULL,
  seller_id        INT NOT NULL,
  buyer_id         INT NOT NULL,
  quantity         INT NOT NULL DEFAULT 1,
  unit_price       DECIMAL(10, 2) NOT NULL,
  item_total       DECIMAL(10, 2) NOT NULL,
  delivery_address VARCHAR(500) NOT NULL,
  delivery_fee     DECIMAL(10, 2) NOT NULL DEFAULT 0,
  charity_donation DECIMAL(10, 2) NOT NULL DEFAULT 0,
  total_amount     DECIMAL(10, 2) NOT NULL,
  created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sales_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_sales_seller
    FOREIGN KEY (seller_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_sales_buyer
    FOREIGN KEY (buyer_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  sender_id     INT NOT NULL,
  receiver_id   INT NOT NULL,
  product_id    INT NULL,
  body          TEXT NOT NULL,
  is_read       TINYINT(1) NOT NULL DEFAULT 0,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_messages_sender
    FOREIGN KEY (sender_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_messages_receiver
    FOREIGN KEY (receiver_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_messages_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

INSERT IGNORE INTO categories (name, description) VALUES
  ('Tech Crafts', 'Technology-inspired handmade goods'),
  ('Handmade', 'Traditional handcrafted items'),
  ('Digital Art', 'Digital downloads and templates'),
  ('Accessories', 'Jewellery, bags, and wearable crafts'),
  ('Bundles', 'Curated product bundles'),
  ('Beauty Tech', 'Beauty devices and tech accessories');

INSERT IGNORE INTO users (full_name, email, password, role, is_active, is_verified) VALUES
  ('System Administrator', 'admin@hercrafthub.co.za',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'admin', 1, 1);
