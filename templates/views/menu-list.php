<!DOCTYPE html>
<html lang="en">
<head>
    <title>Restaurant Menu</title>
    <style>
        .menu-item { border-bottom: 1px solid #eee; padding: 15px; }
        .price { color: #2ecc71; font-weight: bold; }
        .category-tag { font-size: 0.8rem; background: #eee; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Our Menu</h1>

    <?php if (empty($dishes)): ?>
        <p>We are currently updating our menu. Check back soon!</p>
    <?php else: ?>
        <div id="menu-container">
            <?php foreach ($dishes as $dish): ?>
                <div class="menu-item">
                    <h3><?= htmlspecialchars($dish['name']) ?></h3>
                    <p><?= htmlspecialchars($dish['description']) ?></p>
                    <span class="price"><?= number_format($dish['price'], 2) ?> €</span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>