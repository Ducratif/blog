# blog
Instructions de configuration
Télécharger et installer XAMPP ou WAMP (ou tout autre serveur web prenant en charge PHP et MySQL).

Créer une base de données et les tables:

Connectez-vous à phpMyAdmin et exécutez les requêtes SQL suivantes pour créer la base de données et les tables:




1. Structure du projet:
index.php (Accueil)
login.php (Connexion)
register.php (Inscription)
reset_password.php (Mot de passe oublié)
new_password.php (Nouveau mot de passe)
profile.php (Profil)
admin_login.php (Connexion Admin)
admin_dashboard.php (Dashboard Admin)
members.php (Gestion des membres)

d'autre page on étais crée au fur et a mesure du développement du projet.

2. Base de données:
Créez une base de données MySQL blog et plusieurs tables comme suit:
(Voir fichier db.sql)
Le nom de la base de donnée est: blog


3. Fichiers PHP:
Pour modifier les informations de la base de donnée, suiver l'exemple suivant:

Dans le fichier index.php, a la ligne 38, changer les informations de la base de donnée par les votre.
Si vous trouvez pas les informations pour les autres fichier, chercher le code suivant:
```php
$conn = new mysqli('localhost', 'root', '', 'blog');
```


Placez les fichiers PHP fournis dans le répertoire htdocs (ou www selon la configuration de votre serveur web).

Accédez à votre application via le navigateur:

Ouvrez le navigateur et allez sur http://localhost ou http://localhost/nom_du_dossier pour visualiser et utiliser votre blog.

Avec toutes ces instructions, vous devriez avoir une application fonctionnelle pour votre blog avec les fonctionnalités demandées.
N'hésitez pas à ajuster le code selon vos besoins spécifiques.



Inscription Administrateur

Pour créer une page d'inscription pour l'administrateur qui permet de créer un compte administrateur une seule fois et qui empêche toute nouvelle inscription après la création du premier compte, vous pouvez suivre les étapes suivantes.

Ajouter un champ dans la table admin pour vérifier si un compte administrateur existe déjà.
Créer un formulaire d'inscription pour le compte administrateur.
Vérifier si un compte administrateur existe déjà lors de la soumission du formulaire.

Dans la base de donnée, j'ai ajouter ceci
```sql
ALTER TABLE admin ADD COLUMN is_admin_created TINYINT DEFAULT 0;
```

Cela va vérifier s'il y a déjà un compte administrateur ou non.


Explications :
Vérification de l'existence d'un compte administrateur :
```php
   $sql = "SELECT * FROM admin WHERE is_admin_created = 1";
   $result = $conn->query($sql);
   if ($result->num_rows > 0) {
       header("Location: admin_login.php");
       exit();
   }
```

Avant d'afficher le formulaire d'inscription, nous vérifions si un compte administrateur existe déjà. Si oui, nous redirigeons vers admin_login.php.
Création du compte administrateur :
```php
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
       $username = $_POST['username'];
       $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
       $pin = $_POST['pin'];

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
```

Lors de la soumission du formulaire, nous ajoutons l'administrateur à la base de données et mettons is_admin_created à 1.
Formulaire d'inscription :
```php
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
```

Ce formulaire permet à l'administrateur de créer un compte en entrant un pseudo, un mot de passe et un PIN de sécurité.
Avec ce code, vous pouvez créer un compte administrateur une seule fois et empêcher toute future tentative d'inscription pour un nouvel administrateur après la création du premier compte.


Option Administrateur d'article

admin_edit_article.php:
Affiche une liste de tous les articles créés.
Inclut des boutons pour modifier, supprimer et changer la visibilité de chaque article.
Utilise des modals pour les confirmations de suppression et de changement de visibilité.

admin_edit.php:
Affiche un formulaire pré-rempli pour modifier l'article sélectionné.
Inclut un champ pour le titre, le contenu, la catégorie, et une case à cocher pour la visibilité.
Soumet les modifications à la base de données et redirige ensuite vers la page de gestion des articles.


Ajouter votre Footer
Nous allons prendre l'exemple sur la page de connexion.
Donc la page nommé login.php

A la ligne 42, ou sinon chercher le code suivant:
```html
body { background-color: #2c3e50; color: #ecf0f1; }
```

Juste en dessous, ajouter le code suivant:
```html
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
```

Ensuite juste avant le code
```html
</body>
</html>
```

tout en bas de la page ajouter le code suivant:
```html
<!-- FOOTER -->
    <div class="footer">
        <p>© 2023 Votre Site. Tous droits réservés.</p>
    </div>
    <!-- FOOTER -->
```
