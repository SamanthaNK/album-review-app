<?php include('../includes/header.php'); ?>
<?php require_once('../config/database.php'); ?>

<!-- Main Heading Section -->
<section class="hero-banner">
  <div class="container-fluid px-0">
    <div class="position-relative w-100 h-100">
      <!-- Decorative Elements -->
      <div class="position-absolute" style="top: 0; left: 0; width: 100%; height: 100%; opacity: 0.15;">
        <i class="fa-solid fa-compact-disc position-absolute" style="font-size: 12rem; top: -2rem; left: 5%; transform: rotate(-15deg);"></i>
        <i class="fa-solid fa-music position-absolute" style="font-size: 8rem; bottom: 3rem; right: 12%; transform: rotate(20deg);"></i>
        <i class="fa-solid fa-headphones position-absolute" style="font-size: 10rem; top: 25%; right: 30%; transform: rotate(-10deg);"></i>
      </div>

      <!-- Main Content -->
      <div class="container position-relative h-100 d-flex flex-column justify-content-center">
        <div class="text-center">
          <h1 class="display-1 fw-bold mb-4 text-white">
            New Music, New Me
          </h1>

          <p class="lead mb-5 text-white" style="font-size: 1.4rem; max-width: 700px; margin-left: auto; margin-right: auto;">
            Discover fresh albums, share your thoughts, and find your new favorite songs.
          </p>

          <a href="add_album.php" class="btn btn-light rounded-pill px-5 py-3 fw-bold" style="font-size: 1.1rem;">
            <i class="fa-solid fa-plus me-2"></i> Add New Album Review
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container py-5">

  <!-- Last Listened Section -->
  <section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Latest Listens</h2>
      <a href="all_albums.php" class="text-decoration-none d-flex align-items-center">
        See all album reviews <i class="fa-solid fa-arrow-right ms-2"></i>
      </a>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php
      $recentQuery = "SELECT id, title, artist, cover_image_url, created_at FROM albums ORDER BY created_at DESC LIMIT 3";
      $recentResult = $conn->query($recentQuery);
      $albums = [];

      if ($recentResult && $recentResult->num_rows > 0) {
        while ($row = $recentResult->fetch_assoc()) {
          $albums[] = $row;
        }
      }

      // Add placeholders if fewer than 3
      if (count($albums) < 3) {
        for ($i = count($albums); $i < 3; $i++) {
          $albums[] = [
            'id' => 0,
            'title' => 'Album Title',
            'artist' => 'Artist Name',
            'cover_image_url' => '',
            'created_at' => date('Y-m-d', strtotime("-{$i} days")),
          ];
        }
      }

      foreach ($albums as $index => $album):
        $formattedDate = date('F j, Y', strtotime($album['created_at']));
        $animationDelay = ($index + 1) * 100;
      ?>
        <div class="col animated-card" style="animation-delay: <?= $animationDelay ?>ms">
          <div class="card h-100">
            <div class="p-3">
              <?php if (!empty($album['cover_image_url'])): ?>
                <div class="rounded mb-3" style="height: 200px; overflow: hidden;">
                  <img src="<?= htmlspecialchars($album['cover_image_url']) ?>"
                    alt="Album Cover"
                    class="img-fluid w-100 h-100 object-fit-cover">
                </div>
              <?php else: ?>
                <div class="bg-secondary rounded mb-3 d-flex justify-content-center align-items-center"
                  style="height: 200px;">
                  <i class="fa-solid fa-music fa-2x"></i>
                </div>
              <?php endif; ?>

              <h6 class="fw-bold text-truncate"><?= htmlspecialchars($album['title']) ?></h6>
              <p class="text-muted small mb-2"><?= htmlspecialchars($album['artist']) ?></p>

              <div class="d-flex align-items-center mb-3">
                <i class="far fa-calendar-alt text-accent me-2 small"></i>
                <span class="text-muted small"><?= $formattedDate ?></span>
              </div>

              <?php if ($album['id'] != 0): ?>
                <a href="album_review.php?album_id=<?= $album['id'] ?>"
                  class="btn btn-outline-primary btn-sm rounded-pill px-3 w-100">
                  <i class="fa-solid fa-eye me-2"></i> View Review
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Bottom Features Section -->
  <section class="row mb-5">
    <!-- Feeling Stuck Section -->
    <div class="col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-body p-4 text-center">
          <i class="fa-solid fa-shuffle fa-3x mb-3 text-accent"></i>
          <h5 class="fw-bold mb-3">Feeling Stuck?</h5>
          <p class="text-muted mb-4">Discover your next musical obsession with our random picks</p>
          <a href="recommendations.php" class="btn btn-dark rounded-pill px-4">
            <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Get Random Album
          </a>
        </div>
      </div>
    </div>

    <!-- Spotify Section -->
    <div class="col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-body p-4 text-center">
          <i class="fa-brands fa-spotify fa-3x mb-3" style="color: #1ed760"></i>
          <h5 class="fw-bold mb-3">Your Favorites on Spotify</h5>
          <p class="text-muted mb-4">Connect and access your carefully curated playlists</p>
          <a href="https://open.spotify.com/playlist/3ol1tazrjdML4GWlnALA2U?si=ba9fe1bff73d40d3" target="_blank"
            class="btn btn-outline-dark rounded-pill px-4">
            <i class="fa-brands fa-spotify me-2"></i> Open Spotify Playlist
          </a>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include('../includes/footer.php'); ?>