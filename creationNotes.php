
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une note</title>
    <link rel="stylesheet" href="style/styleCreationNotes.css?v=1.2">
</head>
<body>
    <div class="container">
        <h1>Créer une note</h1> <!-- Titre de la page -->
        <form action="creationNotes.php" method="post">
            <div class="formGroup">
                <!-- Titre de la note -->
                <label for="titre">Titre</label>
                <input type="text" class="input-title" name="titre" placeholder="Titre de la note..." required>
            </div>
            <div class="formGroup">
                <!-- Contenu de la note -->
                <label for="content">Contenu</label>
                <textarea class="textarea" name="content" placeholder="Écrivez votre note ici..." required></textarea>
            </div>
            <div class="buttons">
                <!-- Bouton pour engistrer -->
                <button class="btn-annuler" type="button" onclick="window.location.href='accueil.php'">Annuler</button>
                <button class="btn-enregistrer" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</body>

<?php
    
    // Démarrer la session
    session_start();
    
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['idUsers'])) {
        // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
        header("Location: connexion.php");
        exit();
    }
    
    // Récupérer l'ID de l'utilisateur depuis la session
    $idUsers = $_SESSION['idUsers'];
    
    // Vérifier que les données du formulaire sont définies
    if (!isset($_POST['titre']) || !isset($_POST['content'])) {
        die("Erreur: Données du formulaire manquantes.");
    }
    
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $content = $_POST['content'];
    
    // Détails de la connexion à la base de données
    $servername = "localhost";
    $username = "root"; // Remplacez par votre nom d'utilisateur MySQL
    $password = ""; // Remplacez par votre mot de passe MySQL
    $dbname = "projetweb";
    
    // Créer la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
    }
    
    // Préparer la requête SQL
    $sql = "INSERT INTO notes (titre, content, idUsers) VALUES (?, ?, ?)";
    
    // Utiliser une requête préparée pour éviter les injections SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $titre, $content, $idUsers);
    
    if ($stmt->execute()) {
        echo "Nouvelle note créée avec succès";
        // Rediriger vers une page de succès ou une autre page
        header("Location: accueil.php");
        exit();
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }
    
    // Fermer la connexion
    $stmt->close();
    $conn->close();
?>

</html>


