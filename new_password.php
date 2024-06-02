<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_GET['username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '', 'blog');
    if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

    $sql = "UPDATE users SET password = ? WHERE username = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("ss", $new_password, $username);
        if ($stmt->execute()) {
            echo "Mot de passe mis à jour avec succès!";
            header("Location: login.php");
        } else {
            echo "Erreur: " . $conn->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Nouveau mot de passe</h2>
                <form action="new_password.php?username=<?= htmlspecialchars($_GET['username']) ?>" method="post">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

