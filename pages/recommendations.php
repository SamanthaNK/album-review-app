<?php include('../includes/header.php'); ?>

<?php
// Path to your CSV
$csvFile = "../assets/data/recommendations.csv"; // adjust path if needed

// Load a random album
$album = null;
if (($handle = fopen($csvFile, "r")) !== false) {
  $headers = fgetcsv($handle, 1000, ",");
  $rows = [];
  while (($data = fgetcsv($handle, 1000, ",")) !== false) {
    $rows[] = array_combine($headers, $data);
  }
  fclose($handle);

  if (count($rows) > 0) {
    $album = $rows[array_rand($rows)];
  }
}
?>

<div class="container py-5">
  <div class="page-header">
    <h1>Discover New Music</h1>
    <p class="subtitle">Find your next favorite album with our personalized recommendations</p>
  </div>

  <?php if ($album): ?>
    <div class="recommendation-container" style="min-height: 700px; display: flex; align-items: center; justify-content: center;">
      <div class="card mx-auto shadow-lg animated-card" style="max-width: 700px;">
        <div class="position-relative">
          <!-- Album Cover Image -->
          <div style="height: 500px; overflow: hidden;">
            <img src="<?= htmlspecialchars($album['cover_image_url']) ?>"
              class="img-fluid w-100 h-100 object-fit-cover"
              alt="<?= htmlspecialchars($album['title']) ?> album cover">
          </div>

          <!-- Album Info Card Body -->
          <div class="card-body text-center p-4">
            <h2 class="card-title mb-1"><?= htmlspecialchars($album['title']) ?></h2>
            <p class="text-muted mb-3">by <strong><?= htmlspecialchars($album['artist']) ?></strong> (<?= $album['release_year'] ?>)</p>

            <!-- Language Badge -->
            <div class="mb-4">
              <span class="badge bg-secondary rounded-pill px-3 py-2">
                <?= htmlspecialchars($album['language']) ?>
              </span>
            </div>

            <!-- Action Buttons - Horizontal Layout -->
            <div class="button-container mb-2">
              <div class="d-flex justify-content-center gap-3">
                <?php if (!empty($album['spotify_link'])): ?>
                  <a href="<?= htmlspecialchars($album['spotify_link']) ?>" target="_blank"
                    class="btn btn-dark rounded-pill btn-hover px-4">
                    <i class="fa-brands fa-spotify"></i>
                  </a>
                <?php endif; ?>

                <!-- Add to Collection Button -->
                <a href="add_album.php?title=<?= urlencode($album['title']) ?>&artist=<?= urlencode($album['artist']) ?>&release_year=<?= $album['release_year'] ?>&language=<?= urlencode($album['language']) ?>&cover_image_url=<?= urlencode($album['cover_image_url']) ?>&spotify_link=<?= urlencode($album['spotify_link']) ?>"
                  class="btn btn-primary rounded-pill btn-hover px-4 flex-grow-1">
                  <i class="fa-solid fa-plus me-2"></i> Add to Collection
                </a>

                <!-- Shuffle Button -->
                <a href="recommendations.php" class="btn btn-outline-dark rounded-pill btn-hover px-4">
                  <i class="fa-solid fa-shuffle"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger text-center">
      <i class="fa-solid fa-circle-exclamation me-2"></i>
      No recommendations found. Make sure your CSV is valid.
    </div>
  <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>