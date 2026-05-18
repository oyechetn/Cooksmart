<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['recipe_id']) && isset($_POST['action'])) {
    $recipeId = (int)$_POST['recipe_id'];
    if ($_POST['action'] == 'delete') {
        $conn->query("DELETE FROM feedback WHERE recipe_id=$recipeId");
        $conn->query("DELETE FROM ingredients WHERE recipe_id=$recipeId");
        $conn->query("DELETE FROM steps WHERE recipe_id=$recipeId");
        $conn->query("DELETE FROM recipes WHERE id=$recipeId");
    } else {
        $action = $_POST['action'] == 'approve' ? 'approved' : 'rejected';
        $conn->query("UPDATE recipes SET status='$action' WHERE id=$recipeId");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - CookSmart</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>

    <main class="app-shell">
        <header class="page-header">
            <div>
                <h1 class="page-title">Admin dashboard</h1>
                <p class="page-subtitle">Manage your CookSmart recipe collection and keep cooking instructions clear.</p>
            </div>
            <a class="btn-cs" href="add_recipe.php">Add recipe</a>
        </header>

        <section class="admin-list">
            <?php
            $result = $conn->query("SELECT r.*, u.name AS submitted_name, AVG(f.rating) AS avg_rating, COUNT(f.id) AS rating_count
                                    FROM recipes r
                                    LEFT JOIN users u ON u.id = r.submitted_by
                                    LEFT JOIN feedback f ON f.recipe_id = r.id
                                    GROUP BY r.id
                                    ORDER BY FIELD(r.status, 'pending', 'approved', 'rejected'), r.id DESC");

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $statusClass = $row['status'] == 'pending' ? 'badge-pending' : '';
            ?>
                <article class="admin-item">
                    <div class="page-header" style="margin-bottom:8px;">
                        <div>
                            <span class="badge-cs <?php echo $statusClass; ?>"><?php echo htmlspecialchars(ucfirst($row['status'])); ?></span>
                            <h3 style="margin-top:10px;"><?php echo htmlspecialchars($row['name']); ?></h3>
                        </div>
                        <div class="rating-summary">
                            <span class="rating-dial"><?php echo $row['rating_count'] > 0 ? number_format($row['avg_rating'], 1) : 'New'; ?></span>
                            <span><?php echo (int)$row['rating_count']; ?> ratings</span>
                        </div>
                    </div>
                    <p class="page-subtitle"><?php echo htmlspecialchars($row['description']); ?></p>
                    <p class="page-subtitle">
                        Submitted by <?php echo $row['submitted_name'] ? htmlspecialchars($row['submitted_name']) : 'Admin'; ?>
                    </p>

                    <div class="admin-actions">
                        <?php if ($row['status'] == 'pending') { ?>
                            <form class="admin-actions" method="POST">
                                <input type="hidden" name="recipe_id" value="<?php echo (int)$row['id']; ?>">
                                <button name="action" value="approve">Approve</button>
                                <button class="btn-danger-cs" name="action" value="reject">Reject</button>
                            </form>
                        <?php } else { ?>
                            <a class="btn-cs" href="../user/view_recipe.php?id=<?php echo (int)$row['id']; ?>">View recipe</a>
                        <?php } ?>

                        <form method="POST" onsubmit="return confirm('Delete this recipe permanently?');">
                            <input type="hidden" name="recipe_id" value="<?php echo (int)$row['id']; ?>">
                            <button class="btn-danger-cs" name="action" value="delete">Delete</button>
                        </form>
                    </div>
                </article>
            <?php
                }
            } else {
            ?>
                <div class="panel panel-pad">
                    <h2>No recipes yet</h2>
                    <p class="page-subtitle">Add your first recipe to start building the CookSmart library.</p>
                </div>
            <?php } ?>
        </section>
    </main>
</body>
</html>
