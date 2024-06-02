<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'blog');
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$username = $_SESSION['username'];
// Récupérer les informations de l'utilisateur
$sql = "SELECT * FROM users WHERE username = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Récupérer les articles likés par l'utilisateur
$liked_articles_sql = "SELECT a.id, a.title FROM articles a JOIN likes l ON a.id = l.article_id WHERE l.username = ?";
$liked_articles_stmt = $conn->prepare($liked_articles_sql);
$liked_articles_stmt->bind_param("s", $username);
$liked_articles_stmt->execute();
$liked_articles_result = $liked_articles_stmt->get_result();
$liked_articles_stmt->close();

// Récupérer les articles commentés par l'utilisateur
$commented_articles_sql = "SELECT DISTINCT a.id, a.title FROM articles a JOIN reviews r ON a.id = r.article_id WHERE r.username = ?";
$commented_articles_stmt = $conn->prepare($commented_articles_sql);
$commented_articles_stmt->bind_param("s", $username);
$commented_articles_stmt->execute();
$commented_articles_result = $commented_articles_stmt->get_result();
$commented_articles_stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between py-3 mb-4">
            <a href="/" class="text-white text-decoration-none">
                <h1>Blog Moderne</h1>
            </a>
            <div>
                <a href="logout.php" class="btn btn-secondary">Déconnexion</a>
            </div>
        </header>
        
        <div class="profile-header">
            <h2>Bienvenue, <?= htmlspecialchars($user['username']) ?>!</h2>
            <?php if ($user['avatar']): ?>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="img-thumbnail" style="width: 150px; height: 150px;">
            <?php else: ?>
                <p>Aucun avatar. <a href="upload_avatar.php">Ajouter un avatar</a></p>
            <?php endif; ?>
        </div>

        <div class="mt-3">
            <a href="settings.php" class="btn btn-primary">Paramètres</a>
        </div>

        <h3 class="mt-5">Articles Likés</h3>
        <ul class="list-group">
            <?php while($liked_article = $liked_articles_result->fetch_assoc()): ?>
                <li class="list-group-item bg-dark text-white">
                    <a href="article.php?id=<?= htmlspecialchars($liked_article['id']) ?>" class="text-white"><?= htmlspecialchars($liked_article['title']) ?></a>
                </li>
            <?php endwhile; ?>
        </ul>

        <h3 class="mt-5">Articles Commentés</h3>
        <ul class="list-group">
            <?php while($commented_article = $commented_articles_result->fetch_assoc()): ?>
                <li class="list-group-item bg-dark text-white">
                    <a href="article.php?id=<?= htmlspecialchars($commented_article['id']) ?>" class="text-white"><?= htmlspecialchars($commented_article['title']) ?></a>
                </li>
            <?php endwhile; ?>
        </ul>

    </div>
    <div class="footer mt-4">
        <p>© 2023 Votre Site. Tous droits réservés.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

