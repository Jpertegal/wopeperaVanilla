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

/*
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
*/
function insertarUsuari($username, $firstName, $lastName, $email, $pass)
{
    $result = false;
    $conn = getDBConnection();
    
    // Generar valor aleatori i hash
    $valorAleatorioYHash = generarValorAleatorioYHash();
    $valorAleatorio = $valorAleatorioYHash['valor_aleatorio'];
    $hashValorAleatorio = $valorAleatorioYHash['hash_valor_aleatorio'];
    
    $activationLink = "http://localhost/mailCheckAccount.php?code=$hashValorAleatorio&mail=$email";

    try {
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `users` (`username`, `userFirstName`, `userLastName`, `mail`, `passHash`, `creationDate`, `removeDate`, `lastSignIn`, `active`, `activationCode`, `activationLink`)
        VALUES (:username, :firstName, :lastName, :email, :pass, NOW(), null, null, 0, :activationCode, :activationLink)";
        
        $fitxar = $conn->prepare($sql);
        $fitxar->execute([':username' => $username, ':firstName' => $firstName, ':lastName' => $lastName, ':email' => $email, ':pass' => $pass, ':activationCode' => $hashValorAleatorio, ':activationLink' => $activationLink]);
        $result = $fitxar->rowCount() == 1;

        if ($result) {
            // Enviam el correu electrònic de benvinguda amb PHPMailer
            $subject = "Confirmació de registre a la plataforma Wopepera";
            $message = "
                <html>
                <head>
                    <title>Benvingut a Wopepera</title>
                </head>
                <body>
                    <p>Gràcies per registrar-te a la nostra plataforma. Us donem la benvinguda a bord.</p>
                    <p>Fes clic al següent enllaç per activar el teu compte:</p>
                    <p><a href=\"$activationLink\">Active your account now!</a></p>
                    <img src='/css/imgs/logo.png' alt='Imatge Corporativa Wopepera'>
                </body>
                </html>
            ";

            // Configuració de PHPMailer
            require 'PHPMailer/PHPMailer.php';
            require 'PHPMailer/Exception.php';
            require 'PHPMailer/SMTP.php';

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'el_teu_correu_electrònic';
            $mail->Password   = 'la_tua_contrassenya';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('el_teu_correu_electrònic', 'Wopepera Inc.');
            $mail->addAddress($email, $firstName . ' ' . $lastName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            echo 'Correu electrònic enviat correctament';
        }
    } catch (PDOException $e) {
        echo "<p style=\"color:red;\">Error en inserir l'usuari: " . $e->getMessage() . "</p>";
    } catch (Exception $e) {
        echo "Error en enviar el correu electrònic: {$e->getMessage()}";
    } finally {
        // Tanquem la connexió
        $conn = null;
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
