<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'blog');
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$article_id = $_GET['id'];

// Récupérer les informations de l'article
$sql = "SELECT * FROM articles WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    $sql = "UPDATE articles SET title = ?, content = ?, category = ?, is_public = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssii", $title, $content, $category, $is_public, $article_id);
        if ($stmt->execute()) {
            echo "Article mis à jour avec succès!";
            header("Location: admin_edit_article.php");
            exit();
        } else {
            echo "Erreur: " . $conn->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
        .container { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between py-3 mb-4 border-bottom">
            <a href="admin_edit_article.php" class="btn btn-secondary">Retour</a>
            <h1 class="h2">Modifier l'Article</h1>
        </header>
        
        <form action="admin_edit.php?id=<?= htmlspecialchars($article['id']) ?>" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Titre</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Description</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?= htmlspecialchars($article['content']) ?></textarea>
                <small class="form-text text-muted">Utilisez les balises: php: 

```php ton code

```</small>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Catégorie</label>
                <input type="text" class="form-control" id="category" name="category" value="<?= htmlspecialchars($article['category']) ?>" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_public" name="is_public" <?= $article['is_public'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_public">Publier</label>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>