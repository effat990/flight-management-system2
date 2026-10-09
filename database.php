<?php
require "db.php";

// Delete a paid boarding-pass record from the payment database.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_payment_id'])) {
    $deleteId = (int)$_POST['delete_payment_id'];
    if ($deleteId > 0) {
        $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
        $stmt->bind_param("i", $deleteId);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: database.php");
    exit;
}

// Search by passenger ID or passenger name.
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT pay.payment_id, p.passenger_id, p.name AS passenger_name
                            FROM payments pay
                            JOIN bookings b ON b.booking_id = pay.booking_id
                            JOIN passengers p ON p.passenger_id = b.passenger_id
                            WHERE CAST(p.passenger_id AS CHAR) LIKE ? OR p.name LIKE ?
                            ORDER BY pay.payment_id DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $rows = $stmt->get_result();
} else {
    $sql = "SELECT pay.payment_id, p.passenger_id, p.name AS passenger_name
            FROM payments pay
            JOIN bookings b ON b.booking_id = pay.booking_id
            JOIN passengers p ON p.passenger_id = b.passenger_id
            ORDER BY pay.payment_id DESC";
    $rows = $conn->query($sql);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Database - Boarding Pass Records</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>

<main class="container">
  <div class="page-head">
    <div class="eyebrow">Database Records</div>
    <h1>Boarding Pass Database</h1>
    <p>Only passengers who have completed a payment are shown here. Use <b>View</b> to see the complete boarding pass.</p>
  </div>

  <!-- Search bar -->
  <div class="panel">
    <form method="get" class="search-form">
      <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search by Passenger ID or Passenger Name" aria-label="Search by Passenger ID or Passenger Name">
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if ($search !== ''): ?>
        <a href="database.php" class="btn btn-secondary">Clear</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="panel panel-dark">
    <div class="section-head">
      <div><h2>Paid Passenger Records</h2></div>
      <span class="badge"><?= $rows ? $rows->num_rows : 0 ?> Record(s)</span>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>Passenger ID</th>
          <th>Passenger Name</th>
          <th>View</th>
          <th>Delete</th>
        </tr>

        <?php if ($rows && $rows->num_rows): ?>
          <?php while($r = $rows->fetch_assoc()): ?>
            <tr>
              <td>P<?= str_pad($r['passenger_id'], 6, '0', STR_PAD_LEFT) ?></td>
              <td><?= e($r['passenger_name']) ?></td>
              <td>
                <a class="btn btn-primary" href="boarding-pass-view.php?id=<?= e($r['payment_id']) ?>">View</a>
              </td>
              <td>
                <form method="post" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this boarding pass record?');">
                  <input type="hidden" name="delete_payment_id" value="<?= e($r['payment_id']) ?>">
                  <button type="submit" class="btn btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">
              <div class="notice info">
                <?= $search !== '' ? 'No passenger records match your search.' : 'No paid passenger records are available yet. Complete a booking payment first.' ?>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </div>
  </div>
</main>

<?php require "footer.php"; ?>
</body>
</html>
