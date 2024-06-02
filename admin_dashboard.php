<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'blog');
if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_article'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    $sql = "INSERT INTO articles (title, content, category, is_public) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $title, $content, $category, $is_public);
        if ($stmt->execute()) {
            echo "Article créé avec succès!";
        } else {
            echo "Erreur: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0; 
            left: 0;
            width: 200px; 
            background-color: #1c2833; 
            padding-top: 50px;
        }
        .sidebar a {
            text-decoration: none;
            font-size: 20px;
            color: #ecf0f1;
            display: block;
            padding: 10px 20px;
        }
        .sidebar a:hover {
            background-color: #566573;
        }
    </style>
</head>
<body>
    <!-- Barre latéral -->
    <div class="sidebar">
    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="admin_avis.php">Admin Avis</a>
        <a href="admin_edit_article.php">Gérer les Articles</a>
        <a href="admin_members.php">Membres</a>
    </div>
    <div class="content" style="margin-left: 210px; padding: 20px;">
    <!-- Structure du site -->
    <div class="container">
        <header class="d-flex justify-content-between py-3 mb-4">
            <a href="/" class="text-white text-decoration-none">
                <h1>Dashboard Admin</h1>
            </a>
            <a href="logout.php" class="btn btn-secondary">Déconnexion</a>
        </header>

        <!-- Formulaire pour créer un article -->
        <h2>Créer un nouvel article</h2>
        <form action="admin_dashboard.php" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Titre</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Description</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                <small class="form-text text-muted">Utilisez les balises: php: 

```php ton code

```</small>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Catégorie</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_public" name="is_public">
                <label class="form-check-label" for="is_public">Publier</label>
            </div>
            <button type="submit" class="btn btn-primary" name="create_article">Créer</button>
        </form>

        <!-- Liste des derniers inscrits -->
        <h2>Les dix derniers inscrits</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Pseudo</th>
                    <th>Email</th>
                    <th>PIN</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT username, email, pin FROM users ORDER BY reg_date DESC LIMIT 10";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>".$row["username"]."</td><td>".$row["email"]."</td><td>".$row["pin"]."</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Pas d'utilisateurs inscrits récemment.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="admin_members.php" class="btn btn-secondary">Voir tous les membres</a>
    </div>
    </div>
</body>
</html>

