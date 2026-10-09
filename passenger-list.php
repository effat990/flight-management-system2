<?php
require "db.php";
$passengerRows=$conn->query("SELECT passenger_id,name,phone,email,created_at FROM passengers ORDER BY passenger_id DESC");
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Registered Passengers</title><link rel="stylesheet" href="style.css"></head><body>
<?php require "header.php"; ?>
<main class="container">
<div class="page-head"><div class="eyebrow">Passenger Records</div><h1>Registered Passengers</h1><p>View the passengers registered in the FLY Air system.</p></div>
<div class="panel panel-dark">
<div class="section-head"><div><h2>Passenger List</h2><p class="muted">Select View to open a passenger's complete details.</p></div><a class="btn btn-dark" href="passenger.php">← Register Passenger</a></div>
<div class="table-wrap">
<table class="table">
<tr><th>Passenger ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Registered At</th><th>Action</th></tr>
<?php if($passengerRows->num_rows===0): ?>
<tr><td colspan="6">No passengers registered yet.</td></tr>
<?php else: while($r=$passengerRows->fetch_assoc()): ?>
<tr>
<td>PA-<?=str_pad($r['passenger_id'],5,'0',STR_PAD_LEFT)?></td>
<td><?=e($r['name'])?></td>
<td><?=e($r['phone'])?></td>
<td><?=e($r['email'])?></td>
<td><?=e($r['created_at'])?></td>
<td><a class="btn btn-primary" href="passenger-view.php?id=<?=$r['passenger_id']?>">View</a></td>
</tr>
<?php endwhile; endif; ?>
</table>
</div>
</div>
</main>
<?php require "footer.php"; ?>
</body></html>
