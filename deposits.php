<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <title>Открыть депозит — Invest</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    :root{
      --bg:#efefef;
      --card:#ffffff;
      --muted:#9e9e9e;
      --text:#333;
      --accent:#3b3b3b;
      --radius:14px;
      --shadow:0 18px 40px rgba(0,0,0,0.08);
      font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;
    }
    *{box-sizing:border-box;}
    body{
      margin:0;
      min-height:100vh;
      background:linear-gradient(135deg,#f3f3f3,#e0e0e0);
      color:var(--text);
    }
    .shell{
      max-width:760px;
      margin:0 auto;
      padding:26px 18px 40px;
    }
    .top-row{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:20px;
      gap:10px;
    }
    .logo{font-weight:800;font-size:18px;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);}
    .back-link{
      font-size:13px;
      color:var(--muted);
      text-decoration:none;
    }
    .back-link:hover{color:#000;}

    .grid{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:16px;
    }
    @media(max-width:720px){.grid{grid-template-columns:1fr;}}

    .card{
      background:var(--card);
      border-radius:var(--radius);
      padding:18px 18px 16px;
      box-shadow:var(--shadow);
    }
    .card h2{
      margin:0 0 6px;
      font-size:18px;
      color:var(--accent);
    }
    .muted{font-size:13px;color:var(--muted);margin:0 0 12px;}
    .percent{
      font-size:22px;
      font-weight:700;
      color:var(--accent);
      margin-bottom:4px;
    }
    label{
      display:block;
      font-size:13px;
      margin-bottom:4px;
    }
    input{
      width:100%;
      padding:9px 10px;
      border-radius:10px;
      border:1px solid #d3d3d3;
      font-size:14px;
      background:#fafafa;
      outline:none;
      margin-bottom:8px;
    }
    input:focus{
      background:#fff;
      border-color:#b0b0b0;
    }
    button{
      width:100%;
      border:none;
      border-radius:10px;
      padding:10px;
      background:var(--accent);
      color:#fff;
      font-weight:600;
      font-size:14px;
      cursor:pointer;
      box-shadow:0 10px 20px rgba(0,0,0,0.18);
    }
    button:hover{opacity:0.95;}
  </style>
</head>
<body>
  <div class="shell">
    <div class="top-row">
      <div class="logo">INVEST</div>
      <a class="back-link" href="dashboard.php">← Назад в кабинет</a>
    </div>

    <div class="grid">
      <section class="card">
        <h2>Месячный депозит</h2>
        <p class="muted">8% прибыли за 30 дней. Подходит для краткосрочных вложений.</p>
        <div class="percent">8% / месяц</div>

        <form action="deposit_process.php" method="POST">
          <input type="hidden" name="type" value="monthly">
          <label for="amount1">Сумма, ₴</label>
          <input id="amount1" required type="number" name="amount" min="1" step="0.01" placeholder="Например, 1000">
          <button type="submit">Открыть депозит</button>
        </form>
      </section>

      <section class="card">
        <h2>Годовой депозит</h2>
        <p class="muted">12% прибыли за 12 месяцев. Долгосрочный стабильный вариант.</p>
        <div class="percent">12% / год</div>

        <form action="deposit_process.php" method="POST">
          <input type="hidden" name="type" value="yearly">
          <label for="amount2">Сумма, ₴</label>
          <input id="amount2" required type="number" name="amount" min="1" step="0.01" placeholder="Например, 5000">
          <button type="submit">Открыть депозит</button>
        </form>
      </section>
    </div>
  </div>
</body>
</html>
