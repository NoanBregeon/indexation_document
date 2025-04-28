<?php
require_once 'models/config.php';
require_once 'models/BDD.php';
require_once 'controllers/Document.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fichier'])) {
    $fileTmpPath = $_FILES['fichier']['tmp_name'];
    $fileContent = file($fileTmpPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if (count($fileContent) > 1) {
        $titre = array_shift($fileContent);
        $texte = implode(' ', $fileContent);
        
        $db = new bdd();
        $doc = new Document($db->getConnection());
        $doc->ajouter($titre, $texte);
        $doc->indexerDernierDocument();

        header("Location: index.php?page=textes"); 
        exit();
    } else {
        echo "<p>Le fichier doit contenir au moins deux lignes.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EasyIndex - Upload</title>
    <link rel="stylesheet" href="public/style.css?v=0">
</head>
<body>
    <?php include 'layouts/header.php'; ?>
    <h1>EasyIndex - Ajouter un document</h1>
    <form method="post" enctype="multipart/form-data">
        <label>Choisir un fichier texte (.txt) :
            <input type="file" name="fichier" accept=".txt" required>
        </label><br><br>
        <input type="submit" value="Envoyer">
    </form>
    <?php include 'layouts/footer.php'; ?>
</body>
</html>
    