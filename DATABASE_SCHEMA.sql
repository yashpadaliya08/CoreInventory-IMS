-- ============================================================
-- CoreInventory IMS – PostgreSQL 15+ Database Schema
-- ============================================================

-- 1. users
CREATE TABLE users (
    id            BIGSERIAL    PRIMARY KEY,
    name          VARCHAR(255) NOT NULL,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,
    role          VARCHAR(20)  NOT NULL CHECK (role IN ('manager', 'staff')),
    otp_code      VARCHAR(10),
    otp_expires_at TIMESTAMPTZ,
    created_at    TIMESTAMPTZ  DEFAULT NOW(),
    updated_at    TIMESTAMPTZ  DEFAULT NOW()
);

-- 2. warehouses
CREATE TABLE warehouses (
    id               BIGSERIAL    PRIMARY KEY,
    name             VARCHAR(255) NOT NULL,
    code             VARCHAR(50)  NOT NULL UNIQUE,
    location_address TEXT
);

-- 3. locations
CREATE TABLE locations (
    id           BIGSERIAL    PRIMARY KEY,
    warehouse_id BIGINT       NOT NULL REFERENCES warehouses(id) ON DELETE CASCADE,
    name         VARCHAR(255) NOT NULL,
    type         VARCHAR(30)  NOT NULL CHECK (type IN ('internal', 'vendor', 'customer', 'inventory_loss'))
);

CREATE INDEX idx_locations_warehouse_id ON locations(warehouse_id);

-- 4. products
CREATE TABLE products (
    id              BIGSERIAL    PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    sku             VARCHAR(100) NOT NULL,
    category        VARCHAR(255),
    unit_of_measure VARCHAR(50),
    reorder_level   INTEGER      NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX idx_products_sku ON products(sku);

-- 5. receipts
CREATE TABLE receipts (
    id            BIGSERIAL    PRIMARY KEY,
    reference_no  VARCHAR(100) NOT NULL UNIQUE,
    vendor_name   VARCHAR(255),
    status        VARCHAR(20)  NOT NULL DEFAULT 'Draft' CHECK (status IN ('Draft', 'Waiting', 'Ready', 'Done', 'Canceled')),
    expected_date DATE
);

-- 6. receipt_items
CREATE TABLE receipt_items (
    id         BIGSERIAL PRIMARY KEY,
    receipt_id BIGINT    NOT NULL REFERENCES receipts(id) ON DELETE CASCADE,
    product_id BIGINT    NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity   INTEGER   NOT NULL DEFAULT 0
);

CREATE INDEX idx_receipt_items_receipt_id  ON receipt_items(receipt_id);
CREATE INDEX idx_receipt_items_product_id  ON receipt_items(product_id);

-- 7. deliveries
CREATE TABLE deliveries (
    id             BIGSERIAL    PRIMARY KEY,
    reference_no   VARCHAR(100) NOT NULL UNIQUE,
    customer_name  VARCHAR(255),
    status         VARCHAR(20)  NOT NULL DEFAULT 'Draft' CHECK (status IN ('Draft', 'Waiting', 'Ready', 'Done', 'Canceled')),
    scheduled_date DATE
);

-- 8. delivery_items
CREATE TABLE delivery_items (
    id          BIGSERIAL PRIMARY KEY,
    delivery_id BIGINT    NOT NULL REFERENCES deliveries(id) ON DELETE CASCADE,
    product_id  BIGINT    NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity    INTEGER   NOT NULL DEFAULT 0
);

CREATE INDEX idx_delivery_items_delivery_id ON delivery_items(delivery_id);
CREATE INDEX idx_delivery_items_product_id  ON delivery_items(product_id);

-- 9. transfers
CREATE TABLE transfers (
    id               BIGSERIAL    PRIMARY KEY,
    reference_no     VARCHAR(100) NOT NULL UNIQUE,
    from_location_id BIGINT       NOT NULL REFERENCES locations(id) ON DELETE CASCADE,
    to_location_id   BIGINT       NOT NULL REFERENCES locations(id) ON DELETE CASCADE,
    status           VARCHAR(20)  NOT NULL DEFAULT 'Draft' CHECK (status IN ('Draft', 'Ready', 'Done'))
);

CREATE INDEX idx_transfers_from_location_id ON transfers(from_location_id);
CREATE INDEX idx_transfers_to_location_id   ON transfers(to_location_id);

-- 10. transfer_items
CREATE TABLE transfer_items (
    id          BIGSERIAL PRIMARY KEY,
    transfer_id BIGINT    NOT NULL REFERENCES transfers(id) ON DELETE CASCADE,
    product_id  BIGINT    NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity    INTEGER   NOT NULL DEFAULT 0
);

CREATE INDEX idx_transfer_items_transfer_id ON transfer_items(transfer_id);
CREATE INDEX idx_transfer_items_product_id  ON transfer_items(product_id);

-- 11. adjustments
CREATE TABLE adjustments (
    id                  BIGSERIAL    PRIMARY KEY,
    reference_no        VARCHAR(100) NOT NULL UNIQUE,
    location_id         BIGINT       NOT NULL REFERENCES locations(id) ON DELETE CASCADE,
    product_id          BIGINT       NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    recorded_quantity   INTEGER      NOT NULL DEFAULT 0,
    physical_quantity   INTEGER      NOT NULL DEFAULT 0,
    difference_quantity INTEGER      NOT NULL DEFAULT 0,
    status              VARCHAR(20)  NOT NULL DEFAULT 'Draft' CHECK (status IN ('Draft', 'Done'))
);

CREATE INDEX idx_adjustments_location_id ON adjustments(location_id);
CREATE INDEX idx_adjustments_product_id  ON adjustments(product_id);

-- 12. stock_ledger
CREATE TABLE stock_ledger (
    id              BIGSERIAL    PRIMARY KEY,
    product_id      BIGINT       NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    location_id     BIGINT       NOT NULL REFERENCES locations(id) ON DELETE CASCADE,
    reference_type  VARCHAR(255) NOT NULL,
    reference_id    BIGINT       NOT NULL,
    quantity_change INTEGER      NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ  DEFAULT NOW(),
    updated_at      TIMESTAMPTZ  DEFAULT NOW()
);

CREATE INDEX idx_stock_ledger_product_id  ON stock_ledger(product_id);
CREATE INDEX idx_stock_ledger_location_id ON stock_ledger(location_id);
CREATE INDEX idx_stock_ledger_reference   ON stock_ledger(reference_type, reference_id);
