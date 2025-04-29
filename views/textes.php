<?php
require_once '../models/config.php';
require_once '../models/BDD.php';

$db = new bdd();
$conn = $db->getConnection();

// Gestion de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    
    // Supprimer d'abord les indexations liées au document
    $stmtDeleteIndexations = $conn->prepare("DELETE FROM indexation WHERE Id_Documents = ?");
    $stmtDeleteIndexations->bind_param("i", $deleteId);
    $stmtDeleteIndexations->execute();
    $stmtDeleteIndexations->close();
    
    // Ensuite supprimer le document
    $stmtDeleteDoc = $conn->prepare("DELETE FROM documents WHERE Id_Documents = ?");
    $stmtDeleteDoc->bind_param("i", $deleteId);
    
    if ($stmtDeleteDoc->execute()) {
        $message = "Document supprimé avec succès.";
        $messageClass = "success";
    } else {
        $message = "Erreur lors de la suppression du document.";
        $messageClass = "error";
    }
    $stmtDeleteDoc->close();
}

// Gestion de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['titre'], $_POST['texte'])) {
    $editId = intval($_POST['edit_id']);
    $titre = trim($_POST['titre']);
    $texte = trim($_POST['texte']);
    
    if (!empty($titre) && !empty($texte)) {
        $stmt = $conn->prepare("UPDATE documents SET Titre = ?, Texte = ? WHERE Id_Documents = ?");
        $stmt->bind_param("ssi", $titre, $texte, $editId);
        
        if ($stmt->execute()) {
            $message = "Document modifié avec succès.";
            $messageClass = "success";
        } else {
            $message = "Erreur lors de la modification du document.";
            $messageClass = "error";
        }
        $stmt->close();
    } else {
        $message = "Le titre et le contenu ne peuvent pas être vides.";
        $messageClass = "error";
    }
}

// Récupération du document à éditer
$editMode = false;
$editData = [];

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmtEdit = $conn->prepare("SELECT * FROM documents WHERE Id_Documents = ?");
    $stmtEdit->bind_param("i", $editId);
    $stmtEdit->execute();
    $resultEdit = $stmtEdit->get_result();
    
    if ($row = $resultEdit->fetch_assoc()) {
        $editMode = true;
        $editData = $row;
    }
    $stmtEdit->close();
}

// Récupération de tous les documents
$query = "SELECT * FROM documents ORDER BY Id_Documents DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Textes</title>
    <link rel="stylesheet" href="../public/style.css?v=1">
    <style>
        .success { color: green; }
        .error { color: red; }
        .actions { display: flex; gap: 10px; }
        .btn-edit { background-color: #3498db; }
        .btn-delete { background-color: #e74c3c; }
        .btn { 
            color: white; 
            border: none; 
            padding: 5px 10px; 
            cursor: pointer; 
            border-radius: 3px;
        }
        .text-content {
            max-height: 150px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        form textarea {
            width: 100%;
            min-height: 200px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include '../layouts/header.php'; ?>
    
    <div class="container">
        <h1><?= $editMode ? 'Modifier un document' : 'Liste des Textes' ?></h1>
        
        <?php if (isset($message)): ?>
            <p class="<?= $messageClass ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        
        <?php if ($editMode): ?>
            <!-- Formulaire de modification -->
            <form method="post" action="textes.php">
                <input type="hidden" name="edit_id" value="<?= $editData['Id_Documents'] ?>">
                
                <div class="form-group">
                    <label for="titre">Titre:</label>
                    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($editData['Titre']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="texte">Contenu:</label>
                    <textarea id="texte" name="texte" required><?= htmlspecialchars($editData['Texte']) ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-edit">Enregistrer</button>
                    <a href="textes.php" class="btn">Annuler</a>
                </div>
            </form>
        <?php else: ?>
            <!-- Tableau des textes -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Contenu</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['Id_Documents']) ?></td>
                                <td><?= htmlspecialchars($row['Titre']) ?></td>
                                <td>
                                    <div class="text-content">
                                        <?= nl2br(htmlspecialchars($row['Texte'])) ?>
                                    </div>
                                </td>
                                <td class="actions">
                                    <a href="textes.php?edit=<?= $row['Id_Documents'] ?>" class="btn btn-edit">Modifier</a>
                                    <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document?');">
                                        <input type="hidden" name="delete_id" value="<?= $row['Id_Documents'] ?>">
                                        <button type="submit" class="btn btn-delete">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">Aucun document trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <?php include '../layouts/footer.php'; ?>
</body>
</html>