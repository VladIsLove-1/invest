<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

$transactions = [];
if (file_exists("transactions.json")) {
    $all = json_decode(file_get_contents("transactions.json"), true);
    if (is_array($all)) {
        foreach ($all as $tx) {
            if ($tx["user_id"] == $user_id) {
                $transactions[] = $tx;
            }
        }
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <title>История транзакций — Invest</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    :root{
      --bg:#efefef;--card:#ffffff;--muted:#9e9e9e;--text:#333;--accent:#3b3b3b;
      --radius:14px;--shadow:0 18px 40px rgba(0,0,0,0.08);
      font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;
    }
    *{box-sizing:border-box;}
    body{
      margin:0;min-height:100vh;
      background:linear-gradient(135deg,#f3f3f3,#e0e0e0);
      color:var(--text);
    }
    .shell{max-width:760px;margin:0 auto;padding:26px 18px 40px;}
    .top-row{
      display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;
    }
    .logo{font-weight:800;font-size:18px;letter-spacing:0.08em;text-transform:uppercase;color:var(--accent);}
    .back-link{font-size:13px;color:var(--muted);text-decoration:none;}
    .back-link:hover{color:#000;}
    .card{
      background:var(--card);border-radius:var(--radius);
      padding:18px 18px 12px;box-shadow:var(--shadow);
    }
    h1{margin:0 0 8px;font-size:20px;color:var(--accent);}
    p{margin:0 0 12px;font-size:13px;color:var(--muted);}
    .tx-list{margin-top:10px;display:flex;flex-direction:column;gap:8px;max-height:460px;overflow:auto;}
    .tx-item{
      border-radius:10px;padding:9px 10px;
      background:#f7f7f7;border:1px solid #e2e2e2;font-size:13px;
      display:grid;grid-template-columns:1.4fr 1fr;column-gap:10px;row-gap:4px;
    }
    .tag{
      display:inline-block;padding:3px 8px;border-radius:999px;
      font-size:11px;background:#e4e4e4;color:#555;text-transform:uppercase;
      letter-spacing:0.04em;font-weight:600;
    }
    .strong{font-weight:600;color:var(--accent);}
    .muted{font-size:12px;color:var(--muted);}
  </style>
</head>
<body>
  <div class="shell">
    <div class="top-row">
      <div class="logo">INVEST</div>
      <a class="back-link" href="dashboard.php">← Назад в кабинет</a>
    </div>

    <section class="card">
      <h1>История транзакций</h1>
      <p>Пополнения баланса, заявки на вывод и другие операции по вашему аккаунту.</p>

      <?php if (count($transactions) === 0): ?>
        <p class="muted">Транзакций пока нет.</p>
      <?php else: ?>
        <div class="tx-list">
          <?php foreach ($transactions as $tx): ?>
            <div class="tx-item">
              <div>
                <div>
                  <span class="tag">
                    <?php
                      if ($tx["type"] === "deposit_balance") echo "Пополнение";
                      elseif ($tx["type"] === "withdraw_request") echo "Вывод";
                      else echo htmlspecialchars($tx["type"]);
                    ?>
                  </span>
                </div>
                <div style="margin-top:6px;">
                  <span class="muted">Сумма: </span>
                  <span class="strong"><?php echo number_format($tx["amount"], 2, '.', ' '); ?> USDT</span>
                </div>
                <?php if (!empty($tx["wallet"])): ?>
                  <div class="muted">Кошелёк: <?php echo htmlspecialchars($tx["wallet"]); ?></div>
                <?php endif; ?>
              </div>
              <div style="text-align:right;">
                <?php if (!empty($tx["status"])): ?>
                  <div class="muted">Статус: <?php echo htmlspecialchars($tx["status"]); ?></div>
                <?php endif; ?>
                <div class="muted"><?php echo htmlspecialchars($tx["date"]); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>
</body>
</html>
