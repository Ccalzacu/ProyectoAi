<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';
include 'templates/header.php';

if (!isset($_GET['id'])) {
    echo "<div class='container'><p>Restaurante no especificado.</p></div>";
    include 'templates/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$sql = "SELECT b.*, p.id as photo_id FROM business b LEFT JOIN photo p ON b.id = p.business_id WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$restaurant = null;
$photos = [];

while ($row = $result->fetch_assoc()) {
    if (!$restaurant) {
        $restaurant = $row;
    }
    if (!empty($row['photo_id'])) {
        $photos[] = $row['photo_id'];
    }
}

if (!$restaurant) {
    echo "<div class='container'><p>Restaurante no encontrado.</p></div>";
    include 'templates/footer.php';
    exit;
}
?>

<div class="container">
    <div class="restaurant-detail">
        <div class="restaurant-header">
            <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
            <div class="stars">
                <?php echo $restaurant['stars']; ?> ★ 
                <span>(<?php echo $restaurant['review_count']; ?> opiniones)</span>
            </div>
        </div>

        <div class="restaurant-body">
            <div class="restaurant-gallery" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                <?php 
                if (empty($photos)) {
                    echo '<img src="assets/img/default_restaurant.png" alt="Default" style="max-width: 100%; border-radius: 8px;">';
                } else {
                    foreach ($photos as $photo_id) {
                        $img_path = "assets/img/phil-photos/phil/" . $photo_id . ".jpg";
                        if (file_exists($img_path)) {
                            echo '<img src="' . $img_path . '" alt="Foto restaurante" style="height: 200px; object-fit: cover; border-radius: 8px;">';
                        }
                    }
                }
                ?>
            </div>

            <div class="restaurant-info-detailed">
                <p><strong>Categorías:</strong> <?php echo htmlspecialchars($restaurant['categories']); ?></p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($restaurant['city']); ?>, <?php echo htmlspecialchars($restaurant['state']); ?></p>
                <p><strong>Estado:</strong> <?php echo $restaurant['is_open'] ? 'Abierto' : 'Cerrado'; ?></p>
                
                <!-- Aquí irían las reseñas si tuviéramos una tabla de reseñas -->
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="index.php" class="btn">Volver al listado</a>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
