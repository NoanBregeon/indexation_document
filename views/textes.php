<?php
require_once '../models/config.php';
require_once '../models/BDD.php';

$db = new bdd();
$conn = $db->getConnection();

$query = "SELECT * FROM documents";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Textes</title>
    <link rel="stylesheet" href="../public/style.css?v=0">
</head>
<body>
    <?php include '../layouts/header.php'; ?>
    <h1>Liste des Textes</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Contenu</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Id_Documents']) ?></td>
                    <td><?= htmlspecialchars($row['Titre']) ?></td>
                    <td><?= htmlspecialchars($row['Texte']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php include '../layouts/footer.php'; ?>
</body>
</html>