<?php
function galleryImgUrl($url): string {
    if (empty($url)) return '/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/signature-blossom.png';
    if (strpos($url, '/') === 0 || strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }
    return '/MonetNails-Ecommerce2/MonetNails-Ecommerce/' . ltrim($url, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>The Gallery &amp; Shop | Monet Nails</title>
  <meta name="description" content="Browse and shop our handcrafted signature press-on nail sets at Monet Nails Bar.">

  <!-- Google Fonts: Bodoni Moda (headings) + Manrope (body) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;1,6..96,400&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Gallery Stylesheet -->
  <link rel="stylesheet" href="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/css/gallery.css?v=2">
  <style>
    .top-nav { z-index: 200 !important; }
    .popup-overlay { z-index: 90 !important; }
    .profile-popup { z-index: 300 !important; }
    .popup-item svg, .popup-item img { pointer-events: none !important; }
  </style>
</head>
<body>

<!-- ==================== TOP NAV (Registered Customer) ==================== -->
<?php
$activeNav = 'gallery';
require __DIR__ . '/../../includes/header.php';
?>

<!-- ==================== HERO ==================== -->
<header class="hero" id="gallery-hero">
  <h1>The Gallery &amp; Shop</h1>
  <p>Artistry you can wear. Browse and purchase our signature handcrafted press-on sets.</p>
</header>

<!-- ==================== CATEGORY PILLS + FILTER BUTTON ==================== -->
<div class="container">
  <div class="toolbar">
    <!-- Quick category pills -->
    <div class="pills">
      <a class="pill <?php echo (count($selected_categories) === 0) ? 'active' : ''; ?>"
         href="index.php?page=gallery" id="pill-all">All</a>

      <?php foreach ($categories as $cat): ?>
        <?php $isSelected = in_array((int)$cat['category_id'], $selected_categories, true); ?>
        <a class="pill <?php echo $isSelected ? 'active' : ''; ?>"
           href="index.php?page=gallery&category[]=<?php echo (int)$cat['category_id']; ?>">
          <?php echo htmlspecialchars($cat['category_name']); ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Filter modal toggle button -->
    <button type="button" class="filter-btn" id="filterBtn" onclick="openFilters()">
      <img src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/icon-25.svg" alt="Filter">
      <span>Filter<?php echo (count($selected_categories) > 0) ? ' (' . count($selected_categories) . ')' : ''; ?></span>
    </button>
  </div>
</div>

<!-- ==================== PRODUCT GRID ==================== -->
<main class="container product-section" id="product-section">
  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <article class="product-card" id="product-<?php echo (int)$product['product_id']; ?>">
        <a href="index.php?page=product&id=<?php echo (int)$product['product_id']; ?>" class="card-img-link">
          <div class="card-img">
            <img src="<?php echo htmlspecialchars(galleryImgUrl($product['image'])); ?>"
                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                 loading="lazy">
          </div>
        </a>
        <h3 class="card-title">
          <a href="index.php?page=product&id=<?php echo (int)$product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a>
        </h3>
        <p class="card-price">Rs. <?php echo number_format((float)$product['price'], 0); ?></p>
        <div class="card-actions">
          <a class="btn-buy" href="index.php?page=checkout&buy_now_product_id=<?php echo (int)$product['product_id']; ?>">Buy Now</a>
          <a class="btn-cart" href="index.php?page=cart&action=add&product_id=<?php echo (int)$product['product_id']; ?>">Add to Cart</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <?php if (empty($products)): ?>
    <p class="empty-note">No products found for this selection.</p>
  <?php endif; ?>
</main>

<!-- ==================== SITE FOOTER ==================== -->
<?php require __DIR__ . '/../../includes/footer.php'; ?>


<!-- ==================== FILTER POP-UP MODAL ==================== -->
<div class="filter-overlay" id="filterOverlay" onclick="closeOnOverlay(event)">
  <div class="filter-modal">
    <div class="filter-header">
      <img class="filter-icon" src="/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/gallery/icon-25.svg" alt="">
      <h2 class="filter-title">Filters</h2>
      <button type="button" class="filter-close" onclick="closeFilters()">&times;</button>
    </div>

    <form method="GET" action="index.php">
      <input type="hidden" name="page" value="gallery">
      <p class="filter-label">CATEGORY</p>
      <?php foreach ($categories as $cat): ?>
        <?php $isSelected = in_array((int)$cat['category_id'], $selected_categories, true); ?>
        <label class="filter-option">
          <input type="checkbox"
                 name="category[]"
                 value="<?php echo (int)$cat['category_id']; ?>"
                 <?php echo $isSelected ? 'checked' : ''; ?>>
          <span class="checkbox-box"></span>
          <span class="option-name"><?php echo htmlspecialchars($cat['category_name']); ?></span>
          <span class="option-count"><?php echo (int)$cat['product_count']; ?></span>
        </label>
      <?php endforeach; ?>

      <div class="filter-actions">
        <a class="clear-link" href="index.php?page=gallery">CLEAR ALL</a>
        <button type="submit" class="btn-apply">Apply Filters</button>
      </div>
    </form>
  </div>
</div>

<!-- ==================== JAVASCRIPT ==================== -->
<script>

  /* Filter Modal */
  function openFilters() {
    document.getElementById("filterOverlay").classList.add("show");
  }

  function closeFilters() {
    document.getElementById("filterOverlay").classList.remove("show");
  }

  function closeOnOverlay(event) {
    if (event.target.id === "filterOverlay") {
      closeFilters();
    }
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeFilters();
  });
</script>

</body>
</html>
