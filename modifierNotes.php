<?php
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['idUsers'])) {
    header("Location: login.php");
    exit();
}

// Vérifier que l'id de la note est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: accueil.php");
    exit();
}

// Détails de connexion à la BDD
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projetweb";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer l'id de l'utilisateur de la session
$idUser = $_SESSION['idUsers'];
$idNote = $_GET['id'];

// Préparer et exécuter la requête SQL pour récupérer la note
$sql = "SELECT titre, content FROM notes WHERE idNotes = ? AND idUsers = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idNote, $idUser);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier que la note existe
if ($result->num_rows === 0) {
    header("Location: accueil.php");
    exit();
}

$note = $result->fetch_assoc();

// Traiter la mise à jour de la note
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $content = $_POST['content'];

    $sqlUpdate = "UPDATE notes SET titre = ?, content = ? WHERE idNotes = ? AND idUsers = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssii", $titre, $content, $idNote, $idUser);

    if ($stmtUpdate->execute()) {
        echo "La note a été mise à jour avec succès";
    } else {
        echo "Erreur lors de la mise à jour de la note";
    }

    $stmtUpdate->close();
}

// Fermer la connexion
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Modifier la note</title>
    <link rel="stylesheet" href="style/styleModifierNotes.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Titre de la page -->
        <h1>Modifier la note</h1>
        <form id="updateForm" method="POST">
            <div class="formGroup">
                <!-- Titre de la note -->
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($note['titre']); ?>" required />
            </div>
            <div class="formGroup">
                <!-- Contenu de la note -->
                <label for="content">Contenu</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($note['content']); ?></textarea>
            </div>
            <!-- Bouton retour qui renvoi vers la page d'accueil -->
            <button type="submit">Retour</button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $('#updateForm').submit(function(event) {
            event.preventDefault(); // Empêche le formulaire de se soumettre normalement
            
            // Récupérer les données du formulaire
            var formData = $(this).serialize();
            
            // Envoyer les données via AJAX
            $.ajax({
                type: 'POST',
                url: 'modifierNotes.php?id=<?php echo $idNote; ?>', // Envoyer l'ID de la note dans l'URL
                data: formData,
                success: function(response) {
                    // Gérer la réponse du serveur
                    var message = response.match(/La note a été mise à jour avec succès/i); // Recherche de la phrase dans la réponse
                    if (!message) {
                        alert("Une erreur s'est produite lors de la mise à jour de la note."); // Afficher une alerte en cas de réponse inattendue
                    } else {
                        // Rediriger vers la page d'accueil après mise à jour réussie
                        window.location.href = 'accueil.php';
                    }
                },
                error: function() {
                    alert("Une erreur s'est produite lors de la mise à jour de la note."); // Afficher une alerte en cas d'erreur
                }
            });
        });
    });
</script>  
    

</body>
</html>

