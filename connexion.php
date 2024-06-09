<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CESI tes notes</title>
    <link rel="stylesheet" href="style/styleConnexion.css" />
</head>
<body>
    <!-- Le Titre-->
    <div class="titreConnexion">
        <h1>Bienvenue sur CESI tes notes !</h1>
        <p>Veuillez rentrer vos identifiants.</p>
    </div>

    <!-- Formulaire pour vérifier les identifiants -->
    <form class="connexion" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="text" name="username" id="pseudo" placeholder="Pseudo" required />
        <input type="password" name="password" id="password" placeholder="Mot de passe" required />
        <input type="submit" name="connex" id="connex" value="Se connecter" />
    </form>
    </br>
    <div class="pasDeCompte">
        Pas de compte ? cliquez<a href="inscription.php"> ici</a>
    </div>

    <!-- Code PHP pour vérifier les identifiants -->
    <?php
        // Commence la session
        session_start();

        // Informations de connexion à la base de données MySQL
        $servername = "localhost"; 
        $dbUsername = "root";      
        $dbPassword = "";          
        $dbname = "projetweb";    

        // Crée une nouvelle connexion
        $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

        // Vérifie la connexion
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Vérifie si la requête est de type POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupère les valeurs envoyées par le formulaire
            $inputUsername = $_POST['username'];
            $inputPassword = $_POST['password'];
        
            // Prépare et exécute la requête SQL pour récupérer le mot de passe haché de l'utilisateur et son id
            $stmt = $conn->prepare("SELECT idUsers, mdp FROM users WHERE username = ?");
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("s", $inputUsername);
            $stmt->execute();
            $stmt->store_result();
        
            // Vérifie si l'utilisateur existe
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($userId, $hashedPassword);
                $stmt->fetch();
            
                // Vérifie si le mot de passe saisi correspond au mot de passe haché stocké dans la base de données
                if (password_verify($inputPassword, $hashedPassword)) {
                    // Crée une session pour l'utilisateur
                    $_SESSION['idUsers'] = $userId;
                    $_SESSION['username'] = $inputUsername;

                    // Redirige vers accueil.php
                    header("Location: accueil.php");
                    exit();
                } else {
                    echo "Nom d'utilisateur ou mot de passe incorrect.";
                }
            } else {
                echo "Nom d'utilisateur ou mot de passe incorrect.";
            }
        
            // Ferme la requête préparée
            $stmt->close();
        }

        // Ferme la connexion à la base de données
        $conn->close();
    ?>
</body>
</html>