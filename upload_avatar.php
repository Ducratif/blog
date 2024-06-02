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
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = $_FILES['avatar']['name'];
        $file_size = $_FILES['avatar']['size'];
        $file_type = $_FILES['avatar']['type'];

        // Vérifier l'extension et le type du fichier
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (in_array($file_ext, $allowed_extensions) && ($file_type == 'image/jpeg' || $file_type == 'image/png' || $file_type == 'image/gif')) {
            // Déplacer le fichier téléchargé vers le répertoire des avatars
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $new_file_name = $username . '.' . $file_ext;
            $upload_file = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $upload_file)) {
                // Mettre à jour l'avatar dans la base de données
                $sql = "UPDATE users SET avatar = ? WHERE username = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ss", $upload_file, $username);
                    if ($stmt->execute()) {
                        $message = "Avatar mis à jour avec succès!";
                    } else {
                        $message = "Erreur lors de la mise à jour de l'avatar: " . $conn->error;
                    }
                    $stmt->close();
                }
            } else {
                $message = "Erreur lors du téléchargement du fichier.";
            }
        } else {
            $message = "Type de fichier non autorisé. Seules les images JPG, PNG et GIF sont autorisées.";
        }
    } else {
        $message = "Erreur lors du téléchargement du fichier.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Télécharger un Avatar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
        .container { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between py-3 mb-4 border-bottom">
            <a href="profile.php" class="btn btn-secondary">Retour au Profil</a>
            <h1 class="h2">Télécharger un Avatar</h1>
        </header>
        
        <?php if ($message): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="upload_avatar.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="avatar" class="form-label">Choisissez un fichier d'avatar (jpg, png, gif)</label>
                <input type="file" class="form-control" id="avatar" name="avatar" required>
            </div>
            <button type="submit" class="btn btn-primary">Télécharger</button>
        </form>
    </div>
    <div class="footer">
        <p>© 2023 Votre Site. Tous droits réservés.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

