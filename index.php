<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Blog Moderne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #1c2833;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
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
        <a href="index.php">Accueil</a>
        <a href="login.php">Connexion</a>
        <a href="register.php">Inscription</a>
        <a href="profile.php">Profil</a>
        <a href="admin_login.php">Admin Connexion</a>
        <a href="admin_dashboard.php">Admin Dashboard</a>
        <a href="admin_edit_article.php">Gérer les Articles</a>
        <a href="members.php">Membres</a>
    </div>
    <div class="content" style="margin-left: 210px; padding: 20px;">
    <!-- Structure du site -->
    <div class="container">
        <header class="d-flex justify-content-between py-3 mb-4">
            <a href="/" class="text-white text-decoration-none">
                <h1>Blog Moderne</h1>
            </a>
            <div>
                <a href="login.php" class="btn btn-primary">Connexion</a>
                <a href="register.php" class="btn btn-secondary">Inscription</a>
            </div>
        </header>
        <div class="row">
            <div class="col-lg-9">
                <!-- Bannière -->
                <div class="p-5 mb-4 bg-dark rounded-3">
                    <div class="container-fluid py-5">
                        <h1 class="display-5 fw-bold">Bienvenue sur notre blog!</h1>
                        <p class="col-md-8 fs-4">Explorez les articles les plus populaires ci-dessous.</p>
                    </div>
                </div>
                <!-- Articles Populaires -->
                <h2>Articles Populaires</h2>
                <div class="list-group">
                    <?php
                    // Connexion à la base de données
                    $conn = new mysqli('localhost', 'root', '', 'blog');
                    if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }
                    $sql = "SELECT id, title FROM articles WHERE is_public = 1 ORDER BY created_at DESC LIMIT 10";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<a href='article.php?id=".$row["id"]."' class='list-group-item list-group-item-action bg-dark text-white'>".$row["title"]."</a>";
                        }
                    } else {
                        echo "<p>Aucun article trouvé.</p>";
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
            <!-- Barre latérale -->
            <div class="col-lg-3">
                <h3>Catégories</h3>
                <ul class="list-group">
                    <?php
                    // Exemple de liste de catégories
                    $categories = ['Technologie', 'Science', 'Art', 'Culture', 'Politique'];
                    foreach ($categories as $category) {
                        echo "<li class='list-group-item bg-dark text-white'>$category</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    </div>

    <!-- FOOTER -->
<body>
    <div class="footer">
        <p>© 2023 Votre Site. Tous droits réservés.</p>
    </div>
    <!-- FOOTER -->

</body>
</html>