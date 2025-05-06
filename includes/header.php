<!-- includes/header.php -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>New Music, New Me</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../assets/images/favicon.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Delius&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light px-4 sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="../index.php">
        <i class="fa-solid fa-headphones-simple fa-lg me-2" style="color: #EE6F6F;"></i> New Music, New Me
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="../index.php">
              <i class="fa-solid fa-house-chimney me-1"></i> Home
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../pages/all_albums.php">
              <i class="fa-solid fa-compact-disc me-1"></i> Albums
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../pages/recommendations.php">
              <i class="fa-solid fa-lightbulb me-1"></i> Discover
            </a>
          </li>
        </ul>

        <!-- User Profile Icon -->
        <div class="ms-3 d-flex align-items-center">
          <a href="https://open.spotify.com/user/b9erevsnq2obuh2p0jlpluu74?si=a8c1acf85fbb48c5"
            class="btn btn-sm rounded-pill btn-outline-primary" aria-label="User Profile">
            <i class="fa-brands fa-spotify me-1"></i> Connect
          </a>
        </div>
      </div>
    </div>
  </nav>

