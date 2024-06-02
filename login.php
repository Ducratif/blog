<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '', 'blog');
    if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

    $sql = "SELECT * FROM users WHERE username = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Démarrer une session et rediriger l'utilisateur
                session_start();
                $_SESSION['username'] = $username;
                header("Location: profile.php");
                exit();
            } else {
                echo "Mot de passe incorrect!";
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
    <title>Connexion</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Connexion</h2>
                <form action="login.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Pseudo</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Connexion</button>
                </form>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>© 2023 Votre Site. Tous droits réservés.</p>
    </div>
    <!-- FOOTER -->

</body>
</html>

