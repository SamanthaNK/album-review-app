<?php include('../includes/header.php'); ?>

<?php
require_once('../config/database.php');

// Fetch genres for the dropdown
$genreQuery = "SELECT id, name FROM genres ORDER BY name ASC";
$genreResult = $conn->query($genreQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $title = trim($_POST['title']);
    $artist = trim($_POST['artist']);
    $release_year = (int) $_POST['release_year'];
    $language = trim($_POST['language']);
    $cover_image_url = trim($_POST['cover_image_url']);
    $spotify_link = !empty($_POST['spotify_link']) ? trim($_POST['spotify_link']) : null;
    $genres = isset($_POST['genres']) ? $_POST['genres'] : [];


    // Validate required fields
    if ($title && $artist && $release_year && $language && $cover_image_url && !empty($genres)) {
        // Insert into albums table
        $stmt = $conn->prepare("INSERT INTO albums (title, artist, release_year, language, cover_image_url, spotify_link) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $title, $artist, $release_year, $language, $cover_image_url, $spotify_link);

        if ($stmt->execute()) {
            $album_id = $stmt->insert_id; // Get the last inserted album ID

            // Insert into album_genres pivot table
            foreach ($genres as $genre_id) {
                $insertGenre = $conn->prepare("INSERT INTO album_genres (album_id, genre_id) VALUES (?, ?)");
                $insertGenre->bind_param("ii", $album_id, $genre_id);
                $insertGenre->execute();
            }

            // Check which button was clicked
            if (isset($_POST['save_add_review'])) {
                header("Location: add_album_review.php?album_id=$album_id");
                exit();
            } elseif (isset($_POST['save_view_album'])) {
                header("Location: album_review.php?album_id=$album_id&status=album_added");
                exit();
            }
        } else {
            header("Location: add_album.php?status=error"); // Redirect back with error
            exit();
        }
    }
}

// Show popup alerts
if (isset($_GET['status']) && $_GET['status'] === 'error') {
    echo '<div class="alert alert-danger text-center" role="alert">❌ Error adding album. Please try again.</div>';
}
?>

<h2 class="text-center mb-4">Add a New Album</h2>

<form method="POST" action="add_album.php" class="mx-auto" style="max-width: 600px;">
    <div class="mb-3">
        <label class="form-label">Album Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
    </div>

    <div class="mb-3">
        <label for="artist" class="form-label">Artist Name</label>
        <input type="text" id="artist" name="artist" class="form-control" required
            value="<?= isset($_POST['artist']) ? htmlspecialchars($_POST['artist']) : '' ?>">
    </div>

    <div class="mb-3">
        <label for="release_year" class="form-label">Release Year</label>
        <input type="number" id="release_year" name="release_year" class="form-control" min="1900" max="<?= date("Y"); ?>" required
            value="<?= isset($_POST['release_year']) ? htmlspecialchars($_POST['release_year']) : '' ?>">
    </div>

    <div class="mb-3">
        <label for="language" class="form-label">Language</label>
        <input type="text" id="language" name="language" class="form-control" required
            value="<?= isset($_POST['language']) ? htmlspecialchars($_POST['language']) : '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Album Cover Image URL</label>
        <input type="url" name="cover_image_url" class="form-control" id="coverImageUrl" required>
        <div class="mt-3 text-center">
            <img id="imagePreview" src="" alt="Album Cover Preview" style="max-width: 100%; height: auto; display: none; border: 1px solid #ccc; border-radius: 10px;" />
        </div>
    </div>

    <div class="mb-3">
        <label for="spotify_link" class="form-label">Spotify Link (optional)</label>
        <input type="url" id="spotify_link" name="spotify_link" class="form-control"
            value="<?= isset($_POST['spotify_link']) ? htmlspecialchars($_POST['spotify_link']) : '' ?>">
    </div>

    <div class="mb-3">
        <label for="genres" class="form-label">Genres</label>
        <select name="genres[]" id="genres" class="form-select" multiple required>
            <?php while ($row = $genreResult->fetch_assoc()) : ?>
                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select>
        <div class="form-text">Hold Ctrl (Windows) or Command (Mac) to select multiple genres.</div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" name="save_add_review" class="btn btn-success">💬 Save and Add Review</button>
        <button type="submit" name="save_view_album" class="btn btn-primary">🎧 Save and View Album</button>
    </div>
</form>

<?php include('../includes/footer.php'); ?>