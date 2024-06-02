<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'blog');
if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $user_id = $_POST['user_id'];

    if ($action == 'ban') {
        $sql = "UPDATE users SET is_banned = 1 WHERE id = ?";
    } elseif ($action == 'unban') {
        $sql = "UPDATE users SET is_banned = 0 WHERE id = ?";
    } elseif ($action == 'change_password') {
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
    } elseif ($action == 'change_email') {
        $new_email = $_POST['new_email'];
        $sql = "UPDATE users SET email = ? WHERE id = ?";
    } elseif ($action == 'change_pin') {
        $new_pin = $_POST['new_pin'];
        $sql = "UPDATE users SET pin = ? WHERE id = ?";
    }

    if ($stmt = $conn->prepare($sql)) {
        if ($action == 'change_password') {
            $stmt->bind_param("si", $new_password, $user_id);
        } elseif ($action == 'change_email') {
            $stmt->bind_param("si", $new_email, $user_id);
        } elseif ($action == 'change_pin') {
            $stmt->bind_param("ii", $new_pin, $user_id);
        } else {
            $stmt->bind_param("i", $user_id);
        }
        if ($stmt->execute()) {
            echo "Action réussie!";
        } else {
            echo "Erreur: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des membres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2c3e50; color: #ecf0f1; }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between py-3 mb-4">
            <a href="/" class="text-white text-decoration-none">
                <h1>Gestion des membres</h1>
            </a>
            <a href="admin_dashboard.php" class="btn btn-secondary">Retour au Dashboard</a>
        </header>
        <h2>Liste des membres</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Pseudo</th>
                    <th>Email</th>
                    <th>PIN</th>
                    <th>Date d'inscription</th>
                    <th>Dernière connexion</th>
                    <th>Adresse IP</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id, username, email, pin, reg_date, last_seen, ip_address FROM users";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row["username"]."</td>";
                        echo "<td>".$row["email"]."</td>";
                        echo "<td>".$row["pin"]."</td>";
                        echo "<td>".$row["reg_date"]."</td>";
                        echo "<td>".$row["last_seen"]."</td>";
                        echo "<td>".$row["ip_address"]."</td>";
                        echo "<td>
                            <form action='admin_members.php' method='post' style='display:inline;'>
                                <input type='hidden' name='user_id' value='".$row["id"]."'>
                                <button type='submit' name='action' value='ban' class='btn btn-sm btn-danger'>Bannir</button>
                                <button type='submit' name='action' value='unban' class='btn btn-sm btn-success'>Débannir</button>
                            </form>
                            <button class='btn btn-sm btn-primary' onclick='changePassword(".$row["id"].")'>Changer Mot de Passe</button>
                            <button class='btn btn-sm btn-warning' onclick='changeEmail(".$row["id"].")'>Changer Email</button>
                            <button class='btn btn-sm btn-info' onclick='changePin(".$row["id"].")'>Changer PIN</button>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Aucun membre trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function changePassword(userId) {
            let newPassword = prompt("Entrez le nouveau mot de passe:");
            if (newPassword) {
                let form = document.createElement("form");
                form.action = "admin_members.php";
                form.method = "post";

                let inputUser = document.createElement("input");
                inputUser.type = "hidden";
                inputUser.name = "user_id";
                inputUser.value = userId;
                form.appendChild(inputUser);

                let inputAction = document.createElement("input");
                inputAction.type = "hidden";
                inputAction.name = "action";
                inputAction.value = "change_password";
                form.appendChild(inputAction);

                let inputPassword = document.createElement("input");
                inputPassword.type = "hidden";
                inputPassword.name = "new_password";
                inputPassword.value = newPassword;
                form.appendChild(inputPassword);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function changeEmail(userId) {
            let newEmail = prompt("Entrez le nouvel email:");
            if (newEmail) {
                let form = document.createElement("form");
                form.action = "admin_members.php";
                form.method = "post";

                let inputUser = document.createElement("input");
                inputUser.type = "hidden";
                inputUser.name = "user_id";
                inputUser.value = userId;
                form.appendChild(inputUser);

                let inputAction = document.createElement("input");
                inputAction.type = "hidden";
                inputAction.name = "action";
                inputAction.value = "change_email";
                form.appendChild(inputAction);

                let inputEmail = document.createElement("input");
                inputEmail.type = "hidden";
                inputEmail.name = "new_email";
                inputEmail.value = newEmail;
                form.appendChild(inputEmail);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function changePin(userId) {
            let newPin = prompt("Entrez le nouveau PIN:");
            if (newPin) {
                let form = document.createElement("form");
                form.action = "admin_members.php";
                form.method = "post";

                let inputUser = document.createElement("input");
                inputUser.type = "hidden";
                inputUser.name = "user_id";
                inputUser.value = userId;
                form.appendChild(inputUser);

                let inputAction = document.createElement("input");
                inputAction.type = "hidden";
                inputAction.name = "action";
                inputAction.value = "change_pin";
                form.appendChild(inputAction);

                let inputPin = document.createElement("input");
                inputPin.type = "hidden";
                inputPin.name = "new_pin";
                inputPin.value = newPin;
                form.appendChild(inputPin);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

