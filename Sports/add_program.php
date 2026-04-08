<?php
session_start();
require_once '../Profile/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /SportVarietyFit/index.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$result = $conn->query("SELECT role FROM users WHERE id = $user_id LIMIT 1");
$user = $result?->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    header("Location: /SportVarietyFit/index.php");
    exit();
}

// Parāda visas sporta kategorijas select izvēlnē
$categories = [];
$res = $conn->query("SELECT id, badge FROM sports_categories ORDER BY badge ASC");
while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
}
// Parāda visas treniņu programmas select izvēlnē
$workout_types = [];
$res2 = $conn->query("SELECT id, workout_title FROM workouts ORDER BY workout_title ASC");
while ($row2 = $res2->fetch_assoc()) {
    $workout_types[] = $row2;
}
// Parāda visas workout programmas select izvēlnē
$workout_programs = [];
$res3 = $conn->query("SELECT id, title FROM workout_programs ORDER BY title ASC");
while ($row3 = $res3->fetch_assoc()) {
    $workout_programs[] = $row3;
}
// Pievieno workout programmu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_program'])) {
    $sports_category_id = (int) $_POST['sports_category_id'];
    $workout_type_id = (int) $_POST['workout_type_id'];
    $title = trim($_POST['title']);
    $short_description = trim($_POST['short_description']);
    $age_group = $_POST['age_group'];
    $level = $_POST['level'];
    $image = '';
    // Pievieno bildi
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('program_') . ".$ext";
        move_uploaded_file($_FILES['image']['tmp_name'],__DIR__ . "/../uploads/" . $image);
    }
    // Pievieno datu bāzē
    if ($sports_category_id && $workout_type_id && $title && $short_description && $age_group && $level) {
        $stmt = $conn->prepare("
            INSERT INTO workout_programs 
            (sports_category_id, workout_type_id, image, title, short_description, age_group, level) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $sports_category_id, $workout_type_id, $image, $title, $short_description, $age_group, $level);
        $stmt->execute();
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    $error = "All fields are required.";
}
// Pievieno vingrinājumus treniņa programmai
if (isset($_POST['add_exercise'])) {
    $program_id = (int) $_POST['workout_programs_id'];
    $sets = (int) $_POST['sets'];
    $titles = $_POST['exercise_title'];
    $descriptions = $_POST['exercise_description'];
    $reps = $_POST['exercise_reps'];
    $times = $_POST['exercise_time'];

    foreach ($titles as $i => $title) {
        if (empty(trim($title))) continue;
        $image = '';
        // pievieno bildi
        if (!empty($_FILES['exercise_image']['name'][$i])) {
            $ext = pathinfo($_FILES['exercise_image']['name'][$i], PATHINFO_EXTENSION);
            $image = uniqid('exercise_') . ".$ext";
            move_uploaded_file($_FILES['exercise_image']['tmp_name'][$i], __DIR__ . "/../uploads/" . $image);}
        $stmt = $conn->prepare("
            INSERT INTO exercises 
            (workout_program_id, image, title, description, reps, time_seconds, sets, section) VALUES (?, ?, ?, ?, ?, ?, ?, 'main')");
        $stmt->bind_param(
            "isssiii",$program_id, $image, $title, $descriptions[$i], $reps[$i], $times[$i], $sets);
        $stmt->execute();
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
// izvada esošos vingrinājumus tabulā
$exercises = $conn->query("
    SELECT e.*, wp.title AS program_name 
    FROM exercises e
    JOIN workout_programs wp ON e.workout_program_id = wp.id
    ORDER BY e.id DESC
")->fetch_all(MYSQLI_ASSOC);
// Dzēst vingrinājumu
if (isset($_GET['delete_id'])) {
    $id = (int) $_GET['delete_id'];
    $conn->query("DELETE FROM exercises WHERE id=$id");
    header("Location: add_program.php");
    exit;
}
// Rediģēt vingrinājumu
if (isset($_POST['update_exercise'])) {
    $id = (int)$_POST['exercise_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $sets = (int)$_POST['sets'];
    $reps = (int)$_POST['reps'];
    $time = (int)$_POST['time_seconds'];

    $stmt = $conn->prepare("
        UPDATE exercises 
        SET title=?, description=?, sets=?, reps=?, time_seconds=? 
        WHERE id=?");
    $stmt->bind_param("ssiiii", $title, $description, $sets, $reps, $time, $id);
    $stmt->execute();
    header("Location: add_program.php");
    exit;
}
$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/add_program.css">
    <title>Add workout program & Exercises</title>
</head>
<body>
    <?php include '../Include/header.php'; ?>
    <div class="admin-wrapper">
        <h1 style="text-align: center;">Admin Panel</h1>
        <?php include '../Include/adminbar.php'; ?>
    </div>
    <!-- pievieno treniņa programmu -->
    <div class="form-container">
        <h2>Add Workout Program</h2>
        <form method="post">
            <label>Sports Category</label>
            <select name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['badge']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Workout Type:</label>
            <select name="workout_type_id" required>
                <option value="">Select Type</option>
                <?php foreach ($workout_types as $type): ?>
                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['workout_title']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Image:</label>
            <input type="file" name="image" accept="image/*" required>
            <label>Title:</label>
            <input type="text" name="title" required>
            <label>Short Description:</label>
            <textarea name="short_description" rows="3" required></textarea>
            <label>Age Group:</label>
            <select name="age_group" required>
                <option value="">Select Age Group</option>
                <option value="kid">Kid</option>
                <option value="teen">Teen</option>
                <option value="adult">Adult</option>
                <option value="senior">Senior</option>
            </select>
            <label>Level:</label>
            <select name="level" required>
                <option value="">Select Level</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
            <button type="submit" name="add_program">Add Program</button>
        </form>
    </div>
    <!-- Pievieno vingrinājumus sadaļa-->
     <!-- Atlasa workout programmu -->
     <div class="form-container">
        <h2>Add Exercises</h2>
        <form method="post" enctype="multipart/form-data">
            <label>Sports Category</label>
            <select name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['badge']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Workout Type:</label>
            <select name="workout_type_id" required>
                <option value="">Select Type</option>
                <?php foreach ($workout_types as $type): ?>
                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['workout_title']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Workout Program:</label>
            <select name="workout_programs_id" required>
                <option value="">Select Program</option>
                <?php foreach ($workout_programs as $program): ?>
                    <option value="<?= $program['id'] ?>"><?= htmlspecialchars($program['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <!-- vingrinājuma pievienošana -->
            <fieldset>
                <label>Number of Sets:</label>
                <input type="number" name="sets" min="1" value="1" required style="width: 100%;">
                <div id="exercises-section">
                    <div class="exercise-block">
                        <label>Exercise Image:</label>
                        <input type="file" name="exercise_image[]" accept="image/*" required>
                        <label>Title:</label>
                        <input type="text" name="exercise_title[]" required style="width: 100%;">
                        <label>Description:</label>
                        <textarea name="exercise_description[]" style="width: 100%;" rows="2"></textarea>
                        <label>Reps:</label>
                        <input type="number" name="exercise_reps[]" min="0" style="width: 100%;">
                        <label>Time (seconds):</label>
                        <input type="number" name="exercise_time[]" min="0" style="width: 100%;">
                    </div>
                </div>
                <button type="button" onclick="addExerciseBlock()">Add Another Exercise</button>
            </fieldset>
            <button type="submit" name="add_exercise">Add Exercises</button>
        </form>
     </div>
     <!-- Rediģēt vingrinājumus -->
      <div class="form-container">
        <h2>Manage Exercises</h2>
        <form method="post">
            <label>Sports Category</label>
            <select name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['badge']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Workout Type:</label>
            <select name="workout_type_id" required>
                <option value="">Select Type</option>
                <?php foreach ($workout_types as $type): ?>
                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['workout_title']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Workout Program:</label>
            <select name="workout_programs_id" required>
                <option value="">Select Program</option>
                <?php foreach ($workout_programs as $program): ?>
                    <option value="<?= $program['id'] ?>"><?= htmlspecialchars($program['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <h3>Existing Exercises</h3>
            <Table class="exercise-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Sets</th>
                        <th>Reps</th>
                        <th>Time (seconds)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exercises as $ex): ?>
                        <?php if ($edit_id === (int)$ex['id']): ?>
                            <!-- Rediģēšana -->
                            <tr>
                                <form method="post">
                                    <input type="hidden" name="exercise_id" value="<?= $ex['id'] ?>">
                                    <td>
                                        <?php if ($ex['image']): ?>
                                            <img src="../uploads/<?= htmlspecialchars($ex['image']) ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td><input type="text" name="title" value="<?= htmlspecialchars($ex['title']) ?>"></td>
                                    <td><textarea name="description"><?= htmlspecialchars($ex['description']) ?></textarea></td>
                                    <td><input type="number" name="sets" value="<?= $ex['sets'] ?>" min="1"></td>
                                    <td><input type="number" name="reps" value="<?= $ex['reps'] ?>"></td>
                                    <td><input type="number" name="time_seconds" value="<?= $ex['time_seconds'] ?>"></td>
                                    <td>
                                        <button type="submit" name="update_exercise">Save</button><a href="add_program.php">Cancel</a>
                                    </td>
                                </form>
                            </tr>
                        <?php else: ?>
                            <!-- Parastais skats -->
                            <tr>
                                <td>
                                    <?php if ($ex['image']): ?>
                                        <img src="../uploads/<?= htmlspecialchars($ex['image']) ?>">
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($ex['title']) ?></td>
                                <td><?= htmlspecialchars($ex['description']) ?></td>
                                <td><?= htmlspecialchars($ex['sets']) ?></td>
                                <td><?= htmlspecialchars($ex['reps']) ?></td>
                                <td><?= htmlspecialchars($ex['time_seconds']) ?></td>
                                <td>
                                    <a href="add_program.php?edit_id=<?= $ex['id'] ?>">Edit</a>
                                    <a href="add_program.php?delete_id=<?= $ex['id'] ?>" onclick="return confirm('Delete this workout?')">Delete</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </Table>
        </form>
      </div>
    <?php include '../Include/footer.php'; ?>
</body>
<script>
    // Izveido jaunu vingrinājuma bloku formā
    function addExerciseBlock() {
    const section = document.getElementById('exercises-section');
    const block = document.createElement('div');
    block.className = 'exercise-block';
    block.innerHTML = `
        <label>Exercise Image:</label>
        <input type="file" name="exercise_image[]" accept="image/*" style="margin-bottom:8px;">
        <label>Title:</label>
        <input type="text" name="exercise_title[]" required style="width:100%;margin-bottom:8px;">
        <label>Description:</label>
        <textarea name="exercise_description[]" rows="2" style="width:100%;margin-bottom:8px;"></textarea>
        <label>Reps:</label>
        <input type="number" name="exercise_reps[]" min="0" style="width:100%;margin-bottom:8px;">
        <label>Time (seconds):</label>
        <input type="number" name="exercise_time[]" min="0" style="width:100%;margin-bottom:16px;">
    `;
    section.appendChild(block);
}
</script>
</html>