<?php
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['idUsers'])) {
    header("Location: login.php");
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

// Préparer et exécuter la requête SQL pour récupérer les notes
$sql = "SELECT idNotes, titre FROM notes WHERE idUsers = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();

// Traiter la suppression de la note via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idNote'])) {
    $idNote = $_POST['idNote'];

    // Préparer et exécuter la requête SQL de suppression
    $sqlDelete = "DELETE FROM notes WHERE idNotes = ? AND idUsers = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("ii", $idNote, $idUser);

    if ($stmtDelete->execute()) {
        echo "Note supprimée avec succès";
    } else {
        echo "Erreur lors de la suppression de la note";
    }

    $stmtDelete->close();
    exit();
}

// Fermer la connexion principale après la récupération des données
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Accueil</title>
    <link rel="stylesheet" href="style/styleAccueil.css?v=1.2" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        function redirectToCreationPage() {
            window.location.href = 'creationNotes.php';
        }

        function supprimerNote(idNote, element) {
            $.ajax({
                type: 'POST',
                url: 'accueil.php', // La même page traite la suppression
                data: { idNote: idNote },
                success: function(response) {
                    alert(response);
                    if (response.includes("succès")) {
                        // Remove the note from the DOM
                        $(element).closest('.pageItem').remove();
                    }
                },
                error: function() {
                    alert("Erreur lors de la suppression de la note");
                }
            });
        }
    </script>
</head>
<body>
    <!-- Titre -->
    <div class="titreAccueil">
        <p>Bienvenue dans votre espace! Vous pouvez y retrouver vos notes :</p>
    </div>
        <!-- Bouton pour créer une nouvelle page -->
    <div class="zoneActions">
        <button class="btnCreerPage" onclick="redirectToCreationPage()">Créer une nouvelle note</button>
    </div>

        <!-- Affiche les formulaires de l'utilisateur dans l'ordre de création -->
    <div class="zoneChoixFormulaire">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="pageItem">
                <span><?php echo htmlspecialchars($row['titre']); ?></span>
                <button class="btnAcceder" onclick="window.location.href='modifierNotes.php?id=<?php echo $row['idNotes']; ?>'">Accéder</button>
                <button class="btnSupprimer" onclick="supprimerNote(<?php echo $row['idNotes']; ?>, this)">Supprimer</button>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>
