<?php
require "db.php";

$message = "";
$search = trim($_GET['search'] ?? '');
$airline = trim($_GET['airline'] ?? '');
$route = trim($_GET['route'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_flight') {
    $delete_id = (int)($_POST['flight_id'] ?? 0);

    if ($delete_id > 0) {
        $check = $conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE flight_id=?");
        $check->bind_param("i", $delete_id);
        $check->execute();
        $used = (int)$check->get_result()->fetch_assoc()['total'];

        if ($used > 0) {
            $message = '<div class="notice error">This flight cannot be deleted because it already has booking records. Cancel/remove those bookings first.</div>';
        } else {
            $delete = $conn->prepare("DELETE FROM flights WHERE flight_id=?");
            $delete->bind_param("i", $delete_id);
            if ($delete->execute() && $delete->affected_rows > 0) {
                $message = '<div class="notice success">Flight deleted successfully.</div>';
            } else {
                $message = '<div class="notice error">Flight could not be deleted.</div>';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_flight') {
    $flight_name = trim($_POST['flight_name'] ?? '');
    $route_new = trim($_POST['route'] ?? '');
    $departure_date = $_POST['departure_date'] ?? '';
    $departure_time = $_POST['departure_time'] ?? '09:00';
    $boarding_time = $_POST['boarding_time'] ?? '08:15';
    $gate_no = trim($_POST['gate_no'] ?? '');
    $total_seats = max(1, (int)($_POST['total_seats'] ?? 180));
    $travel_time = trim($_POST['travel_time'] ?? '');
    $base_fare = (float)($_POST['base_fare'] ?? 0);

    if (!$flight_name || !$route_new || !$departure_date || !$gate_no || !$travel_time || $base_fare < 0) {
        $message = '<div class="notice error">Please complete all flight fields with valid information.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO flights (flight_name, route, departure_date, departure_time, boarding_time, gate_no, total_seats, seats_available, travel_time, base_fare) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssiisd", $flight_name, $route_new, $departure_date, $departure_time, $boarding_time, $gate_no, $total_seats, $total_seats, $travel_time, $base_fare);
        if ($stmt->execute()) {
            $message = '<div class="notice success">Flight added successfully. Flight ID: <strong>FL-'.str_pad((string)$stmt->insert_id,5,'0',STR_PAD_LEFT).'</strong>.</div>';
            $search = $airline = $route = '';
        } else {
            $message = '<div class="notice error">Could not add the flight. Please try again.</div>';
        }
    }
}

$airlines = $conn->query("SELECT DISTINCT flight_name FROM flights ORDER BY flight_name");
$routes = $conn->query("SELECT DISTINCT route FROM flights ORDER BY route");

$sql = "SELECT * FROM flights WHERE seats_available > 0";
$params = [];
$types = '';

if ($search !== '') {
    $like = '%' . $search . '%';
    $sql .= " AND (flight_name LIKE ? OR route LIKE ?)";
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}
if ($airline !== '') {
    $sql .= " AND flight_name = ?";
    $params[] = $airline;
    $types .= 's';
}
if ($route !== '') {
    $sql .= " AND route LIKE ?";
    $params[] = '%' . $route . '%';
    $types .= 's';
}
$sql .= " ORDER BY departure_date, departure_time, flight_id";

$stmt = $conn->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$flights = $stmt->get_result();

function flight_id_code($id) {
    return 'FL-' . str_pad((string)$id, 5, '0', STR_PAD_LEFT);
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Flights</title><link rel="stylesheet" href="style.css"></head><body>
<?php require "header.php"; ?>
<main class="container">
<div class="page-head"><div class="eyebrow">Flight Search & Management</div><h1>Find Your Flight</h1><p>Search any airline or route, or add a new flight with your own route and schedule.</p></div>

<?= $message ?>

<div class="panel">
  <div class="section-head">
    <div><h2>Search Flights</h2><p class="muted">Search works with a full or partial airline name or route.</p></div>
  </div>
  <form method="get" class="grid grid-3">
    <div class="field"><label>Search Airline or Route</label><input type="text" name="search" value="<?=e($search)?>" placeholder="e.g. Dhaka, Dubai, Qatar Airways"></div>
    <div class="field"><label>Airline</label><select name="airline"><option value="">All airlines</option><?php while($r=$airlines->fetch_assoc()): ?><option value="<?=e($r['flight_name'])?>" <?=$airline===$r['flight_name']?'selected':''?>><?=e($r['flight_name'])?></option><?php endwhile;?></select></div>
    <div class="field"><label>Route</label><select name="route"><option value="">All routes</option><?php while($r=$routes->fetch_assoc()): ?><option value="<?=e($r['route'])?>" <?=$route===$r['route']?'selected':''?>><?=e($r['route'])?></option><?php endwhile;?></select></div>
    <div class="filter-actions"><button class="btn btn-primary" type="submit">Search Flights</button><a class="btn btn-dark" href="flights.php">Reset</a></div>
  </form>
</div>

<div class="panel add-panel">
  <div class="section-head">
    <div><h2>Add Flight</h2><p class="muted">Create any flight and route. The Flight ID is generated automatically.</p></div>
  </div>
  <form method="post">
    <input type="hidden" name="action" value="add_flight">
    <div class="grid grid-3">
      <div class="field"><label>Airline / Flight Name</label><input name="flight_name" required placeholder="e.g. Qatar Airways"></div>
      <div class="field"><label>Route</label><input name="route" required placeholder="e.g. Dhaka to Doha"></div>
      <div class="field"><label>Departure Date</label><input type="date" name="departure_date" value="<?=date('Y-m-d')?>" required></div>
      <div class="field"><label>Departure Time</label><input type="time" name="departure_time" value="09:00" required></div>
      <div class="field"><label>Boarding Time</label><input type="time" name="boarding_time" value="08:15" required></div>
      <div class="field"><label>Gate</label><input name="gate_no" value="A01" required></div>
      <div class="field"><label>Total Seats</label><input type="number" name="total_seats" min="1" value="180" required></div>
      <div class="field"><label>Travel Time</label><input name="travel_time" value="1h 00m" required></div>
      <div class="field"><label>Base Fare (BDT)</label><input type="number" name="base_fare" min="0" step="0.01" value="5000" required></div>
    </div>
    <button class="btn btn-primary" type="submit">+ Add Flight</button>
  </form>
</div>

<div class="flight-cards">
<?php if($flights->num_rows===0): ?><div class="panel"><div class="notice info">No available flights match your search.</div></div><?php endif; ?>
<?php while($r=$flights->fetch_assoc()): ?>
<article class="flight-card">
  <div class="flight-top"><div><span class="airline-chip">✈ <?=e($r['flight_name'])?></span><h2><?=e($r['route'])?></h2></div><div class="flight-id"><?=e(flight_id_code($r['flight_id']))?></div></div>
  <div class="flight-meta">
    <div><small>Departure</small><b><?=e($r['departure_date'])?> · <?=e(date('h:i A', strtotime($r['departure_time'])))?></b></div>
    <div><small>Travel time</small><b><?=e($r['travel_time'] ?: '—')?></b></div>
    <div><small>Available seats</small><b class="seat-count"><?=e($r['seats_available'])?></b></div>
    <div><small>Fare from</small><b>BDT <?=number_format((float)$r['base_fare'],2)?></b></div>
  </div>
  <div class="flight-bottom">
  <span class="badge">Seats available: <?=e($r['seats_available'])?></span>
  <div>
    <a class="btn btn-primary" href="booking.php?flight_id=<?=$r['flight_id']?>">Continue to Booking</a>
    <form method="post" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this flight?');">
      <input type="hidden" name="action" value="delete_flight">
      <input type="hidden" name="flight_id" value="<?=$r['flight_id']?>">
      <button class="btn btn-danger" type="submit">Delete</button>
    </form>
  </div>
</div>
</article>
<?php endwhile; ?>
</div>
</main><?php require "footer.php"; ?></body></html>
