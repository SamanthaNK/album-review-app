<?php include('../includes/header.php'); ?>

<?php require_once('../config/database.php');

// Fetch genres for the dropdown
$genreQuery = "SELECT id, name FROM genres ORDER BY name ASC";
$genreResult = $conn->query($genreQuery);

// Pre-fill form fields if data is passed in the URL query string
$title = isset($_GET['title']) ? $_GET['title'] : '';
$artist = isset($_GET['artist']) ? $_GET['artist'] : '';
$release_year = isset($_GET['release_year']) ? $_GET['release_year'] : '';
$language = isset($_GET['language']) ? $_GET['language'] : '';
$cover_image_url = isset($_GET['cover_image_url']) ? $_GET['cover_image_url'] : '';
$spotify_link = isset($_GET['spotify_link']) ? $_GET['spotify_link'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize the form data
    $title = trim(htmlspecialchars($_POST['title']));
    $artist = trim(htmlspecialchars($_POST['artist']));
    $release_year = (int) $_POST['release_year'];
    $language = trim(htmlspecialchars($_POST['language']));
    $cover_image_url = trim(htmlspecialchars($_POST['cover_image_url']));
    $spotify_link = !empty($_POST['spotify_link']) ? trim(htmlspecialchars($_POST['spotify_link'])) : null;
    $genres = isset($_POST['genres']) ? $_POST['genres'] : [];

    // Validate required fields
    if (empty($title)) {
        $errors[] = "Album title is required.";
    }

    if (empty($artist)) {
        $errors[] = "Artist name is required.";
    }

    if ($release_year < 1900 || $release_year > date('Y')) {
        $errors[] = "Please enter a valid release year between 1900 and " . date('Y') . ".";
    }

    if (empty($language)) {
        $errors[] = "Language is required.";
    }

    if (empty($cover_image_url)) {
        $errors[] = "Album cover image URL is required.";
    } elseif (!filter_var($cover_image_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Please enter a valid URL for the album cover.";
    }

    if (!empty($spotify_link) && !filter_var($spotify_link, FILTER_VALIDATE_URL)) {
        $errors[] = "Please enter a valid Spotify URL.";
    }

    if (empty($genres)) {
        $errors[] = "Please select at least one genre.";
    }

    // If no errors, proceed with database operations
    if (empty($errors)) {
        try {
            // Start transaction
            $conn->begin_transaction();

            // Insert into albums table
            $stmt = $conn->prepare("INSERT INTO albums (title, artist, release_year, language, cover_image_url, spotify_link) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisss", $title, $artist, $release_year, $language, $cover_image_url, $spotify_link);

            if ($stmt->execute()) {
                $album_id = $stmt->insert_id; // Get the last inserted album ID

                // Insert into album_genres pivot table
                $success = true;
                foreach ($genres as $genre_id) {
                    $insertGenre = $conn->prepare("INSERT INTO album_genres (album_id, genre_id) VALUES (?, ?)");
                    $insertGenre->bind_param("ii", $album_id, $genre_id);
                    if (!$insertGenre->execute()) {
                        $success = false;
                        break;
                    }
                }

                if ($success) {
                    // Commit transaction
                    $conn->commit();

                    // Check which button was clicked
                    if (isset($_POST['save_add_review'])) {
                        header("Location: add_album_review.php?album_id=$album_id");
                        exit();
                    } elseif (isset($_POST['save_view_album'])) {
                        header("Location: album_review.php?album_id=$album_id&status=album_added");
                        exit();
                    }
                } else {
                    // Rollback on failure
                    $conn->rollback();
                    $errors[] = "Error adding album genres. Please try again.";
                }
            } else {
                // Rollback on failure
                $conn->rollback();
                $errors[] = "Error adding album. Please try again.";
            }
        } catch (Exception $e) {
            // Rollback on any exception
            $conn->rollback();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Show popup alerts for form validation errors
if (!empty($errors)) {
    echo '<div class="alert alert-danger" role="alert"><ul class="mb-0">';
    foreach ($errors as $error) {
        echo '<li>' . $error . '</li>';
    }
    echo '</ul></div>';
}

// Show popup alerts for other statuses
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'error') {
        echo '<div class="alert alert-danger text-center" role="alert"><i class="fa-solid fa-exclamation" style="color: #a10035;"></i> Error adding album. Please try again.</div>';
    }
}

?>

<div class="page-header">
    <h1>Add a New Album</h1>
    <p class="subtitle">Share your musical discoveries</p>
</div>

<section class="py-4">
    <div class="container">
        <form method="POST" action="add_album.php" class="album-form needs-validation" novalidate id="albumForm">
            <div class="form-wrapper">
                <!-- Form Fields -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-music form-icon"></i>Album Title
                    </label>
                    <input type="text" name="title" class="form-control" placeholder="Enter album title" required
                        value="<?= $title; ?>">
                    <div class="invalid-feedback">Please enter an album title.</div>
                </div>

                <div class="form-group">
                    <label for="artist" class="form-label">
                        <i class="fa-solid fa-user form-icon"></i>Artist Name
                    </label>
                    <input type="text" id="artist" name="artist" class="form-control" placeholder="Enter artist name" required
                        value="<?= $artist; ?>">
                    <div class="invalid-feedback">Please enter an artist name.</div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label for="release_year" class="form-label">
                            <i class="fa-solid fa-calendar form-icon"></i>Release Year
                        </label>
                        <input type="number" id="release_year" name="release_year" class="form-control" placeholder="e.g. 2023" min="1900" max="<?= date("Y"); ?>" required
                            value="<?= $release_year; ?>">
                        <div class="invalid-feedback">Please enter a valid release year.</div>
                    </div>

                    <div class="form-group form-group-half">
                        <label for="language" class="form-label">
                            <i class="fa-solid fa-globe form-icon"></i>Language
                        </label>
                        <input type="text" id="language" name="language" class="form-control" placeholder="e.g. English" required
                            value="<?= $language; ?>">
                        <div class="invalid-feedback">Please enter the album language.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="genres" class="form-label">
                        <i class="fa-solid fa-tags form-icon"></i>Genres
                    </label>
                    <select name="genres[]" id="genres" class="form-select" multiple required>
                        <?php while ($row = $genreResult->fetch_assoc()) : ?>
                            <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="form-text"><i class="fa-solid fa-circle-info me-1"></i> Hold Ctrl (Windows) or Command (Mac) to select multiple genres.</div>
                    <div class="invalid-feedback">Please select at least one genre.</div>
                </div>

                <div class="form-group">
                    <label for="spotify_link" class="form-label">
                        <i class="fa-brands fa-spotify form-icon"></i>Spotify Link (optional)
                    </label>
                    <input type="url" id="spotify_link" name="spotify_link" class="form-control" placeholder="https://open.spotify.com/album/..."
                        value="<?= $spotify_link; ?>">
                    <div class="invalid-feedback">Please enter a valid URL.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-image form-icon"></i>Album Cover Image URL
                    </label>
                    <input type="url" name="cover_image_url" class="form-control" id="coverImageUrl" placeholder="Paste image URL" required value="<?= $cover_image_url; ?>">
                    <div class="invalid-feedback">Please enter a valid image URL.</div>

                    <div class="image-preview-container">
                        <img id="imagePreview" src="<?= $cover_image_url; ?>" alt="Album Cover Preview" class="img-fluid" style="<?= $cover_image_url ? 'display: block;' : 'display: none;' ?>" />

                        <div class="image-placeholder" style="<?= $cover_image_url ? 'display: none;' : 'display: flex;' ?>">
                            <div class="placeholder-content">
                                <i class="fa-solid fa-image placeholder-icon"></i>
                                <p>Preview will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="save_add_review" class="btn btn-outline-primary">
                        <i class="fa-solid fa-comment me-2"></i> Save & Add Review
                    </button>
                    <button type="submit" name="save_view_album" class="btn btn-primary">
                        <i class="fa-solid fa-headphones me-2"></i> Save & View Album
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include('../includes/footer.php'); ?>