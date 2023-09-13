<?php 

 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submitButton'] == 'filter') {
    if (!isset($_POST['date_type'])) {
      
        $_SESSION['message'] = 'inserted failed';
         $_SESSION['alert'] = 'alert-danger';
      }else{
      $from = isset($_POST['from_date']) ? $_POST['from_date'] : '';
      $to = isset($_POST['to_date']) ? $_POST['to_date'] : '';
      $date = '';
  
      if ($from == '' || $to == '') {
          // if dates are null and only ba is selected then first get min and max date
          $stmt = $pdo->prepare("SELECT MAX(tarikh_siap) AS max_date, MIN(tarikh_siap) AS min_date FROM public.ad_service_qr where tarikh_siap != ''");
          $stmt->execute();
          $comp_date = $stmt->fetch(PDO::FETCH_ASSOC);
  
          $stmt = $pdo->prepare("SELECT MAX(csp_paid_date) AS max_date, MIN(csp_paid_date) AS min_date FROM public.ad_service_qr ");
          $stmt->execute();
          $csp_date = $stmt->fetch(PDO::FETCH_ASSOC);
      }
  
      if (isset($_POST['date_type'])) {
         if ($_POST['date_type'] == "CSP") {
              $col_name = 'csp_paid_date';
              $from = $from == '' ? $comp_date['min_date'] : $from;
              $to = $to == '' ? $comp_date['max_date'] : $to;
         }else{
          $col_name = 'tarikh_siap';
          $from = $from == '' ? $csp_date['min_date'] : $from;
          $to = $to == '' ? $csp_date['max_date'] : $to;
         }
      }
  
  

        $stmt = $pdo->prepare("SELECT a.klb_count ,e.total_klb_count, b.klt_count,f.total_klt_count, c.klp_count , g.total_klp_count , d.kls_count , h.total_kls_count, i.kiv_klb_count,
        j.kiv_klt_count , k.kiv_klp_count , l.kiv_kls_count, m.count  FROM 
    (SELECT count(*) as klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Inprogress' AND ".$col_name." >= :from AND ".$col_name." <= :to)a,
    (SELECT count(*) as klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Inprogress' AND ".$col_name." >= :from AND ".$col_name." <= :to)b,
    (SELECT count(*) as klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Inprogress' AND ".$col_name.">= :from AND ".$col_name." <= :to)c,
    (SELECT count(*) as kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Inprogress' AND ".$col_name." >= :from AND ".$col_name." <= :to)d,
    (SELECT count(*) as total_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Complete' AND ".$col_name.">= :from AND ".$col_name." <= :to)e,
    (SELECT count(*) as total_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Complete' AND ".$col_name." >= :from AND ".$col_name." <= :to)f,
    (SELECT count(*) as total_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Complete' AND ".$col_name." >= :from AND ".$col_name." <= :to)g,
    (SELECT count(*) as total_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Complete' AND ".$col_name." >= :from AND ".$col_name." <= :to)h,
    
    (SELECT count(*) as kiv_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'KIV' AND ".$col_name.">= :from AND ".$col_name." <= :to)i,
    (SELECT count(*) as kiv_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'KIV' AND ".$col_name." >= :from AND ".$col_name." <= :to)j,
    (SELECT count(*) as kiv_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'KIV' AND ".$col_name." >= :from AND ".$col_name." <= :to)k,
    (SELECT count(*) as kiv_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'KIV' AND ".$col_name." >= :from AND ".$col_name." <= :to)l,
    (SELECT count(*) as count FROM ad_service_qr WHERE  ".$col_name." >= :from AND ".$col_name." <= :to)m");
    $stmt->bindParam(':from' ,$from);
    $stmt->bindParam(':to',$to);

    }
}else{

$stmt = $pdo->prepare("SELECT a.klb_count ,e.total_klb_count, b.klt_count,f.total_klt_count, c.klp_count , g.total_klp_count , d.kls_count , h.total_kls_count , i.kiv_klb_count,
j.kiv_klt_count , k.kiv_klp_count , l.kiv_kls_count , m.count FROM 
(SELECT count(*) as klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Inprogress')a,
(SELECT count(*) as klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Inprogress')b,
(SELECT count(*) as klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Inprogress')c,
(SELECT count(*) as kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Inprogress')d,
(SELECT count(*) as total_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Complete')e,
(SELECT count(*) as total_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Complete' )f,
(SELECT count(*) as total_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Complete' )g,
(SELECT count(*) as total_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Complete' )h,
(SELECT count(*) as kiv_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'KIV'  )i,
    (SELECT count(*) as kiv_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'KIV'  )j,
    (SELECT count(*) as kiv_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'KIV'  )k,
    (SELECT count(*) as kiv_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'KIV'  )l,
    (SELECT count(*) as count FROM ad_service_qr  )m");
}
$status = "Inprocess";
// $stmt->bindParam(':status',$status);

$stmt->execute();

$count = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<div class="row text-center">
<div class="col-md-12   " onclick="adminSearch('' ,'')" style="cursor: pointer;">
      
      <div class="mb-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
          <p style="font-weight: 600; " class="mb-2"> Total </p>
          <div class="text-center"><?php echo $count['count']; ?></div>
      </div>
  </div>
    
    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLB - 6121' , 'Inprogress' ) ">
        <div class=" m-2 p-1" style="background-color:  #006a4cb0 !important ;" >
            <p style="font-weight: 600;">Total  Inprogress KLB </p>
            <div class="text-center"><?php echo $count['klb_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLB - 6121' , 'Complete' ) ">
        <div class=" m-2 p-1" style="background-color:   #006a4cb0 !important;" >
            <p style="font-weight: 600;">Total  Complete KLB </p>
            <div class="text-center"><?php echo $count['total_klb_count']?></div>
        </div> 
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLB - 6121' , 'KIV' ) ">
        <div class=" m-2 p-1" style="background-color:   #006a4cb0 !important;">
            <p style="font-weight: 600;">Total  KIV KLB </p>
            <div class="text-center"><?php echo $count['kiv_klb_count']?></div>
        </div>
    </div>
    
    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLT - 6122' , 'Inprogress' ) ">
        <div class=" m-2 p-1" style="background-color:  #8bc34a !important;">
            <p style="font-weight: 600;">Total  Inprogress KLT </p>
            <div class="text-center"><?php echo $count['klt_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLT - 6122' , 'Complete' ) ">
        <div class=" m-2 p-1" style="background-color:  #8bc34a !important;">
            <p style="font-weight: 600;">Total  Complete KLT </p>
            <div class="text-center"><?php echo $count['total_klt_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLT - 6122' , 'KIV' ) ">
        <div class=" m-2 p-1" style="background-color:  #8bc34a !important;">
            <p style="font-weight: 600;">Total  KIV KLT </p>
            <div class="text-center"><?php echo $count['kiv_klt_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLP - 6123' , 'Inprogress' ) ">
        <div class=" m-2 p-1" style="background-color:  #9e9e9e !important;">
            <p style="font-weight: 600;">Total  Inprogress KLP </p>
            <div class="text-center"><?php echo $count['klp_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLP - 6123' , 'Complete' ) ">
        <div class=" m-2 p-1" style="background-color:  #9e9e9e !important;">
            <p style="font-weight: 600;">Total  Complete KLP </p>
            <div class="text-center"><?php echo $count['total_klp_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLP - 6123' , 'KIV' ) ">
        <div class=" m-2 p-1" style="background-color:  #9e9e9e !important;">
            <p style="font-weight: 600;">Total  KIV KLP </p>
            <div class="text-center"><?php echo $count['kiv_klp_count']?></div>
        </div>
    </div>
    
    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLS - 6124' , 'Inprogress' ) ">
        <div class=" m-2 p-1" style="background-color:  #009688 !important;">
            <p style="font-weight: 600;">Total Inprogress KLS </p>
            <div class="text-center"><?php echo $count['kls_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLS - 6124' , 'Complete' ) ">
        <div class=" m-2 p-1" style="background-color: #009688  !important;">
            <p style="font-weight: 600;">Total Complete KLS </p>
            <div class="text-center"><?php echo $count['total_kls_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLS - 6124' , 'KIV' ) ">
        <div class=" m-2 p-1" style="background-color:  #009688  !important;">
            <p style="font-weight: 600;">Total KIV KLS </p>
            <div class="text-center"><?php echo $count['kiv_kls_count']?></div>
        </div>
    </div>
    
</div>


