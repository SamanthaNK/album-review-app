<?php
require_once('../config/database.php');

// Check if album_id is set and is a valid integer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['album_id']) && is_numeric($_POST['album_id'])) {
    $album_id = (int) $_POST['album_id'];
    
    // Start a transaction
    $conn->begin_transaction();
    
    try {
        // First, check if the album exists
        $checkQuery = $conn->prepare("SELECT id FROM albums WHERE id = ?");
        $checkQuery->bind_param("i", $album_id);
        $checkQuery->execute();
        $result = $checkQuery->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Album not found");
        }
        
        // Delete the review
        $deleteReview = $conn->prepare("DELETE FROM reviews WHERE album_id = ?");
        $deleteReview->bind_param("i", $album_id);
        
        if (!$deleteReview->execute()) {
            throw new Exception("Failed to delete review: " . $deleteReview->error);
        }
        
        // Commit the transaction
        $conn->commit();
        
        // Redirect back to the album page or albums list
        header("Location: all_albums.php?status=deleted");
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        
        // Display error message
        echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
        echo '<a href="all_albums.php" class="btn btn-primary">Return to Albums</a>';
    }
} else {
    // Invalid request
    header("Location: all_albums.php?error=invalid_request");
    exit();
}
?>
