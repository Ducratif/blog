<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $pin = $_POST['pin'];

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '', 'blog');
    if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

    $sql = "INSERT INTO users (email, username, password, pin) VALUES (?, ?, ?, ?)";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("sssi", $email, $username, $password, $pin);
        if ($stmt->execute()) {
            echo "Inscription réussie!";
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
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Inscription</h2>
                <form action="register.php" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Pseudo</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="pin" class="form-label">PIN</label>
                        <input type="number" class="form-control" id="pin" name="pin" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Inscription</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

