<?php include('../includes/header.php'); ?>
<?php require_once('../config/database.php'); ?>

<!-- Page Header Banner -->
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">My Album Collection</h2>
        <a href="add_album.php" class="btn btn-dark rounded-pill px-4">
            <i class="fa-solid fa-plus me-2"></i> Add New Album
        </a>
    </div>

    <?php
    // Optional success alert after deletion
    if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="alert alert-success text-center">
            <i class="fa-solid fa-circle-check me-2"></i> Review deleted successfully!
        </div>
    <?php endif; ?>

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
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fa-solid fa-compact-disc fa-4x text-secondary opacity-50"></i>
            </div>
            <h4 class="text-muted mb-4">Your collection is empty</h4>
            <a href="add_album.php" class="btn btn-primary rounded-pill px-4 py-2 btn-hover">
                <i class="fa fa-plus me-2"></i> Add Your First Album Review
            </a>
        </div>
    <?php else: ?>
        <div class="album-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            <?php while ($row = $result->fetch_assoc()):
                $rating = $row['rating'];
                $formattedDate = date('M j, Y', strtotime($row['date_listened']));
            ?>
                <div class="album-card">
                    <a href="album_review.php?album_id=<?= $row['id'] ?>" class="text-decoration-none">
                        <div class="album-cover" style="padding-bottom: 100%;">
                            <?php if (!empty($row['cover_image_url'])): ?>
                                <img src="<?= htmlspecialchars($row['cover_image_url']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                            <?php else: ?>
                                <div class="d-flex justify-content-center align-items-center h-100 bg-secondary">
                                    <i class="fa-solid fa-music fa-3x text-white"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="album-info">
                        <h5 class="fw-bold text-truncate mb-1"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="text-muted mb-2"><?= htmlspecialchars($row['artist']) ?></p>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-accent fw-bold">
                                <?= $rating ?><span class="text-muted fw-normal">/10</span>
                            </div>

                            <div class="album-actions">
                                <a href="edit_review.php?album_id=<?= $row['id'] ?>" class="text-primary" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="delete_review.php" method="POST" class="d-inline ms-2">
                                    <input type="hidden" name="album_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn p-0 text-danger border-0 bg-transparent" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="far fa-calendar-alt me-1"></i> Listened on <?= $formattedDate ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>