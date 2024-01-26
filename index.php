<?php
session_start();
require_once './phpScripts/db/dbCon.php';
$error = '';
if (!isset($_SESSION['user'])) {
    setcookie(session_name(), '', time() - 3600, '/');
    session_destroy();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = isset($_POST['logEmail']) ? filter_input(INPUT_POST, 'logEmail', FILTER_SANITIZE_EMAIL) : '';
        $pass = isset($_POST['logPass']) ? filter_input(INPUT_POST, 'logPass', FILTER_SANITIZE_STRING) : '';
        if (!empty($email) && !empty($pass)) {
            $login = verificarUsuariBD($email, $pass);
            if ($login !== false) {
                session_start();
                $_SESSION['id'] = $login['idUsuari'];
                $_SESSION['user'] = $login['name'];
                header('Location: ./pages/home.php');
                exit();
            } else { $error = "Revisa l'adreça de correu i/o la contrasenya";}
        } else {
            $username = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
            $firstName = isset($_POST['firstname']) ? filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING) : '';
            $lastName = isset($_POST['lastname']) ? filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING) : '';
            $email = isset($_POST['email']) ? filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : '';
            $pass = isset($_POST['pass']) ? filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING) : '';
            $login = verificarUsuariBD($email, $pass);
            if ($login == false) {
                insertarUsuari($username, $firstName, $lastName, $email, $pass);
            } else {
                $error = "Usuari existent...";
            }
        }
    }
} else {
    header('Location: ./pages/home.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="./css/index/global.scss">
</head>
<body>
<img class="logo" src="./css/imgs/logo.png" alt="Imatge logo Wopepera" />
<div class="form-structor">
	<form class="signup" method="POST">
		<h2 class="form-title" id="signup">Sign up</h2>
		<div class="form-holder">
			<input name="username"type="text" class="input" placeholder="Username" required />
            <input name="firstname" type="text" class="input" placeholder="First Name" />
            <input name="lastname" type="text" class="input" placeholder="Last Name" />
			<input name="email" type="email" class="input" placeholder="Email" required />
			<input name="pass" type="password" class="input" placeholder="Password" required />
		</div>
		<button class="submit-btn">Sign up Warra</button>
	</form>
	<form class="login slide-up" method="POST">
		<div class="center">
			<h2 class="form-title" id="login">Log in</h2>
			<div class="form-holder">
				<input name="logEmail" type="text" class="input" placeholder="Username or Email" />
				<input name="logPass" type="password" class="input" placeholder="Password" />
			</div>
			<button class="submit-btn">Log in</button>
		</div>
	</form>
</div>
<script src="./js/index.js"></script>
</body>
</html>
