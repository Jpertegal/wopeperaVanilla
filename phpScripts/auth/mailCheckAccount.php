<?php

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
