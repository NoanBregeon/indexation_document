<?php
require_once '../models/config.php';
require_once '../models/BDD.php';

$db = new bdd();
$conn = $db->getConnection();

// Gestion de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM Indexation WHERE Id_Indexation = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $message = "Indexation supprimée avec succès.";
    } else {
        $message = "Erreur lors de la suppression.";
    }
    $stmt->close();
}

// Récupération des mots clés
$sqlMotsCles = "SELECT Mot_Cle, SUM(Nombre_Mot) AS Total FROM Indexation WHERE Type = 'mot_cle' GROUP BY Mot_Cle ORDER BY Total DESC";
$resultMotsCles = $conn->query($sqlMotsCles);

// Récupération des mots de liaison
$sqlMotsLiaison = "SELECT Id_Indexation, Mot_Cle, SUM(Nombre_Mot) AS Total FROM indexation WHERE Type = 'mot_liaison' GROUP BY Mot_Cle ORDER BY Total DESC";
$resultMotsLiaison = $conn->query($sqlMotsLiaison);

// Préparer les données pour Chart.js (Top 10 mots clés)
$labels = [];
$data = [];
$count = 0;
while (($row = $resultMotsCles->fetch_assoc()) && $count < 10) {
    $labels[] = $row['Mot_Cle'];
    $data[] = $row['Total'];
    $count++;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard EasyIndex</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <?php include '../layouts/header.php'; ?>
    <h1>Dashboard - Fréquence des Mots</h1>

    <?php if (isset($message)): ?>
        <p style="color: green; text-align: center;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h2>Mots Clés</h2>
    <table>
        <tr><th>Mot Clé</th><th>Apparitions</th><th>Action</th></tr>
        <?php
        $resultMotsCles->data_seek(0); // Revenir au début
        while ($row = $resultMotsCles->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['Mot_Cle']) . "</td>
                    <td>" . htmlspecialchars($row['Total']) . "</td>
                    <td class='actions'>
                        <form method='post' style='display:inline; margin: 0; padding: 0;'>
                            <input type='hidden' name='delete_id' value='" . htmlspecialchars($row['Mot_Cle']) . "'>
                            <button type='submit' class='btn btn-delete'>Supprimer</button>
                        </form>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <h2>Mots de Liaison</h2>
    <table>
        <tr><th>Mot de Liaison</th><th>Apparitions</th><th>Action</th></tr>
        <?php
        while ($row = $resultMotsLiaison->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['Mot_Cle']) . "</td>
                    <td>" . htmlspecialchars($row['Total']) . "</td>
                    <td class='actions'>
                        <form method='post' style='display:inline; margin: 0; padding: 0;'>
                            <input type='hidden' name='delete_id' value='" . htmlspecialchars($row['Id_Indexation']) . "'>
                            <button type='submit' class='btn btn-delete'>Supprimer</button>
                        </form>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <h2>Top 10 Mots Clés (Graphique)</h2>
    <canvas id="myChart" width="400" height="200"></canvas>
    <?php include '../layouts/footer.php'; ?>
    <script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Occurrences',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: 'rgba(52, 152, 219, 0.6)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    </script>
</body>
</html>
