<?php
// Incluye el archivo de autenticación
include 'includes/auth.php';
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Pantallas</title>
    <link rel="stylesheet" href="css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Barra de Navegación -->
    <header class="header">
        <div class="container">
            <h1 class="header-title">Sistema de Gestión de Pantallas</h1>
            <nav class="header-nav">
                <ul class="nav-list">
                    <li><a href="index.php" class="nav-link">Inicio</a></li>
                    <li><a href="logout.php" class="nav-link">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-content">
        <div class="container">
            <section class="screens-section">
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success">
                        Pantalla eliminada correctamente.
                    </div>
                <?php endif; ?>

                <h2 class="section-title">Pantallas Disponibles</h2>
                <div class="screens-grid">
                <?php
                $stmt = $pdo->query("
                    SELECT s.*, m.file_path 
                    FROM screens s
                    LEFT JOIN (
                        SELECT screen_id, file_path
                        FROM media
                        GROUP BY screen_id
                    ) m ON s.id = m.screen_id
                    ORDER BY s.created_at DESC
                ");

                while ($row = $stmt->fetch()) {
                    $screenUrl = "screens/" . urlencode($row['domain']);
                    $file_path = $row['file_path'];

                    echo "<div class='screen-card'>";

                    // Mostrar multimedia
                    if (!empty($file_path)) {
                        if (filter_var($file_path, FILTER_VALIDATE_URL)) {
                            echo "<div class='screen-preview'>
                                    <iframe src='" . htmlspecialchars($file_path) . "' frameborder='0' allowfullscreen class='preview-iframe'></iframe>
                                  </div>";
                        } else {
                            echo "<img src='" . htmlspecialchars($file_path) . "' alt='Vista previa' class='preview-image'>";
                        }
                    } else {
                        echo "<div class='preview-placeholder'>Sin contenido</div>";
                    }

                    // Nombre y dominio
                    echo "<div class='screen-info'>";
                    echo "<h3 class='screen-name'>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p class='screen-domain'>" . htmlspecialchars($row['domain']) . "</p>";
                    echo "</div>";

                    // Botones
                    echo "<div class='screen-actions'>";
                    echo "<a href='$screenUrl.php' target='_blank' class='btn btn-primary'><i class='fas fa-eye'></i> Ver</a>";
                    echo "<a href='functions/screen_config.php?id=" . $row['id'] . "' class='btn btn-secondary'><i class='fas fa-edit'></i> Editar</a>";
                    echo "<button onclick=\"copyToClipboard('$screenUrl')\" class='btn btn-info'><i class='fas fa-link'></i> Copiar enlace</button>";
                    echo "<form action='functions/screen_delete.php' method='GET' onsubmit='return confirm(\"¿Estás seguro de eliminar esta pantalla?\");' class='delete-form'>";
                    echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='btn btn-danger'><i class='fas fa-trash'></i> Eliminar</button>";
                    echo "</form>";
                    echo "</div>";

                    echo "</div>";
                }
                ?>

                <!-- Tarjeta de agregar nueva pantalla -->
                <div class="screen-card add-card">
                    <a href="functions/add.php" class="add-link">
                        <i class="fas fa-plus add-icon"></i>
                        <p class="add-text">Agregar Pantalla</p>
                    </a>
                </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        function copyToClipboard(text) {
            const url = `${location.origin}/${text}.php`;
            navigator.clipboard.writeText(url).then(() => {
                alert("Enlace copiado: " + url);
            }).catch(err => {
                console.error('Error al copiar', err);
                alert("Error al copiar el enlace.");
            });
        }
    </script>

    <!-- Pie de Página -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> - Sistema de Gestión de Pantallas</p>
        </div>
    </footer>
</body>

</html>
