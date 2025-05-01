<?php include('../includes/header.php'); ?>
<?php require_once('../config/database.php'); ?>

<h2 class="text-center mb-4">🎧 All Reviewed Albums</h2>

<?php
// Fetch all albums with reviews
$query = "
  SELECT a.id, a.title, a.artist, a.cover_image_url, r.rating, r.date_listened 
  FROM albums a
  JOIN reviews r ON a.id = r.album_id
  ORDER BY r.date_listened DESC
";
$result = $conn->query($query);

if ($result->num_rows === 0): ?>
    <div class="alert alert-info text-center">You haven’t reviewed any albums yet!</div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="<?= htmlspecialchars($row['cover_image_url']) ?>" class="card-img-top" alt="Cover">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text"><strong>Artist:</strong> <?= htmlspecialchars($row['artist']) ?></p>
                        <p class="card-text"><strong>Rating:</strong> <?= $row['rating'] ?>/10</p>
                        <p class="card-text"><small class="text-muted">Listened: <?= $row['date_listened'] ?></small></p>
                        <a href="album_review.php?album_id=<?= $row['id'] ?>" class="btn btn-outline-primary w-100 mt-2">View Review</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif;
?>

<?php include('../includes/footer.php'); ?>