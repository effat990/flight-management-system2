<?php
require "db.php";
$message="";

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $name=trim($_POST["name"]??"");
  $phone=trim($_POST["phone"]??"");
  $email=trim($_POST["email"]??"");
  $password=$_POST["password"]??"";

  if(!$name||!$phone||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<4){
    $message='<div class="notice error">Please enter valid information. Password must be at least 4 characters.</div>';
  }else{
    $hash=password_hash($password,PASSWORD_DEFAULT);
    $stmt=$conn->prepare("INSERT INTO passengers(name,phone,email,password) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$name,$phone,$email,$hash);
    if($stmt->execute()){
      $id=$stmt->insert_id;
      $message='<div class="notice success">Registration successful. Your Passenger ID is <strong>PA-'.$id.'</strong>.</div>';
    }else{
      $message='<div class="notice error">Registration failed. Email may already exist.</div>';
    }
  }
}

?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Passenger Registration</title><link rel="stylesheet" href="style.css"></head><body>
<?php require "header.php"; ?>
<main class="container passenger-page">
<div class="page-head"><div class="eyebrow">Passenger</div><h1>Passenger Registration</h1><p>Create a passenger account before booking a flight.</p></div>
<div class="panel">
<?= $message ?>
<form method="post">
<div class="grid grid-2">
<div class="field"><label>Full Name</label><input name="name" required placeholder="Enter full name"></div>
<div class="field"><label>Phone</label><input name="phone" required placeholder="01XXXXXXXXX"></div>
<div class="field"><label>Email</label><input type="email" name="email" required placeholder="name@example.com"></div>
<div class="field"><label>Password</label><input type="password" name="password" required placeholder="Create password"></div>
</div>
<div class="passenger-actions">
<button class="btn btn-primary" type="submit">Register Passenger</button>
<a class="btn btn-dark" href="passenger-list.php">View Registered Passengers</a>
</div>
</form>
</div>
</main>
<?php require "footer.php"; ?>
</body></html>
