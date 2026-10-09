<?php
require_once __DIR__ . "/db.php";
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="topbar">Welcome to Project</div>
<header class="nav">
  <div class="nav-inner">
    <a class="logo" href="index.php"><span class="logo-icon">✈</span>FLY Air</a>
    <nav class="nav-links">
      <a class="<?= $current==='index.php'?'active':'' ?>" href="index.php">Home</a>
      <a class="<?= $current==='passenger.php'?'active':'' ?>" href="passenger.php">Passenger Registration</a>
      <a class="<?= $current==='flights.php'?'active':'' ?>" href="flights.php">Flights</a>
      <a class="<?= $current==='airport.php'?'active':'' ?>" href="airport.php">Airport</a>
      <a class="<?= $current==='booking.php'?'active':'' ?>" href="booking.php">Booking</a>
      <a class="<?= $current==='payment.php'?'active':'' ?>" href="payment.php">Payment</a>
      <a class="<?= in_array($current,['database.php','boarding-pass-view.php'])?'active':'' ?>" href="database.php">Database</a>
    </nav>
  </div>
</header>
