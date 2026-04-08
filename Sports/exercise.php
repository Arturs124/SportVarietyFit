<?php
require_once '../Profile/db.php';

$program_id = isset($_GET['program_id']) ? (int)$_GET['program_id'] : 0;
// treniņu dati
$result = $conn->query("SELECT * FROM exercises WHERE workout_program_id = $program_id ORDER BY id ASC");
$exercises = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercise</title>
    <link rel="stylesheet" href="../Assets/css/exercise.css">
</head>
<body>
    <?php include '../Include/header.php'; ?>
    <div class="exercise-container" id="stepper">
        <div class="workout-section">
            <div class="workout-title">
                Exercises (<?= count($exercises) ?>) | Sets: <?= $exercises ? $exercises[0]['sets'] : 1 ?>
            </div>
            <?php if($exercises): ?>
                <div class="workout-cards-row">
                    <?php foreach ($exercises as $ex): ?>
                        <div class="workout-card">
                            <?php if($ex['image']): ?>
                                <img src="../uploads/<?= htmlspecialchars($ex['image']) ?>" class="workout-card-image">
                            <?php endif; ?>
                            <div class="workout-card-title">
                                <?= htmlspecialchars($ex['title']) ?>
                            </div>
                            <div class="workout-card-type">
                                <?= $ex['reps'] ? $ex['reps'] . ' reps' : ''?>
                                <?= $ex['time_seconds'] ? ' | ' . $ex['time_seconds'] . ' sec' : '' ?>
                            </div>
                            <div>
                                <?= htmlspecialchars($ex['description']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No exercises found.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../Include/footer.php'; ?>
</body>
<script>
const exercises = <?= json_encode($exercises) ?>;
let current = 0;
let currentSet = 1;
let sets = <?= $exercises ? (int)$exercises[0]['sets'] : 1 ?>;
let timerInterval = null;
// sākuma ekrāns
function renderPreview() {
    const stepper = document.getElementById('stepper');
    if (!exercises.length) {
        stepper.innerHTML = "<p>No exercises found.</p>";
        return;
    }
    let html = `
        <div style="text-align:center;">
            <h2>Workout Preview</h2>
            <p>Total Exercises: ${exercises.length}</p>
            <p>Total Sets: ${sets}</p>
            <button class="next-btn" onclick="startWorkout()">Start Workout</button>
        </div>`;
    stepper.innerHTML = html;
}
// vingrinājuma renderēšana
function renderExercise() {
    const stepper = document.getElementById('stepper');
    if (!exercises.length) {
        stepper.innerHTML = "<p>No exercises found.</p>";
        return;
    }
    if (current >= exercises.length) {
        stepper.innerHTML = `
            <h2>Workout Complete!</h2>
            <button class="next-btn" onclick="renderPreview()">Back to Start</button>`;
        return;
    }
    const ex = exercises[current];
    stepper.innerHTML = `
        <div>Set ${currentSet}/${sets} | Exercise ${current + 1}/${exercises.length}</div>
        ${ex.image ? `<img src="../uploads/${ex.image}" style="max-width:300px;border-radius:10px;">` : ''}
        <h2>${ex.title}</h2>
        <p>${ex.description ? ex.description.replace(/\n/g, '<br>') : ''}</p>
        <p>
            ${ex.reps ? ex.reps + ' reps' : ''}
            ${ex.time_seconds ? ' | ' + ex.time_seconds + ' sec' : ''}
        </p>
        <button class="next-btn" onclick="nextExercise()">Next</button>`;
}
function nextExercise() {
    clearInterval(timerInterval);
    if (current < exercises.length - 1) {
        restBetweenExercises();
    } else {
        if (currentSet < sets) {
            restBetweenSets();
        } else {
            current++;
            renderExercise();
        }
    }
}
// atpūta starp vingrinājumiem
function restBetweenExercises() {
    let time = 30;
    const stepper = document.getElementById('stepper');
    stepper.innerHTML = `
        <h2>Rest</h2>
        <p>Next exercise in</p>
        <div id="timer" style="font-size: 2rem;margin: 15px 0;">30</div>
        <button class="next-btn" onclick="skipRest()">Skip</button>`;
    const timer = document.getElementById('timer');
    timerInterval = setInterval(() => {
        time--;
        timer.textContent = time;
        if (time <= 0) {
            clearInterval(timerInterval);
            current++;
            renderExercise();
        }
    }, 1000);
}
// atpūta starp setiem
function restBetweenSets() {
    let time = 60;
    const stepper = document.getElementById('stepper');
    stepper.innerHTML = `
        <div style="text-align:center;">
            <h2>Rest Between Sets</h2>
            <p>Next set (${currentSet + 1} / ${sets})</p>
            <div id="timer" style="font-size:2rem;margin:15px 0;">60</div>
            <button class="next-btn" onclick="skipSetRest()">Skip</button>
        </div>`;
    const timer = document.getElementById('timer');
    timerInterval = setInterval(() => {
        time--;
        timer.textContent = time;

        if (time <= 0) {
            clearInterval(timerInterval);
            currentSet++;
            current = 0;
            renderExercise();
        }
    }, 1000);
}

function skipSetRest() {
    clearInterval(timerInterval);
    currentSet++;
    current = 0;
    renderExercise();
}

function startWorkout() {
    current = 0;
    currentSet = 1;
    renderExercise();
}

function skipRest() {
    clearInterval(timerInterval);
    current++;
    renderExercise();
}
renderPreview();
</script>
</html>