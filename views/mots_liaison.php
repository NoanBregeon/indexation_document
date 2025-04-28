<?php
require_once '../models/config.php';
require_once '../models/BDD.php';

$db = new bdd();
$conn = $db->getConnection();

// Gestion de l'ajout d'un mot-clé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mot'])) {
    $mot = trim($_POST['mot']);
    if (!empty($mot)) {
        $stmt = $conn->prepare("INSERT INTO mot_liaison (Mot_Exclu) VALUES (?)");
        $stmt->bind_param("s", $mot);
        if ($stmt->execute()) {
            $message = "Mot ajouté avec succès.";

            header("Location: mots_liaison.php?page=textes"); 
            exit();
        } else {
            $message = "Erreur lors de l'ajout du mot.";
        }
        $stmt->close();
    } else {
        $message = "Le champ du mot ne peut pas être vide.";
    }
}

// Récupération des mots-clés
$query = "SELECT * FROM mot_liaison";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Mots de Liaison</title>
    <link rel="stylesheet" href="../public/style.css?v=0">
</head>
<body>
    <?php include '../layouts/header.php'; ?>
    <h1>Liste des Mots de Liaison</h1>

    <!-- Affichage des messages -->
    <?php if (isset($message)): ?>
        <p style="color: green; text-align: center;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <form method="post" class="container">
        <label for="mot">Ajouter un nouveau mot de liaison :</label>
        <input type="text" id="mot" name="mot" placeholder="Entrez un mot" required>
        <input type="submit" value="Ajouter">
    </form>

    <!-- Tableau des mots-clés -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Mot</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Id_Mot_Liaison']) ?></td>
                    <td><?= htmlspecialchars($row['Mot_Exclu']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php include '../layouts/footer.php'; ?>
</body>
</html>