<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurantes Philadelphia</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><a href="index.php">Philly Eats</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="profile.php">Mi Perfil</a></li>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Iniciar Sesión</a></li>
                        <li><a href="register.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
