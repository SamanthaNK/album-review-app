<?php include('../includes/header.php'); ?>
<?php
require_once('../config/database.php'); 

//get album ID from URL
if(! isset($_GET['album_id'])) {
    die("Album ID is required.");
}

$album_id = (int) $_GET['album_id'];

// handle form submission
if($_SERVER["REQUEST_METHOD"] === "POST") {
    $date_listened = $_POST['date_listened'];
    $favorite_songs = trim($_POST['favorite_songs']);
    $review_notes = trim($_POST['review_notes']);
    $rating = (int) $_POST['rating'];

    // Simple validation
    if ($rating < 1 || $rating > 10) {
        $error = "Rating must be between 1 and 10.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (album_id, date_listened, favorite_songs, review_notes, rating) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $album_id, $date_listened, $favorite_songs, $review_notes, $rating);

        if ($stmt->execute()) {
            header("Location: album_review.php?album_id=$album_id&status=success");
            exit();
        } else {
            $error = "Error saving review: " . $stmt->error;
        }
    }
}
?>

<h2 class="text-center mb-4">Add Your Review</h2>

<?php if (isset($error)): ?>
  <div class="alert alert-danger text-center"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="add_album_review.php?album_id=<?= $album_id ?>" class="mx-auto" style="max-width: 600px;">
  <div class="mb-3">
    <label class="form-label">Date Listened</label>
    <input type="date" name="date_listened" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Favorite Songs</label>
    <textarea name="favorite_songs" class="form-control" rows="3" placeholder="e.g. track 3, track 5..." required></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Review / Notes</label>
    <textarea name="review_notes" class="form-control" rows="5" placeholder="What did you think about this album?" required></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Rating (1 to 10)</label>
    <select name="rating" class="form-select" required>
      <?php for ($i = 1; $i <= 10; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?></option>
      <?php endfor; ?>
    </select>
  </div>

  <button type="submit" class="btn btn-primary w-100">💾 Save Review and View Album</button>
</form>

<?php include('../includes/footer.php'); ?>