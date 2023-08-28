<?php 

 

$stmt = $pdo->prepare("SELECT a.count ,b.total_count, c.count_sn , d.count_piat FROM 
(SELECT count(*) as count FROM ad_service_qr WHERE ba = :ba AND status = 'Inprocess')a,
(SELECT count(*) as total_count FROM ad_service_qr WHERE ba = :ba AND status = 'Complete' OR ba=:ba AND status = '1')b,
(SELECT count(*) as count_sn FROM ad_service_qr WHERE ba = :ba AND jenis_sambungan = 'UG' )c,
(SELECT count(*) as count_piat FROM ad_service_qr WHERE ba = :ba AND jenis_sambungan != 'UG')d");


$status = "Inprocess";
$stmt->bindParam(':ba', $_SESSION['user_ba']);

$stmt->execute();

$count = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<div class="row text-center">
    
    <div class="col-md-3 c bg-light m-2 p-2" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  Pending <span class="text-captalize"><?php echo $_SESSION['user_ba']?></span> </p>
        <div class="text-center"><?php echo $count['count']?></div>
    </div>
    <div class="col-md-3 c bg-light m-2 p-2" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  Complete <span class="text-captalize"><?php echo $_SESSION['user_ba']?></span></p>
        <div class="text-center"><?php echo $count['total_count']?></div>
    </div>

    <div class="col-md-2 bg-light m-2 p-2" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  SN </p>
        <div class="text-center"><?php echo $count['count_sn']?></div>
    </div>
    <div class="col-md-2 bg-light m-2 p-2" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  PIAT </p>
        <div class="text-center"><?php echo $count['count_piat']?></div>
    </div>

        
</div>


