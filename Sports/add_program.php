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
            <select id="program_sport" name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>">
                        <?= htmlspecialchars($category['badge']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Workout Type:</label>
            <select id="program_type" name="workout_type_id" required>
                <option value="">Select Sport First</option>
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
            <select id="ex_sport" name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>">
                        <?= htmlspecialchars($category['badge']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Workout Type:</label>
            <select id="ex_type" name="workout_type_id" required>
                <option value="">Select Sport First</option>
            </select>

            <label>Workout Program:</label>
            <select id="ex_program" name="workout_programs_id" required>
                <option value="">Select Type First</option>
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
      <div class="exercise-manager">
        <h2>Manage Exercises</h2>
        <form method="post">
            <label>Sports Category</label>
            <select id="sports_category" name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>">
                        <?= htmlspecialchars($category['badge']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Workout Type</label>
            <select id="workout_type" name="workout_type_id">
                <option value="">Select Type</option>
            </select>
            <label>Workout Program</label>
            <select id="workout_program" name="workout_programs_id">
                <option value="">Select Program</option>
            </select>
            <h3>Existing Exercises</h3>
            <div class="table-wrapper">
                <table class="exercise-table">
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
                    <tbody id="exercise-body">
                        <tr id="empty-state">
                            <td colspan="7" style="text-align:center; padding:20px; color:#888;">
                                Please select Sports Category → Workout Type → Workout Program
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
      </div>
    <?php include '../Include/footer.php'; ?>
</body>
<script>
const sportSelect = document.getElementById("sports_category");
const typeSelect = document.getElementById("workout_type");
const programSelect = document.getElementById("workout_program");
const tbody = document.getElementById("exercise-body");
const editId = new URLSearchParams(window.location.search).get("edit_id");
const programSport = document.getElementById("program_sport");
const programType = document.getElementById("program_type");
const exSport = document.getElementById("ex_sport");
const exType = document.getElementById("ex_type");
const exProgram = document.getElementById("ex_program");

// add exercise formai - pievieno jaunu vingrinājuma bloku
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
exSport.addEventListener("change", function () {
    const sportId = this.value;

    exType.innerHTML = `<option value="">Loading...</option>`;
    exProgram.innerHTML = `<option value="">Select Type First</option>`;

    if (!sportId) {
        exType.innerHTML = `<option value="">Select Sport First</option>`;
        return;
    }

    fetch(`../Sports/get_workout_types.php?sports_category_id=${sportId}`)
        .then(res => res.json())
        .then(data => {
            exType.innerHTML = `<option value="">Select Type</option>`;

            if (!data.length) {
                exType.innerHTML = `<option value="">No types available</option>`;
                return;
            }

            data.forEach(type => {
                exType.innerHTML += `
                    <option value="${type.id}">
                        ${type.workout_title}
                    </option>
                `;
            });
        });
});
exType.addEventListener("change", function () {
    const sportId = exSport.value;
    const typeId = this.value;
    exProgram.innerHTML = `<option value="">Loading...</option>`;
    if (!typeId) {
        exProgram.innerHTML = `<option value="">Select Type First</option>`;
        return;
    }
    fetch(`../Sports/get_programs.php?sports_category_id=${sportId}&workout_type_id=${typeId}`)
        .then(res => res.json())
        .then(data => {
            exProgram.innerHTML = `<option value="">Select Program</option>`;
            if (!data.length) {
                exProgram.innerHTML = `<option value="">No programs available</option>`;
                return;
            }
            data.forEach(program => {
                exProgram.innerHTML += `
                    <option value="${program.id}">
                        ${program.title}
                    </option>
                `;
            });
        });
});


function loadExercises(programId) {
    const editId = new URLSearchParams(window.location.search).get("edit_id");
    if (!programId) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#888;">Please select Sports Category → Workout Type → Workout Program</td>
            </tr>
        `;
        return;
    }
    fetch(`../Sports/get_exercises.php?program_id=${programId}`)
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align:center; padding:20px; color:#888;">No exercises found</td>
                    </tr>
                `;
                return;
            }
            tbody.innerHTML = "";
            data.forEach(ex => {
                if (editId && editId == ex.id) {
                    tbody.innerHTML += `
                        <tr>
                            <form method="post">
                                <input type="hidden" name="exercise_id" value="${ex.id}">                           
                                <td>${ex.image ? `<img src="../uploads/${ex.image}">` : ""}</td>
                                <td><input type="text" name="title" value="${ex.title}"></td>                                
                                <td><textarea name="description">${ex.description ?? ""}</textarea></td>                                
                                <td><input type="number" name="sets" value="${ex.sets}"></td>                                
                                <td><input type="number" name="reps" value="${ex.reps}"></td>                               
                                <td><input type="number" name="time_seconds" value="${ex.time_seconds}"></td>                               
                                <td>
                                    <button type="submit" name="update_exercise">Save</button>
                                    <a href="#" onclick="cancelEdit()">Cancel</a>
                                </td>
                            </form>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML += `
                        <tr>
                            <td>${ex.image ? `<img src="../uploads/${ex.image}">` : ""}</td>
                            <td>${ex.title}</td>
                            <td>${ex.description ?? ""}</td>
                            <td>${ex.sets}</td>
                            <td>${ex.reps}</td>
                            <td>${ex.time_seconds}</td>
                            <td>
                                <a href="#" onclick="editExercise(${ex.id})">Edit</a>
                                <a href="add_program.php?delete_id=${ex.id}" onclick="return confirm('Delete?')">Delete</a>
                            </td>
                        </tr>
                    `;
                }
            });
        });
}
// rediģēt vingrinājumu
function editExercise(id) {
    const url = new URL(window.location.href);
    url.searchParams.set("edit_id", id);
    window.history.pushState({}, "", url);
    const programId = programSelect.value;
    if (programId) {
        loadExercises(programId);
    }
}
// atcelt rediģēšanu
function cancelEdit() {
    const url = new URL(window.location.href);
    url.searchParams.delete("edit_id");
    window.history.pushState({}, "", url);
    const programId = programSelect.value;
    if (programId) {
        loadExercises(programId);
    }
}
// galvenās izvēlnes - sporta kategorijas izvēle
sportSelect.addEventListener("change", function () {
    const sportId = this.value;
    typeSelect.innerHTML = `<option value="">Loading...</option>`;
    programSelect.innerHTML = `<option value="">Select Program</option>`;
    if (!sportId) {
        typeSelect.innerHTML = `<option value="">Select Type</option>`;
        return;
    }
    fetch(`../Sports/get_workout_types.php?sports_category_id=${sportId}`)
        .then(res => res.json())
        .then(data => {
            typeSelect.innerHTML = `<option value="">Select Type</option>`;
            if (!data.length) {
                typeSelect.innerHTML = `<option value="">No types available</option>`;
                return;
            }
            data.forEach(item => {
                typeSelect.innerHTML += `<option value="${item.id}">${item.workout_title}</option>`;
            });
        });
});
// galvenās izvēlnes - treniņu veida izvēle
typeSelect.addEventListener("change", function () {
    const sportId = sportSelect.value;
    const typeId = this.value;
    programSelect.innerHTML = `<option value="">Loading...</option>`;
    if (!typeId) {
        programSelect.innerHTML = `<option value="">Select Program</option>`;
        return;
    }
    fetch(`../Sports/get_programs.php?sports_category_id=${sportId}&workout_type_id=${typeId}`)
        .then(res => res.json())
        .then(data => {
            programSelect.innerHTML = `<option value="">Select Program</option>`;
            if (!data.length) {
                programSelect.innerHTML = `<option value="">No programs available</option>`;
                return;
            }
            data.forEach(item => {
                programSelect.innerHTML += `<option value="${item.id}">${item.title}</option>`;
            });
        });
});
programSelect.addEventListener("change", function () {
    loadExercises(this.value);
});
// add workout program formai
programSport.addEventListener("change", function () {
    const sportId = this.value;
    programType.innerHTML = `<option value="">Loading...</option>`;
    if (!sportId) {
        programType.innerHTML = `<option value="">Select Sport First</option>`;
        return;
    }
    fetch(`../Sports/get_workout_types.php?sports_category_id=${sportId}`)
        .then(res => res.json())
        .then(data => {
            programType.innerHTML = `<option value="">Select Type</option>`;
            if (!data.length) {
                programType.innerHTML = `<option value="">No types available</option>`;
                return;
            }
            data.forEach(type => {
                programType.innerHTML += `
                    <option value="${type.id}">
                        ${type.workout_title}
                    </option>
                `;
            });
        });
});
</script>
</html>