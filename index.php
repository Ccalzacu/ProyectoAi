<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';
include 'templates/header.php';

// Configuración de paginación
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

// 1. Calcular N (Total de restaurantes) y R (Promedio de estrellas global) para el Ranking Bayesiano
// Fórmula: (NR + n_i * r_i) / (N + n_i)
// N = Total de restaurantes
// R = Promedio global de estrellas
// n_i = review_count del restaurante
// r_i = stars del restaurante

// Obtener N y R
$stats_sql = "SELECT COUNT(*) as N, AVG(stars) as R FROM business";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
$N = $stats['N'];
$R = $stats['R'];

// 2. Consulta principal con Ranking Bayesiano y Búsqueda
$where_sql = "";
$search_param = "";
if ($search) {
    $where_sql = "WHERE b.name LIKE ?";
    $search_param = "%" . $search . "%";
}

// Contar total de resultados para paginación
$count_sql = "SELECT COUNT(*) as total FROM business b $where_sql";
$stmt_count = $conn->prepare($count_sql);
if ($search) {
    $stmt_count->bind_param("s", $search_param);
}
$stmt_count->execute();
$total_results = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_results / $limit);

$sql = "SELECT b.*, MIN(p.id) as photo_id,
        ((? * ?) + (b.review_count * b.stars)) / (? + b.review_count) as weighted_score
        FROM business b 
        LEFT JOIN photo p ON b.id = p.business_id
        $where_sql
        GROUP BY b.id
        ORDER BY weighted_score DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($search) {
    $stmt->bind_param("dddsii", $N, $R, $N, $search_param, $limit, $offset);
} else {
    $stmt->bind_param("dddii", $N, $R, $N, $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Restaurantes Destacados</h2>
        <?php if (isLoggedIn()): ?>
            <span>Hola, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
        <?php endif; ?>
    </div>

    <form action="index.php" method="GET" style="margin: 20px 0; display: flex; gap: 10px;">
        <input type="text" name="q" placeholder="Buscar restaurante..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; width: 300px; border: 1px solid #ddd; border-radius: 4px;">
        <button type="submit" class="btn" style="padding: 8px 15px; cursor: pointer;">Buscar</button>
        <?php if ($search): ?>
            <a href="index.php" class="btn" style="background-color: #666; text-decoration: none; padding: 8px 15px;">Limpiar</a>
        <?php endif; ?>
    </form>

    <?php if ($search): ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($search); ?></strong> (<?php echo $total_results; ?> encontrados)</p>
    <?php else: ?>
        <p>Ordenados por puntuación ponderada (Bayesian Ranking).</p>
    <?php endif; ?>

    <div class="restaurant-list">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="restaurant-card">
                <div class="restaurant-img-container">
                    <?php 
                        $img_src = "assets/img/default_restaurant.png"; // Imagen por defecto
                        if (!empty($row['photo_id'])) {
                            $potential_img = "assets/img/phil-photos/phil/" . $row['photo_id'] . ".jpg";
                            if (file_exists($potential_img)) {
                                $img_src = $potential_img;
                            }
                        }
                    ?>
                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="restaurant-img">
                </div>
                <div class="restaurant-info">
                    <h3><a href="restaurant.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a></h3>
                    
                    <div class="stars">
                        <?php echo $row['stars']; ?> ★ 
                        <span style="color: #666; font-weight: normal;">(<?php echo $row['review_count']; ?> opiniones)</span>
                    </div>
                    
                    <div class="meta">
                        <p><strong>Puntuación Ponderada:</strong> <?php echo number_format($row['weighted_score'], 2); ?></p>
                        <p><?php echo htmlspecialchars($row['city']); ?>, <?php echo htmlspecialchars($row['state']); ?></p>
                        <p><em><?php echo htmlspecialchars($row['categories']); ?></em></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Paginación -->
    <div class="pagination">
        <?php
        $query_params = $_GET;
        unset($query_params['page']);
        $query_string = http_build_query($query_params);
        $link_prefix = "?" . ($query_string ? $query_string . "&" : "") . "page=";
        ?>

        <?php if ($page > 1): ?>
            <a href="<?php echo $link_prefix . ($page - 1); ?>">&laquo; Anterior</a>
        <?php endif; ?>

        <?php
        // Mostrar rango de páginas cercano a la actual
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);

        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="<?php echo $link_prefix . $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="<?php echo $link_prefix . ($page + 1); ?>">Siguiente &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
