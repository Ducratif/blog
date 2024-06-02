<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $pin = $_POST['pin'];

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '', 'blog');
    if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

    $sql = "SELECT * FROM users WHERE username = ? AND pin = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("si", $username, $pin);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Rediriger vers la page nouveau mot de passe
            header("Location: new_password.php?username=" . $username);
        } else {
            echo "Pseudo ou PIN incorrect!";
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
    <title>Mot de passe oublié</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Mot de passe oublié</h2>
                <form action="reset_password.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Pseudo</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="pin" class="form-label">PIN</label>
                        <input type="number" class="form-control" id="pin" name="pin" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Valider</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

