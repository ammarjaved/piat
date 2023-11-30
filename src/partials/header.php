<?php session_start(); 


if(!isset($_SESSION['user_name']) && !isset($_SESSION['user_id'])){
    header("location:../auth/login.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <title>AD KL SN, QR and PIAT Monitoring</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="../../assets/lib/dselect.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/style.css">
    <script src="../../assets/js/js.js"></script>
    
    <style>
         @media  (min-width: 2200px){
        .container, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
            /* min-width: 2000px !important; */
            max-width: 2000px !important;
        }
        }
    </style>
</head>
<body>


<?php include "navbar.php" ?>
