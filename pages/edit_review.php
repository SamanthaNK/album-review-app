<?php include('../includes/header.php'); ?>
<?php require_once('../config/database.php');

if (!isset($_GET['album_id'])) {
    die("Album ID is required.");
}

$album_id = (int) $_GET['album_id'];

// Fetch album details for display
$albumQuery = $conn->prepare("SELECT title, artist, cover_image_url FROM albums WHERE id = ?");
$albumQuery->bind_param("i", $album_id);
$albumQuery->execute();
$albumResult = $albumQuery->get_result();

if ($albumResult->num_rows === 0) {
    die("Album not found.");
}

$album = $albumResult->fetch_assoc();

// Fetch existing review
$reviewQuery = $conn->prepare("SELECT date_listened, favorite_songs, review_notes, rating FROM reviews WHERE album_id = ?");
$reviewQuery->bind_param("i", $album_id);
$reviewQuery->execute();
$reviewResult = $reviewQuery->get_result();

if ($reviewResult->num_rows === 0) {
    die("Review not found.");
}

$review = $reviewResult->fetch_assoc();

// handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date_listened = $_POST['date_listened'];
    $favorite_songs = trim($_POST['favorite_songs']);
    $review_notes = trim($_POST['review_notes']);
    $rating = (int) $_POST['rating'];

    // Simple validation
    if ($rating < 1 || $rating > 10) {
        $error = "Rating must be between 1 and 10.";
    } else {
        $stmt = $conn->prepare("UPDATE reviews SET date_listened = ?, favorite_songs = ?, review_notes = ?, rating = ? WHERE album_id = ?");
        $stmt->bind_param("sssii", $date_listened, $favorite_songs, $review_notes, $rating, $album_id);

        if ($stmt->execute()) {
            header("Location: album_review.php?album_id=$album_id&status=updated");
            exit();
        } else {
            $error = "Error updating review: " . $stmt->error;
        }
    }
}
?>

<div class="page-header">
    <h2><?= htmlspecialchars($album['title']) ?></h2>
    <p class="subtitle">by <?= htmlspecialchars($album['artist']) ?></p>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger text-center mb-4">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        <?= htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<section class="py-4">
    <div class="container">
        <div class="review-content">
            <!-- Album Cover and Info -->
            <div class="album-sidebar">
                <div class="album-card">
                    <!-- Album Cover -->
                    <div class="album-cover">
                        <img src="<?= htmlspecialchars($album['cover_image_url']) ?>" alt="<?= htmlspecialchars($album['title']) ?> album cover">
                    </div>

                    <!-- Album Info -->
                    <div class="album-meta">
                        <div class="album-details">
                            <div class="rating-icon">
                                <i class="fa-solid fa-star-half-stroke"></i>
                            </div>
                            <div class="album-text">
                                <h4><?= htmlspecialchars($album['title']) ?></h4>
                                <p><?= htmlspecialchars($album['artist']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            <div class="review-form-container">
                <form method="POST" action="edit_review.php?album_id=<?= $album_id ?>" class="needs-validation" novalidate>
                    <div class="form-wrapper">
                        <!-- Rating Selection with Visual Stars -->
                        <div class="form-group">
                            <label class="form-label">Your Rating</label>
                            <div class="rating-selector">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <input type="radio" class="btn-check" name="rating" id="rating<?= $i ?>" value="<?= $i ?>" <?= ($i == $review['rating']) ? 'checked' : '' ?> required>
                                    <label class="rating-btn" for="rating<?= $i ?>">
                                        <?= $i ?>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <div class="form-text"><i class="fa-solid fa-circle-info me-1"></i> Click to select your rating (1-10)</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-calendar-check form-icon"></i>Date Listened
                            </label>
                            <input type="date" name="date_listened" class="form-control" required value="<?= htmlspecialchars($review['date_listened']) ?>">
                            <div class="invalid-feedback">Please select the date you listened to this album.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-heart form-icon"></i>Favorite Songs
                            </label>
                            <textarea name="favorite_songs" class="form-control" rows="3"
                                placeholder="e.g. Track 3 - 'Song Title', Track 5 - 'Another Great Song'..." required><?= htmlspecialchars($review['favorite_songs']) ?></textarea>
                            <div class="invalid-feedback">Please share at least one favorite song.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-comment-dots form-icon"></i>Your Review
                            </label>
                            <textarea name="review_notes" class="form-control" rows="6"
                                placeholder="Share your thoughts about this album. What did you like? What emotions did it evoke? How does it compare to the artist's other work?" required><?= htmlspecialchars($review['review_notes']) ?></textarea>
                            <div class="invalid-feedback">Please write your review.</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary submit-btn">
                                <i class="fa-solid fa-save me-2"></i> Update Your Review
                            </button>
                            <a href="album_review.php?album_id=<?= $album_id ?>" class="btn btn-outline-dark">
                                <i class="fa-solid fa-arrow-left me-2"></i> Back to Review
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>