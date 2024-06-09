<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="style/styleInscription.css?v=1.2" rel="stylesheet" type="text/css" media="all" />
</head>

<body>
    <div class="center">
        <!-- Formulaire pour enregistrer les données de l'utilisateur -->
        <form class="inscription" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="formGroup">
                <input type="text" name="username" id="pseudo" placeholder="Pseudo" required /> <!-- Son nom -->
            </div>
            <div class="formGroup">
                <input type="password" name="password" id="password" placeholder="Mot de passe" required /> <!-- Son mot de passe -->
            </div>
            <input type="submit" name="inscript" id="inscript" value="Créer un compte" /> 
        </form> 
        <br/>
        <!-- Si il a déja un compte -->
        <div>
            Déja un compte ? Connectez vous <a href="connexion.php">ici</a>
        </div>
    </div>

    <?php
        // Vérifie si la requête est de type POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupère les données du formulaire
            $inputUsername = $_POST['username'];
            $inputPassword = $_POST['password'];
        
            // Informations de connexion à la base de données MySQL
            $servername = "localhost"; 
            $dbUsername = "root";      
            $dbPassword = "";         
            $dbname = "projetweb";    
        
            // Crée une nouvelle connexion à la base de données
            $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
        
            // Vérifie la connexion
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
        
            // Vérifier si le nom d'utilisateur est déjà utilisé
            $stmt_check = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt_check->bind_param("s", $inputUsername);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
        
            if ($result->num_rows > 0) {
                // Nom d'utilisateur déjà utilisé
                echo "Ce nom d'utilisateur est déjà utilisé.";
            } else {
                // Prépare et exécute la requête SQL pour insérer les données d'inscription dans la base de données
                $stmt_insert = $conn->prepare("INSERT INTO users (username, mdp) VALUES (?, ?)");
                if ($stmt_insert === false) {
                    die('Prepare failed: ' . htmlspecialchars($conn->error));
                }
            
                // Hache le mot de passe avant de l'insérer dans la base de données
                $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);
            
                // Lie les paramètres et exécute la requête
                $stmt_insert->bind_param("ss", $inputUsername, $hashedPassword);
                if ($stmt_insert->execute()) {
                    // Redirection vers la page de connexion
                    header("Location: connexion.php");
                    exit(); // Arrête l'exécution du script pour éviter toute autre sortie
                } else {
                    echo "Erreur lors de la création du compte : " . $stmt_insert->error;
                }
            
                // Ferme la requête préparée
                $stmt_insert->close();
            }
        
            // Ferme la connexion à la base de données
            $conn->close();
        }
    ?>

</body>
</html>
