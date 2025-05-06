<?php include('../includes/header.php'); ?>
<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    <div class="alert alert-success text-center">
        <i class="fa-solid fa-circle-check me-2"></i> Review added successfully!
    </div>
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
        <div class="alert alert-success text-center">
            <i class="fa-solid fa-circle-check me-2"></i> Album saved successfully!
        </div>
    <?php elseif ($_GET['status'] === 'review_saved' || $_GET['status'] === 'success'): ?>
        <div class="alert alert-success text-center">
            <i class="fa-solid fa-circle-check me-2"></i> Your review was saved successfully!
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Album Header Section -->
<div class="page-header">
    <h2><?= htmlspecialchars($album['title']) ?></h2>
    <p class="subtitle">by <?= htmlspecialchars($album['artist']) ?></p>
    <?php if (!empty($album['spotify_link'])): ?>
        <a href="<?= htmlspecialchars($album['spotify_link']) ?>" target="_blank"
            class="btn btn-light rounded-pill px-4 btn-hover">
            <i class="fa-brands fa-spotify me-2"></i> Listen on Spotify
        </a>
    <?php endif; ?>
</div>

<div class="container">
    <div class="row g-4">
        <!-- Album Info Card -->
        <div class="col-lg-5">
            <div class="card border-0 shadow h-100">
                <div class="text-center p-4">
                    <div class="position-relative mb-4" style="padding-bottom: 100%; overflow: hidden;">
                        <img src="<?= htmlspecialchars($album['cover_image_url']) ?>"
                            class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                            style="border-radius: var(--border-radius)"
                            alt="Album Cover">
                    </div>

                    <div class="album-details mt-4">
                        <div class="row mb-3">
                            <div class="col-6 text-start">
                                <p class="text-muted mb-1 small">Release Year</p>
                                <p class="fw-bold"><?= $album['release_year'] ?></p>
                            </div>
                            <div class="col-6 text-start">
                                <p class="text-muted mb-1 small">Language</p>
                                <p class="fw-bold"><?= htmlspecialchars($album['language']) ?></p>
                            </div>
                        </div>

                        <?php if (!empty($genres)): ?>
                            <div class="text-start mb-3">
                                <p class="text-muted mb-2 small">Genres</p>
                                <div>
                                    <?php foreach ($genres as $genre): ?>
                                        <span class="genre-tag mb-2"><?= htmlspecialchars($genre) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Content Card -->
        <div class="col-lg-7">
            <?php if ($review): ?>
                <div class="card border-0 shadow h-100">
                    <div class="card-header border-0 bg-white pt-4 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold text-primary mb-0">
                                <i class="fa-solid fa-headphones me-2"></i> My Review
                            </h4>
                            <div class="bg-secondary px-3 py-2 rounded-pill">
                                <span class="fw-bold" style="font-size: 1.2rem;">
                                    <?= $review['rating'] ?><span class="text-muted">/10</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="review-info mb-4">
                            <div class="row gx-4 gy-2">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-regular fa-calendar text-accent me-2"></i>
                                        <div>
                                            <p class="text-muted small mb-0">Date Listened</p>
                                            <p class="mb-0 fw-medium"><?= date('F j, Y', strtotime($review['date_listened'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($review['favorite_songs'])): ?>
                            <div class="favorite-songs mb-4">
                                <h5 class="text-accent mb-2">
                                    <i class="fa-solid fa-heart me-2"></i> Favorite Songs
                                </h5>
                                <div class="ps-4 py-2 review-card">
                                    <?php
                                    $songs = explode("\n", $review['favorite_songs']);
                                    foreach ($songs as $song):
                                        if (trim($song) !== ''):
                                    ?>
                                            <div class="mb-1">
                                                <i class="fa-solid fa-music me-2 text-muted"></i> <?= htmlspecialchars(trim($song)) ?>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="review-notes">
                            <h5 class="text-accent mb-3">
                                <i class="fa-solid fa-pen me-2"></i> Notes
                            </h5>
                            <div class="ps-4 py-3 review-card">
                                <?= nl2br(htmlspecialchars($review['review_notes'])) ?>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <a href="edit_review.php?album_id=<?= $album_id ?>" class="btn btn-outline-primary rounded-pill me-2">
                                <i class="fa-solid fa-edit me-2"></i> Edit Review
                            </a>
                            <form action="delete_review.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                <input type="hidden" name="album_id" value="<?= $album_id ?>">
                                <button type="submit" class="btn btn-outline-danger rounded-pill">
                                    <i class="fa-solid fa-trash me-2"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow text-center p-5 h-100">
                    <div class="my-5">
                        <i class="fa-solid fa-pen-to-square fa-4x mb-4 text-accent"></i>
                        <h4 class="fw-bold mb-3">No Review Yet</h4>
                        <p class="text-muted mb-4">Share your thoughts about this album by writing a review.</p>
                        <a href="add_album_review.php?album_id=<?= $album_id ?>"
                            class="btn btn-primary btn-lg rounded-pill px-5 btn-hover">
                            <i class="fa-solid fa-plus me-2"></i> Add Your Review
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>