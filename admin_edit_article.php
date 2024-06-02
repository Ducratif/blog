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

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['toggle_visibility'])) {
        $article_id = $_POST['article_id'];
        $is_public = $_POST['is_public'];
        $sql = "UPDATE articles SET is_public = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $is_public, $article_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['delete_article'])) {
        $article_id = $_POST['article_id'];
        $sql = "DELETE FROM articles WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $article_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
        .container { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between py-3 mb-4 border-bottom">
            <a href="admin_dashboard.php" class="btn btn-secondary">Retour au Dashboard</a>
            <h1 class="h2">Gérer les Articles</h1>
        </header>
        
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Date de Création</th>
                    <th>Dernière Modification</th>
                    <th>Visibilité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id, title, created_at, updated_at, is_public FROM articles";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "<td>" . $row['updated_at'] . "</td>";
                        echo "<td>" . ($row['is_public'] ? 'Public' : 'Privé') . "</td>";
                        echo "<td>
                                <button class='btn btn-warning btn-sm' onclick='toggleVisibility(" . $row['id'] . ", " . $row['is_public'] . ")'>Changer Visibilité</button>
                                <a href='admin_edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Modifier</a>
                                <button class='btn btn-danger btn-sm' onclick='deleteArticle(" . $row['id'] . ")'>Supprimer</button>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="visibilityModal" tabindex="-1" aria-labelledby="visibilityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="visibilityModalLabel">Changer la Visibilité de l'Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="visibilityForm" action="admin_edit_article.php" method="post">
                        <input type="hidden" name="article_id" id="articleIdVisibility">
                        <div class="d-grid gap-2">
                            <button type="submit" name="toggle_visibility" value="public" class="btn btn-success">Public</button>
                            <button type="submit" name="toggle_visibility" value="private" class="btn btn-secondary">Privé</button>
                        </div>
                        <input type="hidden" name="is_public" id="isPublic">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Supprimer l'Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Voulez-vous vraiment supprimer cet article?</p>
                    <p class="text-danger">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" action="admin_edit_article.php" method="post">
                        <input type="hidden" name="article_id" id="articleIdDelete">
                        <button type="submit" name="delete_article" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleVisibility(articleId, isPublic) {
            document.getElementById('articleIdVisibility').value = articleId;
            document.getElementById('isPublic').value = isPublic ? 0 : 1;
            new bootstrap.Modal(document.getElementById('visibilityModal')).show();
        }

        function deleteArticle(articleId) {
            document.getElementById('articleIdDelete').value = articleId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

