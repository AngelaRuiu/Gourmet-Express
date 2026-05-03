
 /* 
    Gourmet Express — Seed Data
    Run AFTER schema.sql: docker exec -i restaurant_db mysql -u xxx -pxxx restaurant_db < database/seed.sql
*/


USE `restaurant_db`;

-- Disable FK checks so we can insert in any order safely
SET FOREIGN_KEY_CHECKS = 0;


-- ACHTUNG: Lookup tables must be inserted before main entities to avoid FK constraint errors!

INSERT INTO `order_status` (`status_name`) VALUES
('pending'),
('confirmed'),
('preparing'),
('ready'),
('out_for_delivery'),
('delivered'),
('cancelled');

INSERT INTO `payment_status` (`status_name`) VALUES
('pending'),
('paid'),
('failed'),
('refunded');


-- RESTAURANT INFO

INSERT INTO `restaurant_info`
    (`name`, `phone`, `email`, `street`, `city`, `postal_code`, `opening_time`, `closing_time`, `max_delivery_km`)
VALUES
    ('Gourmet Express',
     '+49 721 987654',
     'hello@gourmet-express.local',
     'Kaiserstraße 12',
     'Karlsruhe',
     '76133',
     '11:00:00',
     '22:30:00',
     15);

/* USERS 
*/

INSERT INTO `users`
    (`username`, `first_name`, `last_name`, `email`, `password`, `phone`, `role`, `hire_date`)
VALUES
    ('admin',
     'Alexandra',
     'Weber',
     'admin@gourmet-express.local',
     '$2y$12$vkqRJZzwaVNYZYKph5lL1.Y.UM/tdA3Y8Bj/7JmVtEfo2tMkPriRG',   -- Secret1234!
     '+49 721 100001',
     'admin',
     '2022-01-10'),

    ('manager_sophie',
     'Sophie',
     'Müller',
     'sophie.mueller@gourmet-express.local',
     '$2y$12$vkqRJZzwaVNYZYKph5lL1.Y.UM/tdA3Y8Bj/7JmVtEfo2tMkPriRG',   -- Secret1234!
     '+49 721 100002',
     'manager',
     '2022-03-15'),
-- CATEGORIES

INSERT INTO `categories` (`name`, `slug`, `description`, `image_path`, `sort_order`, `is_active`) VALUES
('Appetizers',  'appetizers',  'Light bites to start your experience',          '/img/categories/',  1, 1),
('Main Course', 'main-course', 'Hearty and satisfying mains',                   '/img/categories/',       2, 1),
('Pizza',       'pizza',       'Stone-baked authentic pizzas',                  '/img/categories/',       3, 1),
('Pasta',       'pasta',       'Fresh handmade pasta, made daily',              '/img/categories/',       4, 1),
('Burgers',     'burgers',     'Premium gourmet burgers',                       '/img/categories/',     5, 1),
('Salads',      'salads',      'Crisp, fresh salads',                           '/img/categories/',      6, 1),
('Desserts',    'desserts',    'Indulgent sweets to finish',                    '/img/categories/',    7, 1),
('Beverages',   'beverages',   'Soft drinks, juices, and hot drinks',           '/img/categories/',   8, 1);


-- MENU ITEMS

INSERT INTO `menu_items`
    (`category_id`, `name`, `slug`, `description`, `ingredients`,
     `price`, `is_special`, `special_price`, `special_start`, `special_end`,
     `image_path`, `prep_time`, `is_available`, `is_featured`)
VALUES
-- Appetizers (category 1)
(1, 'Garlic Bread',
    'garlic-bread',
    'Golden toasted bread with roasted garlic butter and fresh parsley.',
    'Sourdough, garlic, butter, parsley, olive oil',
    4.99, 0, NULL, NULL, NULL,
    '/img/menu/', 8, 1, 0),

(1, 'Bruschetta al Pomodoro',
    'bruschetta-al-pomodoro',
    'Grilled ciabatta topped with fresh tomatoes, basil, and extra virgin olive oil.',
    'Ciabatta, tomatoes, fresh basil, garlic, olive oil, balsamic glaze',
    7.50, 0, NULL, NULL, NULL,
    '/img/menu/', 10, 1, 0),

(1, 'Crispy Calamari',
    'crispy-calamari',
    'Lightly breaded calamari rings served with house aioli and lemon.',
    'Squid, seasoned flour, egg, panko, aioli, lemon',
    9.90, 0, NULL, NULL, NULL,
    '/img/menu/', 12, 1, 1),

(1, 'Spring Rolls',
    'spring-rolls',
    'Crispy vegetable spring rolls served with sweet chili dipping sauce.',
    'Rice paper, mixed vegetables, vermicelli, sweet chili',
    6.99, 0, NULL, NULL, NULL,
    '/img/menu/', 10, 1, 0),

-- Main Course (category 2)
(2, 'Grilled Chicken Breast',
    'grilled-chicken-breast',
    'Free-range chicken breast marinated in herbs, served with seasonal vegetables.',
    'Chicken breast, rosemary, thyme, garlic, lemon, seasonal veg',
    16.90, 0, NULL, NULL, NULL,
    '/img/menu/', 22, 1, 1),

(2, 'Beef Tenderloin',
    'beef-tenderloin',
    '200g beef tenderloin, medium-rare, served with truffle mash and red wine jus.',
    'Beef tenderloin, truffle, cream, potato, red wine, shallots',
    28.50, 1, 24.90,
    '2025-06-01 00:00:00', '2025-06-30 23:59:59',
    '/img/menu/', 28, 1, 1),

(2, 'Pan-Seared Salmon',
    'pan-seared-salmon',
    'Atlantic salmon fillet, asparagus, capers, lemon butter sauce.',
    'Salmon, asparagus, capers, butter, lemon, dill',
    19.90, 0, NULL, NULL, NULL,
    '/img/menu/', 20, 1, 0),

-- Pizza (category 3)
(3, 'Margherita',
    'margherita',
    'Classic tomato base, fior di latte mozzarella, fresh basil.',
    'Pizza dough, San Marzano tomato, fior di latte, fresh basil, olive oil',
    13.90, 0, NULL, NULL, NULL,
    '/img/menu/', 15, 1, 0),

(3, 'Pepperoni Diavola',
    'pepperoni-diavola',
    'Spicy pepperoni, chilli flakes, mozzarella, tomato base.',
    'Pizza dough, tomato, mozzarella, spicy pepperoni, chilli, oregano',
    15.90, 0, NULL, NULL, NULL,
    '/img/menu/', 15, 1, 1),

(3, 'Quattro Formaggi',
    'quattro-formaggi',
    'Four-cheese white pizza: mozzarella, gorgonzola, parmesan, fontina.',
    'Pizza dough, mozzarella, gorgonzola, parmesan, fontina, walnuts',
    16.90, 0, NULL, NULL, NULL,
    '/img/menu/', 15, 1, 0),

(3, 'Vegetariana',
    'vegetariana',
    'Grilled courgette, peppers, mushrooms, olives, cherry tomatoes.',
    'Pizza dough, tomato, mozzarella, courgette, peppers, mushrooms, olives',
    14.90, 0, NULL, NULL, NULL,
    '/img/menu/', 15, 1, 0),

-- Pasta (category 4)
(4, 'Spaghetti Carbonara',
    'spaghetti-carbonara',
    'Authentic Roman carbonara: guanciale, egg yolk, pecorino, black pepper.',
    'Spaghetti, guanciale, egg yolk, pecorino romano, black pepper',
    13.50, 0, NULL, NULL, NULL,
    '/img/menu/', 15, 1, 1),

(4, 'Tagliatelle Bolognese',
    'tagliatelle-bolognese',
    'Slow-cooked beef and pork ragù on fresh egg tagliatelle.',
    'Fresh tagliatelle, beef, pork, tomato, carrot, onion, celery, wine',
    14.50, 0, NULL, NULL, NULL,
    '/img/menu/', 18, 1, 0),

(4, 'Penne Arrabbiata',
    'penne-arrabbiata',
    'Fiery tomato sauce with garlic and chilli, finished with fresh parsley.',
    'Penne, San Marzano tomato, garlic, chilli, parsley, olive oil',
    11.90, 0, NULL, NULL, NULL,
    '/img/menu/', 12, 1, 0),

-- Burgers (category 5)
(5, 'Classic Gourmet Burger',
    'classic-gourmet-burger',
    '180g dry-aged beef patty, aged cheddar, lettuce, tomato, pickles, house sauce.',
    'Beef (dry-aged), brioche bun, aged cheddar, iceberg, tomato, pickles, house sauce',
    14.90, 0, NULL, NULL, NULL,
    '/img/menu/', 14, 1, 1),

(5, 'Truffle Mushroom Burger',
    'truffle-mushroom-burger',
    '180g beef patty, truffle mayo, sautéed mushrooms, gruyère, rocket.',
    'Beef patty, brioche bun, truffle mayo, portobello, gruyère, rocket',
    16.90, 0, NULL, NULL, NULL,
    '/img/menu/', 16, 1, 0),

-- Salads (category 6)
(6, 'Caesar Salad',
    'caesar-salad',
    'Romaine hearts, house Caesar dressing, parmesan shavings, sourdough croutons.',
    'Romaine, Caesar dressing, parmesan, croutons, anchovy',
    10.50, 0, NULL, NULL, NULL,
    '/img/menu/', 8, 1, 0),

(6, 'Greek Salad',
    'greek-salad',
    'Tomatoes, cucumber, kalamata olives, red onion, feta, oregano.',
    'Tomato, cucumber, olives, red onion, feta, oregano, olive oil',
    9.90, 0, NULL, NULL, NULL,
    '/img/menu/', 8, 1, 0),

-- Desserts (category 7)
(7, 'Tiramisu',
    'tiramisu',
    'Classic Italian tiramisu with mascarpone, espresso-soaked ladyfingers.',
    'Mascarpone, ladyfingers, espresso, egg, sugar, cocoa powder',
    7.50, 0, NULL, NULL, NULL,
    '/img/menu/', 5, 1, 1),

(7, 'Chocolate Fondant',
    'chocolate-fondant',
    'Warm dark chocolate fondant with a molten centre, vanilla ice cream.',
    'Dark chocolate, butter, eggs, flour, sugar, vanilla ice cream',
    8.50, 0, NULL, NULL, NULL,
    '/img/menu/', 12, 1, 0),

(7, 'New York Cheesecake',
    'new-york-cheesecake',
    'Creamy baked cheesecake with a graham cracker crust and berry coulis.',
    'Cream cheese, graham crackers, butter, egg, sugar, berries',
    7.90, 0, NULL, NULL, NULL,
    '/img/menu/', 5, 1, 0),

-- Beverages (category 8)
(8, 'Still Water (500ml)',
    'still-water',
    'Premium still mineral water.',
    'Still water',
    2.50, 0, NULL, NULL, NULL,
    '/img/menu/', 1, 1, 0),

(8, 'Sparkling Water (500ml)',
    'sparkling-water',
    'Premium sparkling mineral water.',
    'Sparkling water',
    2.50, 0, NULL, NULL, NULL,
    '/img/menu/', 1, 1, 0),

(8, 'Fresh Orange Juice',
    'fresh-orange-juice',
    'Freshly squeezed orange juice, served chilled.',
    'Fresh oranges',
    4.90, 0, NULL, NULL, NULL,
    '/img/menu/', 3, 1, 0),

(8, 'Espresso',
    'espresso',
    'Single-origin espresso shot.',
    'Freshly ground coffee',
    2.90, 0, NULL, NULL, NULL,
    '/img/menu/', 3, 1, 0),

(8, 'Cappuccino',
    'cappuccino',
    'Double espresso with steamed milk foam.',
    'Espresso, whole milk',
    3.90, 0, NULL, NULL, NULL,
    '/img/menu/', 4, 1, 0),

(8, 'Coca-Cola (330ml)',
    'coca-cola',
    'Ice-cold Coca-Cola served in a chilled glass.',
    'Carbonated water, sugar, caramel colour, natural flavours',
    3.20, 0, NULL, NULL, NULL,
    '/img/menu/', 1, 1, 0),

(8, 'Craft Beer (0.5L)',
    'craft-beer',
    'Rotating selection of local Karlsruhe craft ales.',
    'Water, barley malt, hops, yeast',
    5.50, 0, NULL, NULL, NULL,
    '/img/menu/', 2, 1, 0);


-- RESTAURANT TABLES

INSERT INTO `restaurant_tables` (`table_number`, `capacity`, `location`, `is_available`) VALUES
('1',  2, 'Window',        1),
('2',  2, 'Window',        1),
('3',  4, 'Main Hall',     1),
('4',  4, 'Main Hall',     1),
('5',  4, 'Main Hall',     1),
('6',  4, 'Main Hall',     1),
('7',  6, 'Back Section',  1),
('8',  6, 'Back Section',  1),
('9',  8, 'Private Room',  1),
('10', 2, 'Terrace',       1),
('11', 4, 'Terrace',       1),
('12', 4, 'Terrace',       1);

-- RESERVATIONS  (guest customers — no user account)

INSERT INTO `reservations`
    (`table_id`, `customer_name`, `customer_email`, `customer_phone`,
     `guest_count`, `reservation_date`, `reservation_time`, `duration`, `notes`, `status`)
VALUES
-- Today — confirmed
(3, 'Hans Zimmermann',   'hans.z@example.de',    '+49 170 111 2233', 4, CURDATE(), '12:30:00', 90,  NULL,                    'confirmed'),
(5, 'Petra Hoffmann',    'petra.h@example.de',   '+49 171 222 3344', 2, CURDATE(), '13:00:00', 60,  'Window seat preferred', 'confirmed'),
(7, 'Oliver Schulz',     'oliver.s@example.de',  '+49 172 333 4455', 6, CURDATE(), '19:00:00', 120, 'Anniversary dinner',    'confirmed'),
(9, 'Emma Koch',         'emma.k@example.de',    '+49 173 444 5566', 8, CURDATE(), '20:00:00', 150, 'Birthday party',        'confirmed'),

-- Tomorrow — pending / confirmed
(4, 'Max Braun',         'max.b@example.de',     '+49 174 555 6677', 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00:00', 90,  NULL,                       'pending'),
(6, 'Laura König',       'laura.k@example.de',   '+49 175 666 7788', 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:30:00', 90,  'Vegetarian menu required', 'confirmed'),
(1, 'Felix Richter',     'felix.r@example.de',   '+49 176 777 8899', 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '20:00:00', 60,  NULL,                       'pending'),

-- This weekend
(8, 'Claudia Bauer',     'claudia.b@example.de', '+49 177 888 9900', 5, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '19:00:00', 120, 'Allergy: nuts',             'pending'),
(11,'Jonas Werner',      'jonas.w@example.de',   '+49 178 999 0011', 4, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '20:30:00', 90,  'Terrace seating please',    'pending'),

-- Past records
(3, 'Anna Schwarz',      'anna.s@example.de',    '+49 179 000 1122', 3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '19:00:00', 90,  NULL, 'completed'),
(5, 'David Meyer',       'david.m@example.de',   '+49 180 111 2233', 2, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '12:00:00', 60,  NULL, 'completed'),
(2, 'Sophie Wolf',       'sophie.w@example.de',  '+49 181 222 3344', 2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '13:30:00', 60,  NULL, 'no_show'),
(4, 'Paul Neumann',      'paul.n@example.de',    '+49 182 333 4455', 4, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '20:00:00', 90,  NULL, 'cancelled');


-- ADDRESSES  (delivery orders only — order_id updated after orders insert)

INSERT INTO `addresses` (`street`, `city`, `postal_code`) VALUES
('Rüppurrer Str. 45',   'Karlsruhe', '76137'),
('Durlacher Allee 12',  'Karlsruhe', '76131'),
('Ettlinger Str. 99',   'Karlsruhe', '76135'),
('Brauerstraße 7',      'Karlsruhe', '76135'),
('Waldstraße 22',       'Karlsruhe', '76133');

/*
 ORDERS
 Referenced the order_status and payment_status by sub-select so the seed
 is not brittle against auto-increment ID values.
 The order_number is a simple convention: ORD-YYYYMMDD-XXXX, where XXXX is a sequence for that day.
 */

INSERT INTO `orders`
    (`order_number`, `customer_name`, `customer_email`, `customer_phone`,
     `reservation_id`, `type`, `address_id`,
     `subtotal`, `delivery_fee`, `tax`, `total`,
     `order_status_id`, `payment_status_id`, `payment_method`,
     `notes`, `handled_by`, `estimated_ready_time`, `delivered_at`, `paypal_transaction_id`)
VALUES

-- Dine-in (completed, linked to reservation 10 — Anna Schwarz)
('ORD-20250425-0001',
 'Anna Schwarz', 'anna.s@example.de', '+49 179 000 1122',
 10, 'dine_in', NULL,
 41.30, 0.00, 3.30, 44.60,
 (SELECT `id` FROM `order_status`  WHERE `status_name` = 'delivered'),
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'paid'),
 'cash', NULL, 3, NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), NULL),

-- Delivery — paid via PayPal (completed)
('ORD-20250425-0002',
 'Klaus Huber', 'klaus.h@example.de', '+49 151 333 4455',
 NULL, 'delivery', 1,
 29.80, 3.00, 2.64, 35.44,
 (SELECT `id` FROM `order_status`  WHERE `status_name` = 'delivered'),
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'paid'),
 'paypal', NULL, 4, NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), 'PAYPAL-TXN-AA112233'),

-- Takeout — cash — ready for pickup
('ORD-20250426-0001',
 'Monika Lange', 'monika.l@example.de', '+49 152 444 5566',
 NULL, 'takeout', NULL,
 16.80, 0.00, 1.34, 18.14,
 (SELECT `id` FROM `order_status`  WHERE `status_name` = 'ready'),
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'pending'),
 'cash', 'Extra napkins please', 4,
 DATE_ADD(NOW(), INTERVAL 10 MINUTE), NULL, NULL),

-- Delivery — currently being prepared
('ORD-20250426-0002',
 'Stefan Krause', 'stefan.k@example.de', '+49 153 555 6677',
 NULL, 'delivery', 2,
 38.40, 3.00, 3.31, 44.71,
 (SELECT `id` FROM `order_status`  WHERE `status_name` = 'preparing'),
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'pending'),
 'cash', NULL, 3,
 DATE_ADD(NOW(), INTERVAL 25 MINUTE), NULL, NULL),

-- Dine-in — newly placed, pending confirmation
('ORD-20250426-0003',
 'Hans Zimmermann', 'hans.z@example.de', '+49 170 111 2233',
 1, 'dine_in', NULL,
 23.80, 0.00, 1.90, 25.70,
 (SELECT `id` FROM `order_status`  WHERE `status_name` = 'confirmed'),
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'pending'),
 'cash', NULL, 4,
 DATE_ADD(NOW(), INTERVAL 20 MINUTE), NULL, NULL),

-- Delivery — out for delivery, PayPal paid
('ORD-20250426-0004',
 'Inge Brandt', 'inge.b@example.de', '+49 154 666 7788',
 NULL, 'delivery', 3,
 31.80, 3.00, 2.78, 37.58,
 (SELECT `id` FROM `order_status`  WHERE `status_name` = 'out_for_delivery'),
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'paid'),
 'paypal', NULL, NULL, NULL, NULL, 'PAYPAL-TXN-BB223344'),

-- Cancelled order
('ORD-20250424-0003',
 'Werner Fuchs', 'werner.f@example.de', '+49 155 777 8899',
 NULL, 'takeout', NULL,
 12.90, 0.00, 1.03, 13.93,
 (SELECT `id` FROM `order_status`  WHERE `status_name` = 'cancelled'),
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'pending'),
 'cash', NULL, NULL, NULL, NULL, NULL);


-- ORDER ITEMS

-- ORD-20250425-0001 (Anna Schwarz, dine-in)
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `item_name`, `quantity`, `unit_price`, `subtotal`, `notes`) VALUES
(1, 4,  'Spring Rolls',          1,  6.99, 6.99, NULL),
(1, 12, 'Spaghetti Carbonara',   2, 13.50, 27.00, NULL),
(1, 18, 'Tiramisu',              1,  7.50, 7.50, NULL);

-- ORD-20250425-0002 (Klaus Huber, delivery)
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `item_name`, `quantity`, `unit_price`, `subtotal`, `notes`) VALUES
(2, 9,  'Pepperoni Diavola',     1, 15.90, 15.90, NULL),
(2, 13, 'Tagliatelle Bolognese', 1, 14.50, 14.50, NULL);

-- ORD-20250426-0001 (Monika Lange, takeout)
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `item_name`, `quantity`, `unit_price`, `subtotal`, `notes`) VALUES
(3, 15, 'Classic Gourmet Burger', 1, 14.90, 14.90, 'No pickles'),
(3, 24, 'Espresso',               1,  2.90,  2.90, NULL);

-- ORD-20250426-0002 (Stefan Krause, delivery)
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `item_name`, `quantity`, `unit_price`, `subtotal`, `notes`) VALUES
(4, 6,  'Beef Tenderloin',        1, 28.50, 28.50, 'Medium-rare please'),
(4, 17, 'Greek Salad',            1,  9.90,  9.90, NULL);

-- ORD-20250426-0003 (Hans Zimmermann, dine-in)
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `item_name`, `quantity`, `unit_price`, `subtotal`, `notes`) VALUES
(5, 8,  'Margherita',             1, 13.90, 13.90, NULL),
(5, 26, 'Coca-Cola (330ml)',       2,  3.20,  6.40, NULL),
(5, 2,  'Bruschetta al Pomodoro', 1,  7.50,  7.50, NULL);

-- ORD-20250426-0004 (Inge Brandt, delivery)
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `item_name`, `quantity`, `unit_price`, `subtotal`, `notes`) VALUES
(6, 10, 'Quattro Formaggi',        1, 16.90, 16.90, NULL),
(6, 16, 'Truffle Mushroom Burger', 1, 16.90, 16.90, NULL);

-- PAYPAL TRANSACTIONS
INSERT INTO `paypal_transactions`
    (`order_id`, `transaction_id`, `payer_email`, `amount`, `currency`,
     `payment_status`, `payment_status_id`, `capture_id`, `invoice_id`)
VALUES
(2, 'PAYPAL-TXN-AA112233', 'klaus.h@example.de', 35.44, 'EUR', 'COMPLETED',
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'paid'),
 'CAP-AA112233', 'INV-ORD-20250425-0002'),

(6, 'PAYPAL-TXN-BB223344', 'inge.b@example.de',  37.58, 'EUR', 'COMPLETED',
 (SELECT `id` FROM `payment_status` WHERE `status_name` = 'paid'),
 'CAP-BB223344', 'INV-ORD-20250426-0004');

-- PAYMENT LOGS

INSERT INTO `payment_logs` (`order_id`, `action`, `message`) VALUES
(2, 'initiated', 'PayPal order created for ORD-20250425-0002'),
(2, 'captured',  'Payment captured: PAYPAL-TXN-AA112233, amount: €35.44'),
(6, 'initiated', 'PayPal order created for ORD-20250426-0004'),
(6, 'captured',  'Payment captured: PAYPAL-TXN-BB223344, amount: €37.58');


-- REVIEWS  (guest-submitted, mixed approved / pending)

INSERT INTO `reviews` (`order_id`, `name`, `email`, `rating`, `comment`, `is_visible`) VALUES
(1, 'Anna Schwarz',   'anna.s@example.de',  5, 'Exceptional evening. The carbonara was perfect and the service flawless.',  1),
(2, 'Klaus Huber',    'klaus.h@example.de', 4, 'Fast delivery and food was still hot. The Bolognese was delicious.',          1),
(5, 'Hans Zimm.',     'hans.z@example.de',  5, 'Pizza was outstanding. Will definitely return with the family.',              1),
(NULL, 'Anke Brecht', 'anke.b@example.de',  4, 'Lovely restaurant, warm atmosphere. Slightly long wait for mains.',           1),
(NULL, 'Rolf Sauer',  'rolf.s@example.de',  3, 'Food was good but portion sizes felt a little small for the price.',         0),  -- pending
(NULL, 'Jana Vogt',   'jana.v@example.de',  5, 'Best burger in Karlsruhe, no question. The truffle mayo is addictive.',       0);  -- pending


-- CONTACTS

INSERT INTO `contacts` (`name`, `email`, `phone`, `subject`, `message`, `status`) VALUES
('Gerd Hoffmann',  'gerd.h@example.de',  '+49 160 123 4567',
 'Private event enquiry',
 'Hello, I would like to enquire about hosting a birthday dinner for 20 guests next month. Could you send me menu and pricing options?',
 'read'),

('Maria Klein',    'maria.k@example.de', NULL,
 'Allergy information',
 'Could you please confirm which dishes on your menu are gluten-free? My husband has coeliac disease.',
 'replied'),

('Thomas Bauer',   'thomas.b@example.de', '+49 162 987 6543',
 'Catering enquiry',
 'We are organising a company event for 50 people and are looking for a catering partner. Are you available on 15 August?',
 'unread'),

('Sandra Vogel',   'sandra.v@example.de', NULL,
 'Feedback',
 'Just wanted to say your tiramisu is absolutely divine. My whole table loved it last Saturday. Thank you!',
 'read'),

('Michael Engel',  'michael.e@example.de', '+49 163 456 7890',
 'Reservation question',
 'Is it possible to reserve the private room for a group of 8 on a Friday evening?',
 'unread');

-- JOB POSITIONS

INSERT INTO `job_positions`
    (`title`, `department`, `employment_type`, `location`,
     `salary_range_min`, `salary_range_max`,
     `description`, `requirements`, `status`, `posted_date`, `closing_date`)
VALUES
('Head Chef',
 'Kitchen', 'full_time', 'Main Kitchen — Karlsruhe',
 62000, 80000,
 'We are looking for an experienced and passionate Head Chef to lead our culinary team at Gourmet Express. You will be responsible for menu development, kitchen management, and maintaining the highest standards of food quality and presentation.',
 '5+ years professional kitchen experience; 2+ years in a leadership role. Formal culinary training preferred. Strong organisational and communication skills.',
 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 45 DAY)),

('Sous Chef',
 'Kitchen', 'full_time', 'Main Kitchen — Karlsruhe',
 40000, 52000,
 'Support the Head Chef in day-to-day kitchen operations, manage section leads, oversee prep, and maintain food safety standards.',
 '3+ years kitchen experience; experience in a supervisory role is a plus.',
 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 45 DAY)),

('Line Cook',
 'Kitchen', 'full_time', 'Main Kitchen — Karlsruhe',
 28000, 36000,
 'Join our kitchen brigade to prepare high-quality dishes consistently and efficiently during service. Specific station TBD based on experience.',
 'Minimum 1 year kitchen experience. Food hygiene certificate required.',
 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY)),

('Floor Manager',
 'Service', 'full_time', 'Dining Room — Karlsruhe',
 34000, 44000,
 'Oversee the front-of-house team, ensure exceptional guest experiences, handle reservations and complaints, and support daily operations.',
 '2+ years FOH experience; leadership experience preferred. Fluent in German and English.',
 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY)),

('Waiter / Waitress',
 'Service', 'part_time', 'Dining Room — Karlsruhe',
 NULL, NULL,
 'Deliver outstanding dining experiences to our guests. Flexible shifts available including weekends and evenings.',
 'Guest-facing experience preferred. Positive attitude and team player mindset essential.',
 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 21 DAY)),

('Delivery Driver',
 'Logistics', 'part_time', 'Karlsruhe (15km radius)',
 NULL, NULL,
 'Ensure fast, courteous, and accurate delivery of food orders to our customers across Karlsruhe.',
 'Valid driving licence. Own vehicle preferred (mileage reimbursed). Reliable and punctual.',
 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY));

-- JOB APPLICATIONS

INSERT INTO `job_applications`
    (`job_position_id`, `applicant_name`, `applicant_email`, `applicant_phone`,
     `cover_letter`, `experience_years`, `current_company`, `expected_salary`,
     `status`, `review_notes`, `reviewed_by`)
VALUES
-- Head Chef applications
(1, 'René Dubois',      'rene.d@example.fr',  '+33 6 12 34 56 78',
 'I have spent 8 years in Michelin-starred kitchens across France and Germany. I am looking for a leadership role where I can contribute both creatively and operationally.',
 8.0, 'Le Jardin, Strasbourg', 75000,
 'shortlisted', 'Strong CV, excellent references. Schedule interview.',
 (SELECT `id` FROM `users` WHERE `username` = 'manager_sophie')),

(1, 'Sabine Keller',    'sabine.k@example.de', '+49 160 555 1234',
 'Five years as Sous Chef at Hotel Adler, Karlsruhe. Ready to step up into a Head Chef role.',
 5.0, 'Hotel Adler Karlsruhe', 65000,
 'reviewed', 'Solid local experience. Ask for a trial shift.',
 (SELECT `id` FROM `users` WHERE `username` = 'manager_sophie')),

(1, 'Lucas Braun',      'lucas.b@example.de', '+49 161 666 2345',
 'Passionate about modern European cuisine. Looking for a challenge.',
 3.5, 'Self-employed', 60000,
 'pending', NULL, NULL),

-- Line Cook applications
(3, 'Fatima Al-Rashid', 'fatima.a@example.de', '+49 162 777 3456',
 'Trained in Morocco and Germany, 2 years experience in a busy Karlsruhe bistro.',
 2.0, 'Bistro Central', NULL,
 'shortlisted', 'Great attitude in phone screen. Arrange kitchen trial.',
 (SELECT `id` FROM `users` WHERE `username` = 'admin')),

(3, 'Erik Johansson',   'erik.j@example.se',  '+46 70 123 4567',
 'Swedish cook, 1.5 years in Stockholm restaurants, relocating to Karlsruhe.',
 1.5, 'Kvarnen Restaurang', NULL,
 'pending', NULL, NULL),

-- Waiter applications
(5, 'Chiara Rossi',     'chiara.r@example.it', '+39 340 123 4567',
 'Experienced waitress from Florence, currently living in Karlsruhe. Fluent Italian, German, English.',
 3.0, 'Ristorante Da Marco', NULL,
 'accepted', 'Starts 01 June.', (SELECT `id` FROM `users` WHERE `username` = 'manager_sophie')),

(5, 'Ben Adler',        'ben.a@example.de',   '+49 163 888 4567',
 'Student looking for part-time work. Friendly, reliable, quick learner.',
 0.5, NULL, NULL,
 'pending', NULL, NULL),

-- Delivery Driver applications
(6, 'Yusuf Demir',      'yusuf.d@example.de', '+49 175 999 5678',
 'Reliable driver with 3 years food delivery experience. Own car, clean licence.',
 3.0, 'Lieferando Partner', NULL,
 'accepted', 'Good fit. Starts immediately.',
 (SELECT `id` FROM `users` WHERE `username` = 'admin'));

-- Re-enable FK checks
SET FOREIGN_KEY_CHECKS = 1;