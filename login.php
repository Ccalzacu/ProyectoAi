<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $password = $_POST['password'];

    if (empty($name) || empty($password)) {
        $error = "Por favor, ingrese usuario y contraseña.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM user WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Contraseña correcta, iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no encontrado.";
        }
        $stmt->close();
    }
}
?>

<?php include 'templates/header.php'; ?>

<div class="container">
    <h2>Iniciar Sesión</h2>
    
    <?php if ($error): ?>
        <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form action="login.php" method="post">
        <div style="margin-bottom: 15px;">
            <label for="name">Nombre de Usuario:</label><br>
            <input type="text" id="name" name="name" required style="width: 100%; padding: 8px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="password">Contraseña:</label><br>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 8px;">
        </div>
        
        <button type="submit" class="btn">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>.</p>
</div>

<?php include 'templates/footer.php'; ?>
