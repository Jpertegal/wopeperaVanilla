<?php
function getDBConnection()
{
    $connString = 'mysql:host=localhost;port=3308;dbname=isitec';
    $user = 'root';
    $pass = '';
    $db = null;
    try {
        $db = new PDO($connString, $user, $pass, [PDO::ATTR_PERSISTENT => true]);
    } catch (PDOException $e) {
        echo "<p style=\"color:red;\">Error " . $e->getMessage() . "</p>";
    } finally {
        return $db;
    }
}

function verificarUsuariBD($email, $pass)
{
    $result = false;
    $conn = getDBConnection();
    $sql = "SELECT `idUser`, `passHash` FROM `users` WHERE `mail`=:email OR `username`=:user AND active != 1";
    try {
        $usuaris = $conn->prepare($sql);
        $usuaris->execute([':email' => $email, ':user' => $email]);
        if ($usuaris->rowCount() == 1) {
            $dadesUsuari = $usuaris->fetch(PDO::FETCH_ASSOC);
            if (password_verify($pass, $dadesUsuari['passHash'])) {
                $nom = explode('@', $email)[0];
                $result = ['idUser' => $dadesUsuari['idUser'], 'name' => $nom];
            }
        }
    } catch (PDOException $e) {
        echo "<p style=\"color:red;\">Error " . $e->getMessage() . "</p>";
    } finally {
        return $result;
    }
}


function insertarUsuari($username, $firstName, $lastName, $email, $pass)
{
    $result = false;
    $conn = getDBConnection();
    $sql = "INSERT INTO `users` (`username`, `userFirstName`, `userLastName`, `mail`, `passHash`, `creationDate`, `removeDate`, `lastSignIn`, `active`)
    VALUES (:username, :firstName, :lastName, :email, :pass, NOW(), null, null, 0)";

    try {
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        $fitxar = $conn->prepare($sql);
        $fitxar->execute([':username' => $username, ':firstName' => $firstName, ':lastName' => $lastName, ':email' => $email, ':pass' => $pass]);
        $result = $fitxar->rowCount() == 1;
    } catch (PDOException $e) {
        echo "<p style=\"color:red;\">Error " . $e->getMessage() . "</p>";
    } finally {
        return $result;
    }
}


function updateLogin($idUsuari)
{
    $result = false;
    $conn = getDBConnection();
    $sql = "UPDATE users SET lastSignIn = now() WHERE idUser = :id";
    try {
        $fitxar = $conn->prepare($sql);
        $fitxar->execute([':id' => $idUsuari]);
        $result = $fitxar->rowCount() == 1;
    } catch (PDOException $e) {
        echo "<p style=\"color:red;\">Error " . $e->getMessage() . "</p>";
    } finally {
        return $result;
    }
}
