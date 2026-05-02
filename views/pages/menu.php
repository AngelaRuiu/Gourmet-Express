<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Our Menu') ?> — Gourmet Express</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Josefin+Sans:wght@300;400&display=swap" 
    rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream:    #FAF7F2;
            --cream-dk: #EDE8DF;
            --ink:      #141210;
            --ink-soft: #2A2520;
            --gold:     #CFA255;
            --gold-lt:  #E8C07A;
            --rule:     #6B6057;
            --muted:    #B0A396;
        }
 
        html { scroll-behavior: smooth; }
 
        body {
            background-color: var(--ink);
            color: var(--cream);
            font-family: 'Josefin Sans', sans-serif;
            font-weight: 400;
            letter-spacing: 0.04em;
            min-height: 100vh;
        }

        /* NAV */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 3rem;
            background: var(--ink);
            border-bottom: 1px solid rgba(184,146,58,0.25);
        }

        .nav-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 400;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--gold);
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            font-size: 0.7rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--cream-dk);
            opacity: 1;
            transition: color 0.2s;
        }

        .nav-links a:hover,
        .nav-links a.active { opacity: 1; color: var(--gold-lt); }

        /* HERO */
        .hero {
            text-align: center;
            padding: 5rem 2rem 4rem;
            position: relative;
        }

        .hero::before {
            content: '';
            display: block;
            width: 1px;
            height: 60px;
            background: linear-gradient(to bottom, transparent, var(--gold));
            margin: 0 auto 2rem;
        }

        .hero-eyebrow {
            font-size: 0.65rem;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1.25rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(3rem, 7vw, 5.5rem);
            font-weight: 300;
            line-height: 1;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--cream);
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 0.7rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .hero::after {
            content: '';
            display: block;
            width: 60px;
            height: 1px;
            background: var(--gold);
            margin: 2.5rem auto 0;
        }

        /*  CATEGORY TABS  */
        .category-nav {
            display: flex;
            justify-content: center;
            gap: 0;
            padding: 2.5rem 2rem 0;
            flex-wrap: wrap;
        }

        .category-btn {
            background: transparent;
            border: 1px solid rgba(200,188,168,0.2);
            color: var(--muted);
            padding: 0.75rem 1.75rem;
            cursor: pointer;
            font-family: 'Josefin Sans', sans-serif;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            font-size: 0.68rem;
            transition: color 0.2s, border-color 0.2s;
        }

         .category-btn:hover { color: var(--gold); }

        .category-btn.active {
            color: var(--gold);
            border-bottom-color: var(--gold);
        }

        /*  MENU WRAPPER  */
        .menu-wrapper {
            max-width: 860px;
            margin: 0 auto;
            padding: 3rem 2rem 6rem;
        }

        /*  SECTION  */
        .menu-section {
            margin-bottom: 4rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-header::before,
        .section-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(184,146,58,0.3);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 400;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--gold);
            white-space: nowrap;
        }

        /*  MENU ITEM  */
        .menu-item {
            display: grid;
            grid-template-columns: 1fr auto;
            grid-template-rows: auto auto;
            gap: 0 1rem;
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(200,188,168,0.1);
            position: relative;
            transition: background 0.2s;
        }

        .menu-item:last-child { border-bottom: none; }

        .menu-item:hover { background: rgba(184,146,58,0.03); }

        .item-name-row {
            display: flex;
            align-items: baseline;
            gap: 0;
        }

        .item-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 400;
            letter-spacing: 0.04em;
            color: var(--cream);
            flex-shrink: 0;
        }

        /* dotted separator line */
        .item-separator {
            flex: 1;
            border-bottom: 1px dotted rgba(200,188,168,0.25);
            margin: 0 0.75rem 0.2rem;
            min-width: 1rem;
        }

        .item-price {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            font-weight: 300;
            color: var(--gold-lt);
            letter-spacing: 0.04em;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .item-description {
            grid-column: 1 / -1;
            font-size: 0.82rem;
            letter-spacing: 0.06em;
            color: var(--muted);
            line-height: 1.75;
            padding-top: 0.4rem;
            font-style: italic;
            font-family: 'Playfair Display', serif;
            font-weight: 400;
            text-transform: none;
        }

        .item-badges {
            grid-column: 1 / -1;
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .badge {
            font-size: 0.58rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            padding: 0.2rem 0.6rem;
            border: 1px solid;
        }

        .badge-unavailable {
            border-color: rgba(122,111,99,0.4);
            color: var(--muted);
        }

        .badge-new {
            border-color: rgba(184,146,58,0.5);
            color: var(--gold-lt);
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 6rem 2rem;
        }

        .empty-state p {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 300;
            color: var(--muted);
            font-style: italic;
        }

        /* FOOTER */
        footer {
            border-top: 1px solid rgba(184,146,58,0.2);
            text-align: center;
            padding: 2.5rem;
        }

        footer p {
            font-size: 0.65rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--muted);
        }

        footer .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--gold);
            letter-spacing: 0.2em;
            display: block;
            margin-bottom: 0.75rem;
        }

        /*  REVEAL ANIMATION  */
        .menu-section {
            opacity: 0;
            transform: translateY(16px);
            animation: reveal 0.6s ease forwards;
        }

        @keyframes reveal {
            to { opacity: 1; transform: translateY(0); }
        }

        <?php
        // Stagger each section's animation
        $sectionCount = count($groupedDishes ?? []);
        for ($i = 0; $i < max($sectionCount, 6); $i++):
        ?>
        .menu-section:nth-child(<?= $i + 1 ?>) { animation-delay: <?= $i * 0.08 ?>s; }
        <?php endfor; ?>

        /* ─ RESPONSIVE  */
        @media (max-width: 640px) {
            nav { padding: 1rem 1.25rem; }
            .nav-links { gap: 1.5rem; }
            .menu-wrapper { padding: 2rem 1.25rem 4rem; }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav>
        <span class="nav-brand">Gourmet Express</span>
        <ul class="nav-links">
            <li><a href="/">Home</a></li>
            <li><a href="/menu" class="active">Menu</a></li>
            <li><a href="/reservations">Reservations</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>

    <!-- Hero -->
    <header class="hero">
        <p class="hero-eyebrow">Gourmet Express</p>
        <h1 class="hero-title">Our Menu</h1>
        <p class="hero-subtitle">Seasonal ingredients &mdash; crafted with passion</p>
    </header>

    <?php if (empty($dishes)): ?>

        <div class="empty-state">
            <p>We are updating our menu.<br>Please check back soon.</p>
        </div>

    <?php else:

        /*
         * Group dishes by category_id (WIP: To be added the category names).
         * Falls back to a single "Menu" group if neither exists.
         */
        $groupedDishes = [];
        foreach ($dishes as $dish) {
            $category = $dish['category_id'] ? 'Category ' . $dish['category_id'] : 'Menu';
            $groupedDishes[$category][] = $dish;
        }

        $categories = array_keys($groupedDishes);
    ?>

    <!-- Category tabs (JS-driven filter) -->
    <?php if (count($categories) > 1): ?>
    <nav class="category-nav" aria-label="Menu categories">
        <button class="category-btn active" data-cat="all">All</button>
        <?php foreach ($categories as $cat): ?>
            <button class="category-btn" data-category="<?= htmlspecialchars($cat) ?>">
                <?= htmlspecialchars($cat) ?>
            </button>
        <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <!-- Menu sections -->
    <main class="menu-wrapper">

        <?php foreach ($groupedDishes as $category => $items): ?>

        <section class="menu-section" data-section="<?= htmlspecialchars($category) ?>">

            <div class="section-header">
                <span class="section-title"><?= htmlspecialchars($category) ?></span>
            </div>

            <?php foreach ($items as $dish): ?>

            <article class="menu-item">

                <div class="item-name-row">
                    <h3 class="item-name"><?= htmlspecialchars($dish['name']) ?></h3>
                    <span class="item-separator" aria-hidden="true"></span>
                    <span class="item-price">
                        <?= number_format((float)($dish['price'] ?? 0), 2) ?>&nbsp;&euro;
                    </span>
                </div>

                <?php if (!empty($dish['description'])): ?>
                    <p class="item-description"><?= htmlspecialchars($dish['description']) ?></p>
                <?php endif; ?>

                <?php
                $isAvailable = $dish['is_available'] ?? true;
                $isNew       = $dish['is_new'] ?? false;
                if (!$isAvailable || $isNew):
                ?>
                <div class="item-badges">
                    <?php if ($isNew): ?>
                        <span class="badge badge-new">New</span>
                    <?php endif; ?>
                    <?php if (!$isAvailable): ?>
                        <span class="badge badge-unavailable">Currently unavailable</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </article>

            <?php endforeach; ?>
        </section>

        <?php endforeach; ?>

    </main>

    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <span class="footer-brand">Gourmet Express</span>
        <p>&copy; <?= date('Y') ?> &nbsp;&mdash;&nbsp; All rights reserved</p>
    </footer>

    <!-- Category filter script -->
    <script>
        const categoryBtns  = document.querySelectorAll('.category-btn');
        const sections = document.querySelectorAll('.menu-section');

        categoryBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                categoryBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const target = btn.dataset.category;

                sections.forEach(sec => {
                    const show = target === 'all' || sec.dataset.section === target;
                    sec.style.display = show ? '' : 'none';
                });
            });
        });
    </script>

</body>
</html>