<?php
session_start();

// Redirect only if POST triggered by "Proceed"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['captcha_passed'])) {
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        // No session → back to login
        header("Location: login.php");
        exit;
    }

    // ✅ Role-based redirect
    if ($_SESSION['role'] === 'admin') {
        echo "<script>alert('CAPTCHA Verified Successfully!'); window.location.href='admin.php';</script>";
        exit;
    } elseif ($_SESSION['role'] === 'user') {
        echo "<script>alert('CAPTCHA Verified Successfully!'); window.location.href='welcome.php';</script>";
        exit;
    } else {
        // fallback if something went wrong
        header("Location: login.php");
        exit;
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Custom CAPTCHA</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: url('download (2) (1).jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: Arial, sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      width: 350px;
    }

    /* reCAPTCHA style box */
    .checkbox-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 5px;
      background: #fff;
      margin-bottom: 20px;
    }

    .checkbox-container input {
      width: 20px;
      height: 20px;
      margin-right: 10px;
      pointer-events: none; /* prevent manual clicking */
    }

    .checkbox-text {
      flex-grow: 1;
      text-align: left;
      font-size: 16px;
    }

    .recaptcha-logo {
      width: 40px;
      height: 40px;
      background: url('https://www.gstatic.com/recaptcha/api2/logo_48.png') no-repeat center;
      background-size: contain;
    }

    canvas {
      border: 2px solid #6d6868;
      background-color: #fff;
      touch-action: none;
    }

    button {
      margin: 10px 5px;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
    }

    #verify-btn { background-color: #3d8db1ff; color: white; }
    #retry-btn { background-color: #4aaad6ff; color: white; display: none; }
    #proceed-btn { background-color: #156ec8ff; color: white; display: none; }
    #result { margin-top: 10px; font-weight: bold; }
  </style>
</head>
<body>

<div class="container">

 <!-- Always visible "I am not a robot" -->
<div id="not-robot" class="checkbox-container">
  <div style="display:flex; align-items:center;">
    <input type="checkbox" id="fakeCheck">
    <span class="checkbox-text">I'm not a robot</span>
  </div>
  <div style="display:flex; flex-direction:column; align-items:center; font-size:10px; color:#555; font-family: Arial, sans-serif;">
    <div class="recaptcha-logo"></div>
    <span style="margin-top:2px;">reCAPTCHA</span>
  </div>
</div>

  <!-- Actual captcha -->
  <div id="captcha-stage">
    <h3>Trace the given shape to continue</h3>
    <canvas id="captchaCanvas" width="300" height="300"></canvas><br>
    <button id="verify-btn">Verify</button>
    <button id="retry-btn">Retry</button>

    <!-- Hidden form -->
    <form method="POST" style="display:inline;">
      <input type="hidden" name="captcha_passed" value="1">
      <button id="proceed-btn" type="submit">Proceed</button>
    </form>
    <p id="result"></p>
  </div>

</div>

<script>
  const fakeCheck = document.getElementById("fakeCheck");
  const canvas = document.getElementById("captchaCanvas");
  const ctx = canvas.getContext("2d");
  const verifyBtn = document.getElementById("verify-btn");
  const retryBtn = document.getElementById("retry-btn");
  const proceedBtn = document.getElementById("proceed-btn");
  const result = document.getElementById("result");

  let userPoints = [];
  let drawing = false;
  let shapePoints = [];
  const shapeTypes = ['star', 'triangle', 'square', 'circle'];

  function getPos(evt) {
    const rect = canvas.getBoundingClientRect();
    const x = (evt.touches ? evt.touches[0].clientX : evt.clientX) - rect.left;
    const y = (evt.touches ? evt.touches[0].clientY : evt.clientY) - rect.top;
    return { x, y };
  }

  function startDraw(e) {
    drawing = true;
    userPoints = [];
    ctx.strokeStyle = "gold";
    ctx.lineWidth = 2;
    ctx.beginPath();
    const pos = getPos(e);
    ctx.moveTo(pos.x, pos.y);
    userPoints.push(pos);
  }

  function draw(e) {
    if (!drawing) return;
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    userPoints.push(pos);
  }

  function endDraw() { drawing = false; }

  canvas.addEventListener("mousedown", startDraw);
  canvas.addEventListener("mousemove", draw);
  canvas.addEventListener("mouseup", endDraw);
  canvas.addEventListener("touchstart", startDraw);
  canvas.addEventListener("touchmove", draw);
  canvas.addEventListener("touchend", endDraw);

  function isPointNear(p1, p2, tolerance = 30) {
    const dx = p1.x - p2.x;
    const dy = p1.y - p2.y;
    return Math.sqrt(dx * dx + dy * dy) <= tolerance;
  }

  function verifyDrawing() {
    let matched = 0;
    for (const sp of shapePoints) {
      for (const up of userPoints) {
        if (isPointNear(sp, up)) { matched++; break; }
      }
    }
    verifyBtn.style.display = "none";
    if (matched >= shapePoints.length * 0.7) {
      result.textContent = "✔ Passed! You may proceed.";
      result.style.color = "blue";
      retryBtn.style.display = "inline-block";
      proceedBtn.style.display = "inline-block";
      fakeCheck.checked = true;  // <-- mark the checkbox only if successful
    } else {
      result.textContent = "✘ Try Again. Trace more accurately.";
      result.style.color = "red";
      retryBtn.style.display = "inline-block";
      proceedBtn.style.display = "none";
      fakeCheck.checked = false; // <-- uncheck if failed
    }
  }

  function clearCanvas() { ctx.clearRect(0, 0, canvas.width, canvas.height); }

  function drawShape() {
    const shape = shapeTypes[Math.floor(Math.random() * shapeTypes.length)];
    clearCanvas();
    ctx.strokeStyle = "lightgray";
    ctx.lineWidth = 2;
    ctx.beginPath();
    shapePoints = [];

    if (shape === 'star') {
      shapePoints = [
        { x: 150, y: 50 }, { x: 170, y: 120 }, { x: 240, y: 120 },
        { x: 185, y: 165 }, { x: 200, y: 240 }, { x: 150, y: 195 },
        { x: 100, y: 240 }, { x: 115, y: 165 }, { x: 60, y: 120 }, { x: 130, y: 120 },
      ];
    } else if (shape === 'triangle') {
      shapePoints = [{ x: 150, y: 50 }, { x: 250, y: 250 }, { x: 50, y: 250 }];
    } else if (shape === 'square') {
      shapePoints = [{ x: 75, y: 75 }, { x: 225, y: 75 }, { x: 225, y: 225 }, { x: 75, y: 225 }];
    } else if (shape === 'circle') {
      const cx = 150, cy = 150, r = 80;
      for (let angle = 0; angle < 360; angle += 15) {
        let rad = angle * Math.PI / 180;
        shapePoints.push({ x: cx + r * Math.cos(rad), y: cy + r * Math.sin(rad) });
      }
    }

    if (shapePoints.length > 0) {
      ctx.moveTo(shapePoints[0].x, shapePoints[0].y);
      for (let i = 1; i < shapePoints.length; i++) ctx.lineTo(shapePoints[i].x, shapePoints[i].y);
      ctx.closePath();
      ctx.stroke();
    }
  }

  verifyBtn.addEventListener("click", verifyDrawing);
  retryBtn.addEventListener("click", () => {
    result.textContent = "";
    retryBtn.style.display = "none";
    proceedBtn.style.display = "none";
    verifyBtn.style.display = "inline-block";
    fakeCheck.checked = false; // reset checkmark
    drawShape();
  });

  // Initial shape
  drawShape();
</script>

</body>
</html>
