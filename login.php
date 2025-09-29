<?php
session_start();
include 'includes/db.php';

// Si el usuario ya está autenticado, redirigir al dashboard
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Buscar el usuario en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verificar credenciales
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['id']; // Guardar el ID del usuario en la sesión
        header("Location: index.php");   // Redirigir al dashboard
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Gestión de Pantallas</title>
    <link rel="stylesheet" href="css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-desktop login-icon"></i>
                <h1 class="login-title">Sistema de Gestión de Pantallas</h1>
                <p class="login-subtitle">Inicia sesión para continuar</p>
            </div>
            
            <form action="login.php" method="POST" class="login-form">
                <div class="form-group">
                    <i class="fas fa-user form-icon"></i>
                    <input type="text" name="username" placeholder="Usuario" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock form-icon"></i>
                    <input type="password" name="password" placeholder="Contraseña" class="form-input" required>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>
            
            <!-- Mostrar error si existe -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="login-footer">
                <p>¿No tienes una cuenta? <a href="register.php" class="register-link">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>
