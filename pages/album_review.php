<?php include('../includes/header.php'); ?>
<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
  <div class="alert alert-success text-center">✅ Review added successfully!</div>
<?php endif; ?>

<?php
require_once('../config/database.php');

if (!isset($_GET['album_id'])) {
    die("Album ID is required.");
}

$album_id = (int) $_GET['album_id'];

// Fetch album details
$albumQuery = $conn->prepare("SELECT * FROM albums WHERE id = ?");
$albumQuery->bind_param("i", $album_id);
$albumQuery->execute();
$albumResult = $albumQuery->get_result();

if ($albumResult->num_rows === 0) {
    echo "<div class='alert alert-danger'>Album not found.</div>";
    include('../includes/footer.php');
    exit();
}

$album = $albumResult->fetch_assoc();

// Fetch genres
$genreQuery = $conn->prepare("
    SELECT g.name 
    FROM genres g 
    INNER JOIN album_genres ag ON g.id = ag.genre_id 
    WHERE ag.album_id = ?
");
$genreQuery->bind_param("i", $album_id);
$genreQuery->execute();
$genreResult = $genreQuery->get_result();

$genres = [];
while ($row = $genreResult->fetch_assoc()) {
    $genres[] = $row['name'];
}

// Fetch review
$reviewQuery = $conn->prepare("SELECT * FROM reviews WHERE album_id = ?");
$reviewQuery->bind_param("i", $album_id);
$reviewQuery->execute();
$reviewResult = $reviewQuery->get_result();

$review = $reviewResult->fetch_assoc();
?>

<?php if (isset($_GET['status'])): ?>
  <?php if ($_GET['status'] === 'album_added'): ?>
    <div class="alert alert-success text-center">✅ Album saved successfully!</div>
  <?php elseif ($_GET['status'] === 'review_saved' || $_GET['status'] === 'success'): ?>
    <div class="alert alert-success text-center">✅ Your review was saved successfully!</div>
  <?php endif; ?>
<?php endif; ?>

<h2 class="text-center mb-4"><?= htmlspecialchars($album['title']) ?> by <?= htmlspecialchars($album['artist']) ?></h2>

<div class="card mb-4 mx-auto" style="max-width: 700px;">
    <div class="row g-0">
        <div class="col-md-4">
            <img src="<?= htmlspecialchars($album['cover_image_url']) ?>" class="img-fluid rounded-start" alt="Album Cover">
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <p class="card-text"><strong>Release Year:</strong> <?= $album['release_year'] ?></p>
                <p class="card-text"><strong>Language:</strong> <?= htmlspecialchars($album['language']) ?></p>

                <?php if (!empty($genres)): ?>
                    <p class="card-text"><strong>Genres:</strong>
                        <?php foreach ($genres as $genre): ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($genre) ?></span>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($album['spotify_link'])): ?>
                    <a href="<?= htmlspecialchars($album['spotify_link']) ?>" target="_blank" class="btn btn-outline-dark mt-2">🎧 Open on Spotify</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($review): ?>
    <div class="card mx-auto" style="max-width: 700px;">
        <div class="card-header bg-success text-white">
            Review
        </div>
        <div class="card-body">
            <p><strong>Date Listened:</strong> <?= htmlspecialchars($review['date_listened']) ?></p>
            <p><strong>Favorite Songs:</strong> <?= nl2br(htmlspecialchars($review['favorite_songs'])) ?></p>
            <p><strong>Rating:</strong> <?= $review['rating'] ?>/10</p>
            <p><strong>Notes:</strong></p>
            <p><?= nl2br(htmlspecialchars($review['review_notes'])) ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="text-center">
        <p class="mt-4">No review found for this album.</p>
        <a href="add_album_review.php?album_id=<?= $album_id ?>" class="btn btn-primary">Add Review</a>
    </div>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>