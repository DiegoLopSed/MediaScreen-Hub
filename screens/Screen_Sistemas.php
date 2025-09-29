<?php
include '../includes/db.php';

// Obtener screen_id según domain
$stmt = $pdo->prepare("SELECT id, name FROM screens WHERE domain = ?");
$stmt->execute(['Screen_Sistemas']);
$screen = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$screen) {
    die('Pantalla no encontrada.');
}

// Obtener media asociados
$stmt = $pdo->prepare("SELECT * FROM media WHERE screen_id = ?");
$stmt->execute([$screen['id']]);
$mediaItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($screen['name']); ?> - Sistema de Gestión de Pantallas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .screen-wrapper {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        
        .screen-header {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            z-index: 100;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        .screen-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .screen-subtitle {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .media-content {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .no-content {
            color: white;
            text-align: center;
            font-size: 1.5rem;
            opacity: 0.7;
            background: rgba(0, 0, 0, 0.3);
            padding: 40px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        
        .fullscreen-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }
        
        .fullscreen-overlay img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            cursor: pointer;
        }
        
        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 2rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }
        
        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .hidden {
            display: none !important;
        }
        
        .error-message {
            color: #ff6b6b;
            text-align: center;
            font-size: 1.2rem;
            background: rgba(255, 107, 107, 0.1);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
    </style>
</head>
<body>
    <div class="screen-wrapper">
        <div class="screen-header">
            <div class="screen-title"><?php echo htmlspecialchars($screen['name']); ?></div>
            <div class="screen-subtitle">Sistema de Gestión de Pantallas</div>
        </div>
        
        <?php if (empty($mediaItems)): ?>
            <div class="no-content">
                <i style="font-size: 3rem; margin-bottom: 20px; display: block;">📺</i>
                No hay contenido disponible
            </div>
        <?php else: ?>
            <?php foreach ($mediaItems as $item): ?>
                <?php
                $path = $item['file_path'];
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    echo '<iframe src="' . htmlspecialchars($path) . '" class="media-content" allowfullscreen frameborder="0"></iframe>';
                } else {
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $localPath = "../" . $path;

                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        echo '<img src="' . htmlspecialchars($localPath) . '" class="media-content" alt="Imagen" onclick="openFullscreen(\'' . htmlspecialchars($localPath) . '\')" style="cursor: pointer;">';
                    } elseif (in_array(strtolower($ext), ['mp4', 'webm', 'ogg'])) {
                        echo '<video controls autoplay loop muted class="media-content">
                                <source src="' . htmlspecialchars($localPath) . '" type="video/' . htmlspecialchars($ext) . '">
                                Tu navegador no soporta videos HTML5.
                              </video>';
                    } else {
                        echo '<div class="error-message">
                                <i style="font-size: 2rem; margin-bottom: 10px; display: block;">⚠️</i>
                                Tipo de archivo no soportado
                              </div>';
                    }
                }
                ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="fullscreen-container" class="hidden"></div>

    <script>
        function openFullscreen(src) {
            const container = document.getElementById('fullscreen-container');
            container.className = 'fullscreen-overlay';
            container.innerHTML = `
                <button class="close-btn" onclick="closeFullscreen()">×</button>
                <img src="${src}" alt="Imagen ampliada" onclick="closeFullscreen()">
            `;
        }

        function closeFullscreen() {
            const container = document.getElementById('fullscreen-container');
            container.className = 'hidden';
            container.innerHTML = '';
        }

        // Cerrar con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") closeFullscreen();
        });
        
        // Cerrar haciendo clic fuera de la imagen
        document.getElementById('fullscreen-container').addEventListener('click', function(e) {
            if (e.target === this) closeFullscreen();
        });
    </script>
</body>
</html>