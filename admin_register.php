<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'blog');
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Vérifiez si un compte administrateur existe déjà
$sql = "SELECT * FROM admin WHERE is_admin_created = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Redirige l'utilisateur à la connexion si un compte administrateur existe déjà
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $pin = $_POST['pin'];

    // Insérer le premier compte administrateur et marquer is_admin_created à 1
    $sql = "INSERT INTO admin (username, password, pin, is_admin_created) VALUES (?, ?, ?, 1)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $username, $password, $pin);
        if ($stmt->execute()) {
            echo "Compte administrateur créé avec succès!";
            header("Location: admin_login.php");
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
    <title>Inscription Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
        .container { margin-top: 100px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Inscription Administrateur</h2>
                <form action="admin_register.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Pseudo</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="pin" class="form-label">PIN de sécurité</label>
                        <input type="number" class="form-control" id="pin" name="pin" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Créer le compte</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

