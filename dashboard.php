<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$email   = isset($_SESSION['email']) ? $_SESSION['email'] : ('user'.$user_id.'@mail');

// Баланс
$balance = 0;
if (file_exists("balances.json")) {
    $balances = json_decode(file_get_contents("balances.json"), true);
    if (is_array($balances) && isset($balances[$user_id])) {
        $balance = $balances[$user_id];
    }
}

// Депозиты
$deposits = [];
if (file_exists("deposits.json")) {
    $all = json_decode(file_get_contents("deposits.json"), true);
    if (is_array($all)) {
        foreach ($all as $d) {
            if ($d["user_id"] == $user_id) {

                $created = strtotime($d["created_at"]);
                $now = time();
                $days_passed = floor(($now - $created) / 86400);
                if ($days_passed < 0) $days_passed = 0;
                if ($days_passed > $d["days"]) $days_passed = $d["days"];

                $profit = $d["amount"] * ($d["percent"] / 100) * ($days_passed / $d["days"]);

                $d["profit"]      = round($profit, 2);
                $d["days_passed"] = $days_passed;

                $deposits[] = $d;
            }
        }
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <title>Личный кабинет — Invest</title>
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
      max-width:960px;
      margin:0 auto;
      padding:20px 18px 40px;
    }
    header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin-bottom:24px;
    }
    .brand{
      display:flex;
      flex-direction:column;
      gap:4px;
    }
    .logo{
      font-weight:800;
      font-size:18px;
      letter-spacing:0.08em;
      text-transform:uppercase;
      color:var(--accent);
    }
    .brand span{
      font-size:13px;
      color:var(--muted);
    }
    .user-pill{
      display:flex;
      align-items:center;
      gap:10px;
      padding:8px 12px;
      border-radius:999px;
      background:rgba(255,255,255,0.9);
      box-shadow:0 1px 4px rgba(0,0,0,0.08);
      font-size:13px;
    }
    .user-avatar{
      width:28px;
      height:28px;
      border-radius:999px;
      background:linear-gradient(135deg,#d0d0d0,#f0f0f0);
    }
    .user-pill a{
      text-decoration:none;
      color:var(--muted);
      font-weight:500;
      padding-left:8px;
      border-left:1px solid #e0e0e0;
    }
    .user-pill a:hover{color:#000;}

    .grid{
      display:grid;
      grid-template-columns:1.4fr 1.1fr;
      gap:18px;
    }
    @media (max-width:840px){
      .grid{grid-template-columns:1fr;}
    }

    .card{
      background:rgba(255,255,255,0.96);
      border-radius:var(--radius);
      padding:18px 18px 16px;
      box-shadow:var(--shadow);
    }
    .card h2{
      margin:0 0 8px;
      font-size:20px;
      color:var(--accent);
    }
    .card p{
      margin:0;
      font-size:13px;
      color:var(--muted);
    }

    .balance-row{
      display:flex;
      align-items:flex-end;
      justify-content:space-between;
      margin-top:14px;
      gap:10px;
    }
    .balance-main{
      font-size:26px;
      font-weight:700;
      color:var(--accent);
    }
    .balance-label{
      font-size:12px;
      color:var(--muted);
    }

    .btn-row{
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      margin-top:14px;
    }
    .btn{
      flex:1;
      min-width:120px;
      text-align:center;
      text-decoration:none;
      font-size:13px;
      font-weight:600;
      padding:9px 10px;
      border-radius:10px;
      border:none;
      cursor:pointer;
      transition:background .12s, transform .08s, box-shadow .08s, opacity .12s;
    }
    .btn-primary{
      background:var(--accent);
      color:#fff;
      box-shadow:0 10px 22px rgba(0,0,0,0.18);
    }
    .btn-primary:hover{
      opacity:0.95;
      transform:translateY(-1px);
      box-shadow:0 13px 28px rgba(0,0,0,0.22);
    }
    .btn-ghost{
      background:#f3f3f3;
      color:var(--accent);
      box-shadow:none;
    }
    .btn-ghost:hover{
      background:#e7e7e7;
    }

    .deposits-list{
      margin-top:14px;
      display:flex;
      flex-direction:column;
      gap:10px;
      max-height:380px;
      overflow:auto;
    }
    .deposit-item{
      border-radius:12px;
      padding:10px 11px;
      background:#f7f7f7;
      border:1px solid #e1e1e1;
      font-size:13px;
      display:grid;
      grid-template-columns:1.2fr 1fr;
      column-gap:10px;
      row-gap:4px;
    }
    .tag{
      display:inline-block;
      padding:3px 8px;
      border-radius:999px;
      font-size:11px;
      background:#e4e4e4;
      color:#555;
      text-transform:uppercase;
      letter-spacing:0.04em;
      font-weight:600;
    }
    .text-muted{color:var(--muted);font-size:12px;}
    .text-strong{font-weight:600;color:var(--accent);}

    .side-card{
      margin-bottom:14px;
    }
    .side-stat{
      display:flex;
      justify-content:space-between;
      margin-top:8px;
      font-size:13px;
    }
  </style>
</head>
<body>
  <div class="shell">
    <header>
      <div class="brand">
        <div class="logo">INVEST</div>
        <span>Личный кабинет и депозиты</span>
      </div>
      <div class="user-pill">
        <div class="user-avatar"></div>
        <div>
          <div style="font-size:13px;"><?php echo htmlspecialchars($email); ?></div>
          <div style="font-size:11px;color:var(--muted);">ID: <?php echo (int)$user_id; ?></div>
        </div>
        <a href="logout.php">Выйти</a>
      </div>
    </header>

    <main class="grid">
      <section class="card">
        <h2>Баланс</h2>
        <p>Общий баланс аккаунта, без учёта активных депозитов.</p>

        <div class="balance-row">
          <div>
            <div class="balance-main"><?php echo number_format($balance, 2, '.', ' '); ?> USDT</div>
            <div class="balance-label">доступно для вывода и депозитов</div>
          </div>
        </div>

        <div class="btn-row">
          <a class="btn btn-primary" href="add_funds.html">Пополнить</a>
          <a class="btn btn-ghost" href="withdraw.html">Вывести</a>
          <a class="btn btn-ghost" href="transactions.php">История</a>
        </div>
      </section>

      <section>
        <div class="card side-card">
          <h2>Депозиты</h2>
          <p>Открывайте депозиты на месяц или год.</p>
          <div class="btn-row" style="margin-top:10px;">
            <a class="btn btn-primary" href="deposits.php">Открыть депозит</a>
          </div>
        </div>

        <div class="card">
          <h2>Сводка</h2>
          <div class="side-stat">
            <span class="text-muted">Количество депозитов</span>
            <span class="text-strong"><?php echo count($deposits); ?></span>
          </div>
          <div class="side-stat">
            <span class="text-muted">Тарифы</span>
            <span class="text-strong">8% / мес · 12% / год</span>
          </div>
        </div>
      </section>
    </main>

    <section class="card" style="margin-top:18px;">
      <h2>Ваши депозиты</h2>
      <?php if (count($deposits) === 0): ?>
        <p class="text-muted">Пока депозитов нет. Откройте первый на странице «Депозиты».</p>
      <?php else: ?>
        <div class="deposits-list">
          <?php foreach ($deposits as $d): ?>
            <div class="deposit-item">
              <div>
                <div>
                  <span class="tag">
                    <?php echo $d["type"] === "monthly" ? "8% в месяц" : "12% в год"; ?>
                  </span>
                </div>
                <div style="margin-top:6px;">
                  <span class="text-muted">Сумма:</span>
                  <span class="text-strong">₴<?php echo number_format($d["amount"], 2, '.', ' '); ?></span>
                </div>
                <div class="text-muted">
                  Открыт: <?php echo htmlspecialchars($d["created_at"]); ?> · До: <?php echo htmlspecialchars($d["end_at"]); ?>
                </div>
              </div>
              <div style="text-align:right;">
                <div class="text-muted">Текущая прибыль</div>
                <div class="text-strong">₴<?php echo number_format($d["profit"], 2, '.', ' '); ?></div>
                <div class="text-muted">
                  Дней: <?php echo (int)$d["days_passed"]; ?> / <?php echo (int)$d["days"]; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>
</body>
</html>
