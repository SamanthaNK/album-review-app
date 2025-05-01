<?php include('../includes/header.php'); ?>
<?php require_once('../config/database.php'); ?>

<h1 class="text-center mb-4">🎶 New Music, New Me</h1>

<div class="text-center mb-5">
  <a href="add_album.php" class="btn btn-success m-2">➕ Add a New Album</a>
  <a href="all_albums.php" class="btn btn-primary m-2">📚 View All Reviewed Albums</a>
  <a href="recommendations.php" class="btn btn-outline-secondary m-2">🎲 Feeling Stuck?</a>
</div>

<h3 class="mb-3">🆕 Recently Added Albums</h3>

<?php
$latest = $conn->query("SELECT id, title, artist, cover_image_url FROM albums ORDER BY created_at DESC LIMIT 6");

if ($latest->num_rows === 0): ?>
  <p class="text-muted">No albums added yet.</p>
<?php else: ?>
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php while ($row = $latest->fetch_assoc()): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="<?= htmlspecialchars($row['cover_image_url']) ?>" class="card-img-top" alt="Album Cover">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="card-text"><strong>Artist:</strong> <?= htmlspecialchars($row['artist']) ?></p>
            <a href="album_review.php?album_id=<?= $row['id'] ?>" class="btn btn-outline-primary w-100">View Album</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>