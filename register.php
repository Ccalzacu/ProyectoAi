<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($password) || empty($confirm_password)) {
        $error = "Por favor, complete todos los campos.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el usuario ya existe
        $stmt = $conn->prepare("SELECT id FROM user WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "El nombre de usuario ya está en uso.";
        } else {
            // Insertar nuevo usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $yelping_since = date('Y-m-d');
            $review_count = 0;
            $average_stars = 0;

            $insert_stmt = $conn->prepare("INSERT INTO user (name, password, yelping_since, review_count, average_stars) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssjd", $name, $hashed_password, $yelping_since, $review_count, $average_stars);

            if ($insert_stmt->execute()) {
                $success = "Registro exitoso. Ahora puedes <a href='login.php'>iniciar sesión</a>.";
            } else {
                $error = "Error al registrar el usuario: " . $conn->error;
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>

<?php include 'templates/header.php'; ?>

<div class="container">
    <h2>Registro de Usuario</h2>
    
    <?php if ($error): ?>
        <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div style="color: green; margin-bottom: 15px;"><?php echo $success; ?></div>
    <?php else: ?>
        <form action="register.php" method="post">
            <div style="margin-bottom: 15px;">
                <label for="name">Nombre de Usuario:</label><br>
                <input type="text" id="name" name="name" required style="width: 100%; padding: 8px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="password">Contraseña:</label><br>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="confirm_password">Confirmar Contraseña:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 8px;">
            </div>
            
            <button type="submit" class="btn">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>
