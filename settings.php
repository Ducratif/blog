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
    if (isset($_POST['update_info'])) {
        $new_email = $_POST['email'];
        $new_username = $_POST['username'];

        $sql = "UPDATE users SET email = ?, username = ? WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $new_email, $new_username, $username);
            if ($stmt->execute()) {
                $_SESSION['username'] = $new_username;
                $username = $new_username;
                $message = "Informations mises à jour avec succès!";
            } else {
                $message = "Erreur lors de la mise à jour des informations: " . $conn->error;
            }
            $stmt->close();
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

        $sql = "SELECT password FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            $stmt->close();

            if (password_verify($current_password, $hashed_password)) {
                $sql = "UPDATE users SET password = ? WHERE username = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ss", $new_password, $username);
                    if ($stmt->execute()) {
                        $message = "Mot de passe mis à jour avec succès!";
                    } else {
                        $message = "Erreur lors de la mise à jour du mot de passe: " . $conn->error;
                    }
                    $stmt->close();
                }
            } else {
                $message = "Mot de passe actuel incorrect!";
            }
        }
    }
}

$sql = "SELECT * FROM users WHERE username = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paramètres</title>
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
            <h1 class="h2">Paramètres</h1>
        </header>
        
        <?php if ($message): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <h2>Modifier les informations personnelles</h2>
        <form action="settings.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Pseudo</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <button type="submit" name="update_info" class="btn btn-primary">Mettre à jour</button>
        </form>

        <h2 class="mt-5">Changer le mot de passe</h2>
        <form action="settings.php" method="post">
            <div class="mb-3">
                <label for="current_password" class="form-label">Mot de passe actuel</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">Changer le mot de passe</button>
        </form>
    </div>
    <div class="footer mt-4">
        <p>© 2023 Votre Site. Tous droits réservés.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

