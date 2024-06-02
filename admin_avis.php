<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'blog');
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_review'])) {
        $review_id = $_POST['review_id'];
        $sql = "DELETE FROM reviews WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $review_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (isset($_POST['toggle_review_visibility'])) {
        $review_id = $_POST['review_id'];
        $is_private = $_POST['is_private'];
        $sql = "UPDATE reviews SET is_private = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $is_private, $review_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (isset($_POST['respond_review'])) {
        $review_id = $_POST['review_id'];
        $response = $_POST['response'];
        $sql = "UPDATE reviews SET response = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $response, $review_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$reviews_sql = "SELECT * FROM reviews";
$reviews_stmt = $conn->prepare($reviews_sql);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
$reviews_stmt->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Avis</title>
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
            <a href="admin_dashboard.php" class="btn btn-secondary">Retour au Dashboard</a>
            <h1 class="h2">Gérer les Avis</h1>
        </header>

        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Article ID</th>
                    <th>Utilisateur</th>
                    <th>Évaluation</th>
                    <th>Commentaire</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($review = $reviews_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $review['id'] . "</td>";
                    echo "<td>" . $review['article_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($review['username']) . "</td>";
                    echo "<td>" . $review['rating'] . "</td>";
                    echo "<td>" . htmlspecialchars($review['comment']) . "</td>";
                    echo "<td>
                            <form action='admin_avis.php' method='post' style='display:inline;'>
                                <input type='hidden' name='review_id' value='" . $review['id'] . "'>
                                <button type='submit' name='delete_review' class='btn btn-danger btn-sm'>Supprimer</button>
                            </form>
                            <form action='admin_avis.php' method='post' style='display:inline;'>
                                <input type='hidden' name='review_id' value='" . $review['id'] . "'>
                                <input type='hidden' name='is_private' value='" . ($review['is_private'] ? 0 : 1) . "'>
                                <button type='submit' name='toggle_review_visibility' class='btn btn-warning btn-sm'>" . ($review['is_private'] ? 'Rendre Public' : 'Rendre Privé') . "</button>
                            </form>
                            <button class='btn btn-primary btn-sm' onclick='respondReview(" . $review['id'] . ")'>Répondre</button>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de réponse -->
    <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalLabel">Répondre à l'avis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="responseForm" action="admin_avis.php" method="post">
                        <input type="hidden" name="review_id" id="reviewIdResponse">
                        <div class="form-group">
                            <label for="response">Réponse</label>
                            <textarea class="form-control" id="response" name="response" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="respond_review" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function respondReview(reviewId) {
            document.getElementById('reviewIdResponse').value = reviewId;
            new bootstrap.Modal(document.getElementById('responseModal')).show();
        }
    </script>

    <div class="footer">
        <p>© 2023 Votre Site. Tous droits réservés.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

