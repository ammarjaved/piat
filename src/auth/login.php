<?php
session_start();
ob_start();
if( isset($_SESSION['user_name'])){
    header("location:../index.php");
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../services/connection.php'; 
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM auth_users WHERE name = :name');
    $stmt->bindParam(':name', $username);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record && password_verify($password, $record['password'])) {
        $_SESSION['user_name'] = $record['name'];
        $_SESSION['user_id'] = $record['id'];
        $_SESSION['user_ba'] = $record['ba'];
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid Username or Password";
        $_SESSION['alert'] = "alert-danger";
    }

    $pdo = null;
}


if (isset($_SESSION['message'])) {
    echo '<div class="alert ' . $_SESSION['alert'] . ' text-center" role="alert">';
    echo $_SESSION['message'];
    echo '<button type="button" class="close btn" onclick="this.parentNode.style.display = \'none\'">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';

    unset($_SESSION['message']);
    unset($_SESSION['alert']);
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <title>PIAT CHECKLIST LV OVERHEAD</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/style.css">
    <script src="../../assets/js/js.js"></script>
    
    
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="/src/index.php" style="font-weight: 600;"> Piat Check List</a>
 
  </div>
</nav>

<div class="container text-center   p-4  " style="width:100% !important; height:90vh">
<div class="text-center" style=" margin-top:100px ">
<div class="row p-2">
<div class="col-md-3"></div>
    <div class="card p-3 text-start col-md-6" >
        <div class="text-center"><img src="../../images/bbgg.png" alt="" srcset="" height="100"></div>
        <form action="" method="post">
        <label for="username" class="p-1" style="font-weight: 600;"> username</label>
        <input type="text" name="username" id="username" class="form-control" >
        <label for="password" class="p-1" style="font-weight: 600;">Password</label>
        <input type="password" name="password" class="form-control" id="password">
        <div class="text-center m-3">
            <button class="btn btn-sm btn-success">Login</button>
        </div>
        </form>
        </div>
        </div>
    </div>
</div>