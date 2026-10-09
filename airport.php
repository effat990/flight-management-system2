<?php
require "db.php";

$message = "";
$country = trim($_GET['country'] ?? '');
$city = trim($_GET['city'] ?? '');
$airport_id = (int)($_GET['airport_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_airport') {
    $airport_name = trim($_POST['airport_name'] ?? '');
    $new_city = trim($_POST['city'] ?? '');
    $new_country = trim($_POST['country'] ?? '');

    if (!$airport_name || !$new_city || !$new_country) {
        $message = '<div class="notice error">Please enter country, city and airport name.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO airports (airport_name, city, country) VALUES (?,?,?)");
        $stmt->bind_param("sss", $airport_name, $new_city, $new_country);
        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $message = '<div class="notice success">Airport added successfully. Airport ID: <strong>AP-'.str_pad((string)$new_id,5,'0',STR_PAD_LEFT).'</strong>.</div>';
            $country = $new_country;
            $city = $new_city;
            $airport_id = $new_id;
        } else {
            $message = '<div class="notice error">Could not add the airport. Please try again.</div>';
        }
    }
}

$countries=$conn->query("SELECT DISTINCT country FROM airports ORDER BY country");
$cities=[];
$airports=[];
if($country){
    $s=$conn->prepare("SELECT DISTINCT city FROM airports WHERE country=? ORDER BY city");
    $s->bind_param("s",$country); $s->execute(); $cities=$s->get_result();
}
if($country && $city){
    $s=$conn->prepare("SELECT * FROM airports WHERE country=? AND city=? ORDER BY airport_name");
    $s->bind_param("ss",$country,$city); $s->execute(); $airports=$s->get_result();
}
$selected=null;
if($airport_id){
    $s=$conn->prepare("SELECT * FROM airports WHERE airport_id=?");
    $s->bind_param('i',$airport_id); $s->execute(); $selected=$s->get_result()->fetch_assoc();
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Airport</title><link rel="stylesheet" href="style.css"></head><body>
<?php require "header.php"; ?>
<main class="container">
<div class="page-head"><div class="eyebrow">Airport Selection & Management</div><h1>Choose Your Airport</h1><p>Select a country, city and airport, or add a new airport to the system.</p></div>

<?= $message ?>

<div class="panel add-panel">
  <div class="section-head"><div><h2>Add Airport</h2><p class="muted">Add any country, city and airport. The Airport ID is generated automatically.</p></div></div>
  <form method="post">
    <input type="hidden" name="action" value="add_airport">
    <div class="grid grid-3">
      <div class="field"><label>Country</label><input name="country" required placeholder="e.g. Bangladesh"></div>
      <div class="field"><label>City</label><input name="city" required placeholder="e.g. Dhaka"></div>
      <div class="field"><label>Airport Name</label><input name="airport_name" required placeholder="e.g. Hazrat Shahjalal International Airport"></div>
    </div>
    <button class="btn btn-primary" type="submit">+ Add Airport</button>
  </form>
</div>

<div class="panel">
  <form method="get">
    <div class="grid grid-3">
      <div class="field"><label>Country</label><select name="country" onchange="this.form.submit()"><option value="">Select country</option><?php while($r=$countries->fetch_assoc()): ?><option value="<?=e($r['country'])?>" <?=$country===$r['country']?'selected':''?>><?=e($r['country'])?></option><?php endwhile;?></select></div>
      <div class="field"><label>City</label><select name="city" onchange="this.form.submit()"><option value="">Select city</option><?php foreach($cities as $r): ?><option value="<?=e($r['city'])?>" <?=$city===$r['city']?'selected':''?>><?=e($r['city'])?></option><?php endforeach;?></select></div>
      <div class="field"><label>Airport</label><select name="airport_id"><option value="">Select airport</option><?php foreach($airports as $r): ?><option value="<?=$r['airport_id']?>" <?=$airport_id==$r['airport_id']?'selected':''?>><?=e($r['airport_name'])?> — <?=e($r['airport_id'])?></option><?php endforeach;?></select></div>
    </div>
    <button class="btn btn-primary" type="submit">Confirm Airport</button>
  </form>
</div>

<?php if($selected): ?>
<div class="panel airport-confirm"><div><span class="eyebrow">Confirmed</span><h2><?=e($selected['airport_name'])?></h2><p><?=e($selected['city'])?>, <?=e($selected['country'])?> · Airport ID: <strong>AP-<?=str_pad($selected['airport_id'],5,'0',STR_PAD_LEFT)?></strong></p></div><a class="btn btn-primary" href="booking.php?airport_id=<?=$selected['airport_id']?>">Continue to Booking</a></div>
<?php endif; ?>

<?php if($airports && $airports->num_rows): ?>
<div class="panel"><h2>Airports in <?=e($city)?></h2><div class="table-wrap"><table class="table"><tr><th>ID</th><th>Airport</th><th>City</th><th>Country</th><th></th></tr><?php foreach($airports as $r): ?><tr><td>AP-<?=str_pad($r['airport_id'],5,'0',STR_PAD_LEFT)?></td><td><strong><?=e($r['airport_name'])?></strong></td><td><?=e($r['city'])?></td><td><?=e($r['country'])?></td><td><a class="btn btn-primary" href="airport.php?country=<?=urlencode($r['country'])?>&city=<?=urlencode($r['city'])?>&airport_id=<?=$r['airport_id']?>">Confirm</a></td></tr><?php endforeach;?></table></div></div>
<?php endif; ?>
</main><?php require "footer.php"; ?></body></html>
