<?php
require "db.php";
$id=(int)($_GET['id']??0);
$stmt=$conn->prepare("SELECT passenger_id,name,phone,email,created_at FROM passengers WHERE passenger_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$passenger=$stmt->get_result()->fetch_assoc();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Passenger Details</title><link rel="stylesheet" href="style.css"></head><body>
<?php require "header.php"; ?>
<main class="container">
<div class="page-head"><div class="eyebrow">Passenger Details</div><h1>Registered Passenger</h1><p>Complete information for the selected passenger.</p></div>
<?php if(!$passenger): ?>
<div class="panel"><div class="notice error">Passenger not found.</div><a class="btn btn-dark" href="passenger.php">Back to Registration</a></div>
<?php else: ?>
<div class="panel passenger-detail">
<div class="detail-id">PA-<?=str_pad($passenger['passenger_id'],5,'0',STR_PAD_LEFT)?></div>
<div class="detail-grid">
<div><small>Full Name</small><strong><?=e($passenger['name'])?></strong></div>
<div><small>Phone</small><strong><?=e($passenger['phone'])?></strong></div>
<div><small>Email</small><strong><?=e($passenger['email'])?></strong></div>
<div><small>Registration Date</small><strong><?=e($passenger['created_at'])?></strong></div>
</div>
<a class="btn btn-dark" href="passenger.php">← Back to Passengers</a>
</div>
<?php endif; ?>
</main>
<?php require "footer.php"; ?>
</body></html>
