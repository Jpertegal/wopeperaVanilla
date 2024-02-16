<?php

include 'dbCon.php';
session_start();

if (isset($_GET['code']) && isset($_GET['mail'])) {
    $activationCode = $_GET['code'];
    $email = $_GET['mail'];

    try {
        getDBConnection();

        $sql = "SELECT * FROM users WHERE mail = :email AND activationCode = :activationCode";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':activationCode', $activationCode);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // L'usuari ha estat trobat, actualitza la base de dades
            $updateSql = "UPDATE users SET active = 1, activationCode = null, activationDate = NOW() WHERE mail = :email";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':email', $email);
            $updateStmt->execute();

            // Informa que la verificació s'ha completat amb èxit
            $_SESSION['verification_status'] = true;
        } else {
            // Informa que la verificació ha fallat
            $_SESSION['verification_status'] = false;
        }
    } catch (PDOException $e) {
        echo "Error de la base de dades: " . $e->getMessage();
    } finally {
        $conn = null;
        header('Location: index.php');
        exit();
    }
} else {
    // Si no es reben els paràmetres esperats, redirigeix l'usuari a la pàgina principal amb un missatge d'error
    $_SESSION['verification_status'] = false;
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Activation</title>
</head>
<body>

<?php
session_start();

if (isset($_SESSION['verification_status'])) {
    if ($_SESSION['verification_status']) {
        echo '<h1>La teva web ha estat activada amb èxit!</h1>';
    } else {
        echo '<h1>Error: No s\'ha pogut activar la web.</h1>';
    }
    unset($_SESSION['verification_status']); // Elimina la variable de sessió
} else {
    echo '<h1>Benvingut a la teva web!</h1>';
}
?>

</body>
</html>