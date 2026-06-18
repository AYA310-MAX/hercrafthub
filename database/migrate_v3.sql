USE hercrafthub;

ALTER TABLE sales
  ADD COLUMN IF NOT EXISTS item_total DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER unit_price,
  ADD COLUMN IF NOT EXISTS delivery_address VARCHAR(500) NOT NULL DEFAULT '' AFTER total_amount,
  ADD COLUMN IF NOT EXISTS delivery_fee DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER delivery_address,
  ADD COLUMN IF NOT EXISTS charity_donation DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER delivery_fee;

UPDATE sales SET item_total = total_amount WHERE item_total = 0 AND total_amount > 0;
