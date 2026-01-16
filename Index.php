<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Assets/css/homepage.css">
    <link rel="icon" href="Bildes/SportVarietyFitIcon.png">
    <title>Homepage</title>
</head>
<body>
<?php include 'Include/header.php'; ?>
<img src="Bildes/Banner.png" alt="Banner" class="Banner">
<!-- Sporta kategorija -->
<h1 class="choose-sport-title" style="text-align:center;margin-top:40px;">Select Your Sport</h1>
<div class="sports-grid">
    <div class="sport-card">
        <img src="Bildes/category/football.jpg" alt="Football" class="card-image" style="width:300px;height:300px;object-fit:cover;border-radius:8px;margin-bottom:10px;">
        <p class="badge" style="font-weight:bold;">Football</p>
        <h2 class="card-title" style="font-size:1.1rem;">Strength & Speed</h2>
        <a href="#" class="card-button" style="display:inline-block;margin-top:8px;padding:8px 16px;background:#a71d2a;color:#fff;border-radius:6px;text-decoration:none;">View</a>
    </div>

    <div class="sport-card">
        <img src="Bildes/category/basketball.jpg" alt="Basketball" class="card-image" style="width:300px;height:300px;object-fit:cover;border-radius:8px;margin-bottom:10px;">
        <p class="badge" style="font-weight:bold;">Basketball</p>
        <h2 class="card-title" style="font-size:1.1rem;">Explosive Power</h2>
        <a href="#" class="card-button" style="display:inline-block;margin-top:8px;padding:8px 16px;background:#a71d2a;color:#fff;border-radius:6px;text-decoration:none;">View</a>
    </div>

    <div class="sport-card">
        <img src="Bildes/category/motocross.jpg" alt="Motocross" class="card-image" style="width:300px;height:300px;object-fit:cover;border-radius:8px;margin-bottom:10px;">
        <p class="badge" style="font-weight:bold;">Motocross</p>
        <h2 class="card-title" style="font-size:1.1rem;">Endurance & Control</h2>
        <a href="#" class="card-button" style="display:inline-block;margin-top:8px;padding:8px 16px;background:#a71d2a;color:#fff;border-radius:6px;text-decoration:none;">View</a>
    </div>

    <div class="sport-card">
        <img src="Bildes/category/swimming.jpg" alt="Swimming" class="card-image" style="width:300px;height:300px;object-fit:cover;border-radius:8px;margin-bottom:10px;">
        <p class="badge" style="font-weight:bold;">Swimming</p>
        <h2 class="card-title" style="font-size:1.1rem;">Technique & Speed</h2>
        <a href="#" class="card-button" style="display:inline-block;margin-top:8px;padding:8px 16px;background:#a71d2a;color:#fff;border-radius:6px;text-decoration:none;">View</a>
    </div>
</div>
<div style="text-align:center;margin-top:24px;">
    <a href="#" style="display:inline-block;padding:12px 32px;background:#2b4cff;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:1.1em;box-shadow:0 2px 8px #2b4cff22;">View More Sports</a>
</div>

<!-- abonements -->
<div class="plans-section-bg">
    <div class="plans-container">
        <div class="plans-container" style="max-width:800px;margin:40px auto 0 auto;display:flex;gap:32px;justify-content:center;align-items:flex-start;flex-wrap:wrap;"> 
            <!-- Free Plan -->
            <div class="plan-card" style="background:#fff;border-radius:16px;box-shadow:0 4px 24px #0001;padding:32px 28px;flex:1 1 320px;min-width:280px;text-align:center;border:2.5px solid #e3e8f0;">
                <div class="plan-title" style="font-size:1.5em;font-weight:700;margin-bottom:10px;color:#2b4cff;">Free Plan</div>
                <div class="plan-price" style="font-size:2.2em;font-weight:800;margin-bottom:18px;color:#222;">$0<span style="font-size:0.5em;font-weight:400;">/month</span></div>
                <ul class="plan-features" style="text-align:left;margin:0 auto 22px auto;max-width:260px;">
                    <li>Access to all sports categories</li>
                    <li>Access to all home and gym workout programs</li>
                    <li>Participate in daily, weekly, and monthly challenges</li>
                    <li>View basic progress statistics (completed workouts, challenge streaks, etc.)</li>
                </ul>
                <button class="plan-btn" style="background:#e3e8f0;color:#aaa;cursor:not-allowed;" disabled>Current Plan</button>
            </div>
            <!-- Pro Plan -->
            <div class="plan-card pro" style="background:#fff;border-radius:16px;box-shadow:0 6px 32px #2b4cff22;padding:32px 28px;flex:1 1 320px;min-width:280px;text-align:center;border:2.5px solid #2b4cff;">
                <div class="plan-title" style="font-size:1.5em;font-weight:700;margin-bottom:10px;color:#2b4cff;">Pro Plan</div>
                <div class="plan-price" style="font-size:2.2em;font-weight:800;margin-bottom:18px;color:#222;">$4.99<span style="font-size:0.5em;font-weight:400;">/month</span></div>
                <ul class="plan-features" style="text-align:left;margin:0 auto 22px auto;max-width:260px;">
                    <li>Access to the AI fitness assistant (answers questions, gives exercise ideas)</li>
                    <li>Access to advanced statistics (time trained, muscle focus, calories burned)</li>
                    <li>Ad-free experience</li>
                    <li>Can download workouts as PDF <strong>(Free Plan: Can only view workouts online.)</strong></li>
                </ul>
                <button class="plan-btn" style="background:linear-gradient(90deg,#2b4cff 60%,#6d8cff 100%);color:#fff;">Upgrade to Pro</button>
            </div>
        </div>
    </div>
</div>

<!-- About Us -->
<section class="about-section" style="max-width:700px;margin:30px auto 0 auto;padding:24px 18px;background:#fff;border-radius:12px;box-shadow:0 2px 16px #0001;">
    <h2 style="font-size:1.6rem;margin-bottom:10px;text-align:center;">🏆 What is SportVariety Fit?</h2>
    <p style="font-size:1.08rem;color:#333;line-height:1.6;">
        <strong>SportVarietyFit</strong> is a sport-specific workout platform built for athletes of all kinds - whether you're training for basketball, football, motocross, swimming, or anything in between. 
        <br><br>
        We believe fitness shouldn't be one-size-fits-all. That's why we offer customized workout plans based on the sport you choose. Whether you train at home or in a gym, our programs are tailored to match the physical demands of your sport - helping you build the right strength, speed, endurance, and skills to perform at your best.
        <br><br>
        From complete beginners to experienced athletes, SportVariety Fit makes it easy to:
        <ul>
            <li><strong>Select your sport</strong></li>
            <li><strong>Follow a structured training plan</strong></li>
            <li><strong>Combine workouts from multiple sports</strong></li>
            <li><strong>Track your progress and stay motivated</strong></li>
        </ul>
        <br>
        Train with purpose. Train for your sport.
    </p>
</section>

<!-- Motivacijas slideshow -->
<div class="slideshow">
    <div class="slides">
        <img src="Bildes/motivation/ali.png">
        <img src="Bildes/motivation/kobe.png">
        <img src="Bildes/motivation/jordan.png">
        <img src="Bildes/motivation/usain.png">
    </div>
</div>

<!-- blog -->
<section class="blog-preview-section" style="max-width:500px;margin:40px auto;padding:28px 20px 32px 20px;background:linear-gradient(120deg,#f7f9fa 60%,#e3e8f0 100%);border-radius:16px;box-shadow:0 4px 24px #2b4cff11;">
    <h2 style="font-size:1.5rem;margin-bottom:18px;text-align:center;letter-spacing:1px;color:#2b4cff;">📝 Latest Blog Post</h2>
    
    <div class="blog-preview" style="display:flex;flex-direction:column;align-items:center;gap:0;">
        <div style="width:100%;max-width:340px;overflow:hidden;border-radius:12px;box-shadow:0 2px 16px #2b4cff11;margin-bottom:16px;">
            <img src="Bildes/blogimage.png" alt="Blog Image" style="width:100%;display:block;">
        </div>
        <h3 style="margin-bottom:8px;font-size:1.25em;font-weight:700;color:#222;text-align:center;">Training Smarter for Your Sport</h3>
        <div style="color:#888;font-size:0.97em;margin-bottom:10px;">Jan 16, 2026</div>
        <p style="margin-bottom:16px;font-size:1.08em;color:#333;line-height:1.6;text-align:center;">
            Discover how sport-specific workouts can dramatically improve performance and help you reach your goals faster.
        </p>
        <a href="#" style="display:inline-block;padding:10px 28px;background:linear-gradient(90deg,#2b4cff 60%,#6d8cff 100%);color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:1.08em;box-shadow:0 2px 8px #2b4cff22;transition:background 0.18s;">Read Full Post</a>
    </div>
    
    <div style="text-align:center;margin-top:18px;">
        <a href="#" style="display:inline-block;padding:10px 22px;background:#0078d7;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;">View More Blog Posts</a>
    </div>
</section>

<?php include 'Include/footer.php'; ?>
<script src="Assets/js/slide.js"></script>
<script src="Assets/js/slideshow.js"></script>
</body>
</html>