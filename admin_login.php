<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $pin = $_POST['pin'] ?? null;

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '', 'blog');
    if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

    $sql = "SELECT * FROM admin WHERE username = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password']) && ($pin === null || (int)$pin === (int)$row['pin'])) {
                // Démarrer une session et rediriger l'administrateur
                session_start();
                $_SESSION['admin'] = $username;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Mot de passe ou PIN incorrect!";
            }
        } else {
            echo "Nom d'utilisateur incorrect!";
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
    <title>Connexion Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Connexion Administrateur</h2>
                <form action="admin_login.php" method="post">
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
                        <input type="number" class="form-control" id="pin" name="pin">
                    </div>
                    <button type="submit" class="btn btn-primary">Connexion</button>
                </form>
                <div class="mt-3">
       <a href="admin_register.php" class="btn btn-secondary">Créer un compte administrateur</a>
   </div>
            </div>
        </div>
    </div>
</body>
</html>

