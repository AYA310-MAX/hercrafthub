-- Run this on an existing hercrafthub database to add new columns and tables.

USE hercrafthub;

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) NULL AFTER bio;

ALTER TABLE products
  ADD COLUMN IF NOT EXISTS quantity INT NOT NULL DEFAULT 1 AFTER is_sold;

CREATE TABLE IF NOT EXISTS sales (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  product_id    INT NOT NULL,
  seller_id     INT NOT NULL,
  buyer_id      INT NOT NULL,
  quantity      INT NOT NULL DEFAULT 1,
  unit_price    DECIMAL(10, 2) NOT NULL,
  total_amount  DECIMAL(10, 2) NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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

CREATE INDEX IF NOT EXISTS idx_products_quantity ON products(quantity);
CREATE INDEX IF NOT EXISTS idx_sales_seller ON sales(seller_id);
CREATE INDEX IF NOT EXISTS idx_sales_buyer ON sales(buyer_id);
CREATE INDEX IF NOT EXISTS idx_sales_product ON sales(product_id);

-- Ensure existing products have quantity
UPDATE products SET quantity = 1 WHERE quantity IS NULL OR quantity < 1;
