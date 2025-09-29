<?php
session_start();
include 'includes/db.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene y sanitiza los datos del formulario
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validación de errores
    $error = '';
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Por favor, completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verifica si el usuario o correo ya existen en la base de datos
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = "El nombre de usuario o correo ya están en uso.";
        } else {
            // Inserta al nuevo usuario
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $_SESSION['user'] = $pdo->lastInsertId();
                header("Location: index.php");
                exit;
            } else {
                $error = "Error al registrar el usuario. Inténtalo nuevamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Gestión de Pantallas</title>
    <link rel="stylesheet" href="css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-plus login-icon"></i>
                <h1 class="login-title">Crear Cuenta</h1>
                <p class="login-subtitle">Regístrate para acceder al sistema</p>
            </div>
            
            <form action="register.php" method="POST" class="login-form">
                <!-- Mensaje de error -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <i class="fas fa-user form-icon"></i>
                    <input type="text" name="username" placeholder="Usuario" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-envelope form-icon"></i>
                    <input type="email" name="email" placeholder="Correo electrónico" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock form-icon"></i>
                    <input type="password" name="password" placeholder="Contraseña" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock form-icon"></i>
                    <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" class="form-input" required>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-user-plus"></i>
                    Crear Cuenta
                </button>
            </form>
            
            <div class="login-footer">
                <p>¿Ya tienes una cuenta? <a href="login.php" class="register-link">Inicia sesión</a></p>
            </div>
        </div>
    </div>
</body>
</html>
