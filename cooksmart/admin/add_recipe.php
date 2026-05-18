<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Recipe - CookSmart</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>

    <main class="app-shell">
        <header class="page-header">
            <div>
                <h1 class="page-title">Add new recipe</h1>
                <p class="page-subtitle">Enter the recipe once, then CookSmart can scale servings and run guided timers.</p>
            </div>
            <a class="btn-outline-cs" href="dashboard.php">Back to dashboard</a>
        </header>

        <section class="panel panel-pad">
            <form class="form-stack" method="POST" enctype="multipart/form-data">
                <input class="field" type="text" name="name" placeholder="Recipe name" required>

                <textarea name="description" placeholder="Short description"></textarea>

                <label>
                    <strong>Upload image</strong>
                    <input class="field" type="file" name="image" accept="image/*">
                </label>

                <div class="recipe-grid">
                    <label>
                        <strong>Base quantity in grams</strong>
                        <input class="field" type="number" name="base_quantity" placeholder="Example: 500" required>
                    </label>

                    <label>
                        <strong>Base serving in people</strong>
                        <input class="field" type="number" name="base_serving" placeholder="Example: 4" required>
                    </label>
                </div>

                <label>
                    <strong>Ingredients</strong>
                    <input class="field" type="text" name="ingredients" placeholder="Rice-500g, Water-1L, Salt-2spoon" required>
                </label>

                <label>
                    <strong>Steps with time</strong>
                    <input class="field" type="text" name="steps" placeholder="Boil water|10:00, Add salt|1:00, Cook rice|15:00" required>
                </label>

                <button name="save">Add recipe</button>
            </form>

            <?php
            if (isset($_POST['save'])) {
                $name = $_POST['name'];
                $desc = $_POST['description'];
                $bq = $_POST['base_quantity'];
                $bs = $_POST['base_serving'];
                $imageName = "";

                if (!empty($_FILES['image']['name'])) {
                    $imageName = time() . "_" . $_FILES['image']['name'];
                    move_uploaded_file($_FILES['image']['tmp_name'], "../assets/" . $imageName);
                }

                $adminId = (int)$_SESSION['user']['id'];
                $conn->query("INSERT INTO recipes (name, description, base_quantity, base_serving, image, submitted_by, status)
                              VALUES ('$name','$desc','$bq','$bs','$imageName','$adminId','approved')");

                $recipe_id = $conn->insert_id;
                $ingredients = explode(",", $_POST['ingredients']);

                foreach ($ingredients as $ing) {
                    $parts = explode("-", trim($ing));
                    $iname = $parts[0];
                    $qty = isset($parts[1]) ? $parts[1] : '';

                    $conn->query("INSERT INTO ingredients (recipe_id, name, quantity)
                                  VALUES ('$recipe_id','$iname','$qty')");
                }

                $steps = explode(",", $_POST['steps']);
                $step_no = 1;

                foreach ($steps as $s) {
                    $parts = explode("|", trim($s));
                    $instruction = $parts[0];
                    $timeText = isset($parts[1]) ? $parts[1] : '0:00';
                    $timeParts = explode(":", $timeText);
                    $minutes = isset($timeParts[0]) ? (int)$timeParts[0] : 0;
                    $seconds = isset($timeParts[1]) ? (int)$timeParts[1] : 0;
                    $totalSeconds = ($minutes * 60) + $seconds;

                    $conn->query("INSERT INTO steps (recipe_id, step_no, instruction, time_sec)
                                  VALUES ('$recipe_id','$step_no','$instruction','$totalSeconds')");

                    $step_no++;
                }

                echo "<div class='alert-cs alert-success'>Recipe added successfully.</div>";
            }
            ?>
        </section>
    </main>
</body>
</html>
