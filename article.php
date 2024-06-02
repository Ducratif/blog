<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'blog');
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations de l'article
$sql = "SELECT * FROM articles WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    $stmt->close();
}

// Ajouter un like
if(isset($_POST['like_article']) && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $like_sql = "INSERT INTO likes (article_id, username) VALUES (?, ?)";
    if ($like_stmt = $conn->prepare($like_sql)) {
        $like_stmt->bind_param("is", $article_id, $username);
        $like_stmt->execute();
        $like_stmt->close();
    }
}

// Ajouter un avis
if(isset($_POST['submit_review']) && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $rating = intval($_POST['rating']);
    $comment = $_POST['comment'];
    $review_sql = "INSERT INTO reviews (article_id, username, rating, comment) VALUES (?, ?, ?, ?)";
    if ($review_stmt = $conn->prepare($review_sql)) {
        $review_stmt->bind_param("isis", $article_id, $username, $rating, $comment);
        $review_stmt->execute();
        $review_stmt->close();
    }
}

// Compter les likes
$likes_sql = "SELECT COUNT(*) as likes_count FROM likes WHERE article_id = ?";
$likes_stmt = $conn->prepare($likes_sql);
$likes_stmt->bind_param("i", $article_id);
$likes_stmt->execute();
$likes_result = $likes_stmt->get_result();
$likes_count = $likes_result->fetch_assoc()['likes_count'];
$likes_stmt->close();

// Récupérer les avis
$reviews_sql = "SELECT * FROM reviews WHERE article_id = ?";
$reviews_stmt = $conn->prepare($reviews_sql);
$reviews_stmt->bind_param("i", $article_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
$reviews_stmt->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
        .footer {
            background-color: #1c2833;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between py-3 mb-4 border-bottom">
            <a href="index.php" class="btn btn-secondary">Retour</a>
            <h1><?= htmlspecialchars($article['title']) ?></h1>
        </header>

        <p><?= nl2br(htmlspecialchars($article['content'])) ?></p>

        <form action="article.php?id=<?= $article_id ?>" method="post">
            <?php if(isset($_SESSION['username'])): ?>
                <button type="submit" name="like_article" class="btn btn-primary">Like</button>
            <?php else: ?>
                <p><a href="login.php">Connectez-vous</a> pour liker cet article.</p>
            <?php endif; ?>
        </form>
        <p><?= $likes_count ?> likes</p>

        <hr>

        <h2>Ajouter un avis</h2>
        <?php if(isset($_SESSION['username'])): ?>
            <form action="article.php?id=<?= $article_id ?>" method="post">
                <div class="form-group">
                    <label for="rating">Évaluation</label>
                    <select class="form-control" id="rating" name="rating" required>
                        <option value="1">1 - Très mauvais</option>
                        <option value="2">2 - Mauvais</option>
                        <option value="3">3 - Moyenne</option>
                        <option value="4">4 - Bon</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Commentaire</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn btn-primary">Soumettre</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Connectez-vous</a> pour ajouter un avis.</p>
        <?php endif; ?>

        <hr>

        <h2>Avis</h2>
        <?php while($review = $reviews_result->fetch_assoc()): ?>
            <div class="review">
                <p><strong><?= htmlspecialchars($review['username']) ?></strong> a donné une note de <strong><?= $review['rating'] ?>/5</strong></p>
                <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                <!-- Afficher la réponse de l'admin s'il y en a une -->
                <?php if (!empty($review['response'])) : ?>
                    <div class="response bg-light text-dark p-2 mt-2">
                        <p><strong>Réponse de l'admin:</strong></p>
                        <p><?= nl2br(htmlspecialchars($review['response'])) ?></p>
                    </div>
                <?php endif; ?>
                <hr>
            </div>
        <?php endwhile; ?>
        
    </div>
    <div class="footer">
        <p>© 2023 Votre Site. Tous droits réservés.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

