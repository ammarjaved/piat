<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submitButton'] == 'filter') {
    $dateString = "2025-01-01";
// $date = new DateTime($dateString);
// $formattedDate = $date->format('Y-m-d');
// echo $formattedDate;

    if (!isset($_POST['date_type'])) {

       

      
        $_SESSION['message'] = 'inserted failed';
         $_SESSION['alert'] = 'alert-danger';
      }else{

       
      $from = isset($_POST['from_date']) ? $_POST['from_date'] : '';
      $to = isset($_POST['to_date']) ? $_POST['to_date'] : '';
      $date = '';
  
      if ($from == '' || $to == '') {
          // if dates are null and only ba is selected then first get min and max date
          $stmt = $pdo->prepare("SELECT MAX(tarikh_siap) AS max_date, MIN(tarikh_siap) AS min_date FROM public.ad_service_qr where tarikh_siap != '' and (status in ('Inprogress','KIV') or complete_date>='2026-01-01' or tarikh_siap>='2026-01-01')");
          $stmt->execute();
          $comp_date = $stmt->fetch(PDO::FETCH_ASSOC);

          $stmt = $pdo->prepare("SELECT MAX(csp_paid_date) AS max_date, MIN(csp_paid_date) AS min_date FROM public.ad_service_qr where status in ('Inprogress','KIV') or complete_date>='2026-01-01' or tarikh_siap>='2026-01-01'");
          $stmt->execute();
          $csp_date = $stmt->fetch(PDO::FETCH_ASSOC);
      }
  
      if (isset($_POST['date_type'])) {
         if ($_POST['date_type'] == "CSP") {
              $col_name = 'csp_paid_date';
              $from = $from == '' ? $comp_date['min_date'] : $from;
              $to = $to == '' ? $comp_date['max_date'] : $to;
         }else if ($_POST['date_type'] == "Completion") {
            $col_name = 'tarikh_siap';
            $from = $from == '' ? $csp_date['min_date'] : $from;
            $to = $to == '' ? $csp_date['max_date'] : $to;
         }else{
            $from_siap = $from == '' ? $comp_date['min_date'] : $from;
              $to_siap = $to == '' ? $comp_date['max_date'] : $to; 
              $from_paid = $from == '' ? $csp_date['min_date'] : $from;
              $to_paid = $to == '' ? $csp_date['max_date'] : $to;
           //   echo  $from_siap.'-'.$to_siap.'-'.$from_paid.'-'.$to_paid;
            $col_name = 'both';
         }
      }


      $ba = $_POST['searchBA'];


      if ($col_name == 'both') {

        // echo json_encode($_POST);

     $baseQuery = "SELECT a.klb_count ,e.total_klb_count, b.klt_count,f.total_klt_count, c.klp_count , g.total_klp_count , d.kls_count , h.total_kls_count, i.kiv_klb_count,
        j.kiv_klt_count , k.kiv_klp_count , l.kiv_kls_count, m.count  FROM"; 

        // Handle aging clause
        $agingClause = "";
        if(isset($_POST['aging']) && $_POST['aging'] !== '') {
            if($_POST['aging'] === '>60') {
                $agingClause = "AND (CURRENT_DATE - csp_paid_date::date) > 60";
            } else {
                $range = explode(',', $_POST['aging']);
                $min = intval($range[0]);
                $max = intval($range[1]);
                $agingClause = "AND (CURRENT_DATE - csp_paid_date::date) BETWEEN :aging_min AND :aging_max";
            }
        }
    $subqueries = [
    "(SELECT count(*) as klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Inprogress' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)   OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) {$agingClause})a",
    "(SELECT count(*) as klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Inprogress' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)   OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) {$agingClause})b",
    "(SELECT count(*) as klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Inprogress' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)  OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) {$agingClause})c",
    "(SELECT count(*) as kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Inprogress' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)   OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) {$agingClause})d",
    "(SELECT count(*) as total_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Complete' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)   OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid))  and complete_date>='2026-01-01' {$agingClause})e",
    "(SELECT count(*) as total_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Complete' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)   OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) and complete_date>='2026-01-01' {$agingClause})f",
    "(SELECT count(*) as total_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Complete' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)   OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) and complete_date>='2026-01-01' {$agingClause})g",
    "(SELECT count(*) as total_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Complete' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)   OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) and complete_date>='2026-01-01' {$agingClause})h",
    "(SELECT count(*) as kiv_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'KIV' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap )  OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) {$agingClause})i",
    "(SELECT count(*) as kiv_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'KIV' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)   OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) {$agingClause})j",
    "(SELECT count(*) as kiv_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'KIV' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap )  OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) {$agingClause})k",
    "(SELECT count(*) as kiv_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'KIV' AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap )  OR  (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) {$agingClause})l",
    "(SELECT count(*) as count FROM ad_service_qr WHERE ba LIKE :ba  AND ((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)  OR (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)) and (status in ('Inprogress','KIV') or complete_date>='2026-01-01' or tarikh_siap>='2026-01-01') {$agingClause})m"
    ];
    
    
    // $params = [
    //     ':from_paid' => $from_paid,
    //     ':to_paid' => $to_paid,
    //     ':from_siap' => $from_siap,
    //     ':to_siap' => $to_siap,
    //     ':ba' => '%' . $ba . '%'
    // ];
    
    // // Get the query
    // $query = $stmt->queryString;
    
    // // Replace parameters in query
    // foreach ($params as $param => $value) {
    //     $query = str_replace($param, "'$value'", $query);
    // }
    
    // // Print the final query
    // echo $query;
    // exit();
    
    $query = $baseQuery . ' ' . implode(',', $subqueries);

    // Prepare and execute with proper binding
    $stmt = $pdo->prepare($query);

    
    $stmt->bindParam(':from_paid' ,$from_paid);
    $stmt->bindParam(':to_paid',$to_paid);
    $stmt->bindParam(':from_siap' ,$from_siap);
    $stmt->bindParam(':to_siap',$to_siap);
    $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);

    if (isset($_POST['aging']) && $_POST['aging'] !== '' && $_POST['aging'] !== '>60') {
        $stmt->bindParam(':aging_min', $min, PDO::PARAM_INT);
        $stmt->bindParam(':aging_max', $max, PDO::PARAM_INT);
    }

    try {
        // $stmt->execute();
        // $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle error appropriately
        error_log("Query execution failed: " . $e->getMessage());
        throw $e;
    }

       
    }else{

       
  

       
    //     $baseQuery="SELECT a.klb_count ,e.total_klb_count, b.klt_count,f.total_klt_count, c.klp_count , g.total_klp_count , d.kls_count , h.total_kls_count, i.kiv_klb_count,
    //     j.kiv_klt_count , k.kiv_klp_count , l.kiv_kls_count, m.count  FROM"; 
    //   $agingClause = "";

    //   // Add aging filter if selected
    //           if(isset($_POST['aging']) && $_POST['aging']!='') {
    //               if($_POST['aging'] === '>60') {
    //                   $agingClause = "AND (CURRENT_DATE - csp_paid_date::date) > 60";
    //               } else {
    //                   $range = explode(',', $_POST['aging']);
    //                   $min = intval($range[0]);
    //                   $max = intval($range[1]);
    //                   $agingClause = "AND (CURRENT_DATE - csp_paid_date::date) BETWEEN $min AND $max";
    //               }
    //           }  
          
    // $query = $baseQuery . "(SELECT count(*) as klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Inprogress' AND ".$col_name." >= :from AND ".$col_name." <= :to " . $agingClause . ")a,
    // (SELECT count(*) as klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Inprogress' AND ".$col_name." >= :from AND ".$col_name." <= :to " . $agingClause . ")b,
    // (SELECT count(*) as klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Inprogress' AND ".$col_name.">= :from AND ".$col_name." <= :to " . $agingClause . ")c,
    // (SELECT count(*) as kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Inprogress' AND ".$col_name." >= :from AND ".$col_name." <= :to " . $agingClause . ")d,
    // (SELECT count(*) as total_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Complete' AND ".$col_name.">= :from AND ".$col_name." <= :to and complete_date>='2025-01-01' " . $agingClause . ")e,
    // (SELECT count(*) as total_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Complete' AND ".$col_name." >= :from AND ".$col_name." <= :to and complete_date>='2025-01-01' " . $agingClause . ") f,
    // (SELECT count(*) as total_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Complete' AND ".$col_name." >= :from AND ".$col_name." <= :to and complete_date>='2025-01-01' " . $agingClause . ")g,
    // (SELECT count(*) as total_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Complete' AND ".$col_name." >= :from AND ".$col_name." <= :to and complete_date>='2025-01-01' " . $agingClause . ")h,
    
    // (SELECT count(*) as kiv_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'KIV' AND ".$col_name.">= :from AND ".$col_name." <= :to " . $agingClause . ")i,
    // (SELECT count(*) as kiv_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'KIV' AND ".$col_name." >= :from AND ".$col_name." <= :to " . $agingClause . ")j,
    // (SELECT count(*) as kiv_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'KIV' AND ".$col_name." >= :from AND ".$col_name." <= :to " . $agingClause . ")k,
    // (SELECT count(*) as kiv_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'KIV' AND ".$col_name." >= :from AND ".$col_name." <= :to " . $agingClause . ")l,
    // (SELECT count(*) as count FROM ad_service_qr WHERE ba LIKE :ba  AND ".$col_name." >= :from AND ".$col_name." <= :to and (status in ('Inprogress','KIV') or complete_date>='2025-01-01' " . $agingClause . ")m";
    // $stmt = $pdo->prepare($query);
    // $stmt->bindParam(':from' ,$from);
    // $stmt->bindParam(':to',$to);
    // $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);

    $baseQuery = "SELECT 
        a.klb_count, e.total_klb_count,
        b.klt_count, f.total_klt_count,
        c.klp_count, g.total_klp_count,
        d.kls_count, h.total_kls_count,
        i.kiv_klb_count, j.kiv_klt_count,
        k.kiv_klp_count, l.kiv_kls_count,
        m.count
    FROM";

    // Handle aging clause
    $agingClause = "";
    if(isset($_POST['aging']) && $_POST['aging'] !== '') {
        if($_POST['aging'] === '>60') {
            $agingClause = "AND (CURRENT_DATE - csp_paid_date::date) > 60";
        } else {
            $range = explode(',', $_POST['aging']);
            $min = intval($range[0]);
            $max = intval($range[1]);
            $agingClause = "AND (CURRENT_DATE - csp_paid_date::date) BETWEEN :aging_min AND :aging_max";
        }
    }

    // Create subqueries with proper parameter references
    $subqueries = [
        "(SELECT count(*) as klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Inprogress' AND {$col_name} >= :from AND {$col_name} <= :to {$agingClause})a",
        "(SELECT count(*) as klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Inprogress' AND {$col_name} >= :from AND {$col_name} <= :to {$agingClause})b",
        "(SELECT count(*) as klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Inprogress' AND {$col_name} >= :from AND {$col_name} <= :to {$agingClause})c",
        "(SELECT count(*) as kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Inprogress' AND {$col_name} >= :from AND {$col_name} <= :to {$agingClause})d",
        "(SELECT count(*) as total_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Complete' AND {$col_name} >= :from AND {$col_name} <= :to AND complete_date >= '2026-01-01' {$agingClause})e",
        "(SELECT count(*) as total_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Complete' AND {$col_name} >= :from AND {$col_name} <= :to AND complete_date >= '2026-01-01' {$agingClause})f",
        "(SELECT count(*) as total_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Complete' AND {$col_name} >= :from AND {$col_name} <= :to AND complete_date >= '2026-01-01' {$agingClause})g",
        "(SELECT count(*) as total_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Complete' AND {$col_name} >= :from AND {$col_name} <= :to AND complete_date >= '2026-01-01' {$agingClause})h",
        "(SELECT count(*) as kiv_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'KIV' AND {$col_name} >= :from AND {$col_name} <= :to {$agingClause})i",
        "(SELECT count(*) as kiv_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'KIV' AND {$col_name} >= :from AND {$col_name} <= :to {$agingClause})j",
        "(SELECT count(*) as kiv_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'KIV' AND {$col_name} >= :from AND {$col_name} <= :to {$agingClause})k",
        "(SELECT count(*) as kiv_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'KIV' AND {$col_name} >= :from AND {$col_name} <= :to {$agingClause})l",
        "(SELECT count(*) as count FROM ad_service_qr WHERE ba LIKE :ba AND {$col_name} >= :from AND {$col_name} <= :to AND (status in ('Inprogress','KIV') OR complete_date >= '2026-01-01' OR tarikh_siap >= '2026-01-01') {$agingClause})m"
    ];

    // Combine the query
    $query = $baseQuery . ' ' . implode(',', $subqueries);

    // Prepare and execute with proper binding
    $stmt = $pdo->prepare($query);

    // Bind the parameters
    $stmt->bindParam(':from', $from);
    $stmt->bindParam(':to', $to);
    $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);

    // Bind aging parameters if needed
    if (isset($_POST['aging']) && $_POST['aging'] !== '' && $_POST['aging'] !== '>60') {
        $stmt->bindParam(':aging_min', $min, PDO::PARAM_INT);
        $stmt->bindParam(':aging_max', $max, PDO::PARAM_INT);
    }

    try {
        // $stmt->execute();
        // $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle error appropriately
        error_log("Query execution failed: " . $e->getMessage());
        throw $e;
    }


    }
    } 
}else{
    $agingClause = "";

    // Add aging filter if selected
  
$baseQuery="SELECT a.klb_count ,e.total_klb_count, b.klt_count,f.total_klt_count, c.klp_count , g.total_klp_count , d.kls_count , h.total_kls_count , i.kiv_klb_count,
j.kiv_klt_count , k.kiv_klp_count , l.kiv_kls_count , m.count FROM"; 
$agingClause = "";

// Add aging filter if selected
if(isset($_POST['aging']) && $_POST['aging']!='') {
    if($_POST['aging'] === '>60') {
        $agingClause = "AND (CURRENT_DATE - csp_paid_date::date) > 60";
    } else {
        $range = explode(',', $_POST['aging']);
        $min = intval($range[0]);
        $max = intval($range[1]);
        $agingClause = "AND (CURRENT_DATE - csp_paid_date::date) BETWEEN $min AND $max";
    }
}
$query=$baseQuery."(SELECT count(*) as klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Inprogress' " . $agingClause . ")a,
(SELECT count(*) as klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Inprogress' " . $agingClause . ")b,
(SELECT count(*) as klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Inprogress' " . $agingClause . ")c,
(SELECT count(*) as kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Inprogress' " . $agingClause . ")d,
(SELECT count(*) as total_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'Complete' and complete_date>='2026-01-01' " . $agingClause . ")e,
(SELECT count(*) as total_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'Complete' and complete_date>='2026-01-01' " . $agingClause . ")f,
(SELECT count(*) as total_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'Complete' and complete_date>='2026-01-01' " . $agingClause . ")g,
(SELECT count(*) as total_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'Complete' and complete_date>='2026-01-01' " . $agingClause . ")h,
(SELECT count(*) as kiv_klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = 'KIV' " . $agingClause . " )i,
    (SELECT count(*) as kiv_klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = 'KIV' " . $agingClause . ")j,
    (SELECT count(*) as kiv_klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = 'KIV' " . $agingClause . ")k,
    (SELECT count(*) as kiv_kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = 'KIV' " . $agingClause . ")l,
    (SELECT count(*) as count FROM ad_service_qr  where status in ('Inprogress','KIV') or complete_date>='2026-01-01' or tarikh_siap>='2026-01-01' " . $agingClause . ")m";
    $stmt = $pdo->prepare($query);    
}
$status = "Inprocess";
// $stmt->bindParam(':status',$status);


$stmt->execute();

$count = $stmt->fetch(PDO::FETCH_ASSOC);

// OH pending stats per BA
$ohPendingStmt = $pdo->prepare("
    SELECT
        ba,
        COUNT(CASE WHEN (tarikh_siap IS NULL OR tarikh_siap = '') THEN 1 END) as no_qr,
        COUNT(CASE WHEN (piat_status IS NULL OR piat_status != 'true') THEN 1 END) as no_piat,
        COUNT(CASE WHEN (erms_status IS NULL OR erms_status != 'done') THEN 1 END) as no_erms,
        COUNT(*) as total_oh
    FROM public.ad_service_qr
    WHERE jenis_sambungan = 'OH'
    AND (status IN ('Inprogress','KIV') OR complete_date >= '2026-01-01' OR tarikh_siap >= '2026-01-01')
    GROUP BY ba
    ORDER BY ba
");
$ohPendingStmt->execute();
$ohPendingRows = $ohPendingStmt->fetchAll(PDO::FETCH_ASSOC);
$ohByBa = [];
foreach ($ohPendingRows as $row) {
    $ohByBa[$row['ba']] = $row;
}

?>

<div class="row text-center">
<div class="col-md-12   " onclick="adminSearch('' ,'')" style="cursor: pointer;">
      
      <div class="mb-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
          <p style="font-weight: 600; " class="mb-2"> Total </p>
          <div class="text-center"><?php echo $count['count']; ?></div>
      </div>
  </div>
    
    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLB - 6121' , 'Inprogress' ) ">
        <div class=" m-2 p-1" style="background-color:  #F86828 !important ;" >
            <p style="font-weight: 600;">Total  Inprogress KLB </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLB - 6121' && $_POST['searchBA'] != '' ? '0': $count['klb_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLB - 6121' , 'Complete' ) ">
        <div class=" m-2 p-1" style="background-color:   #F86828 !important;" >
            <p style="font-weight: 600;">Total  Complete KLB </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLB - 6121' && $_POST['searchBA'] != '' ? '0': $count['total_klb_count']?></div>
        </div> 
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLB - 6121' , 'KIV' ) ">
        <div class=" m-2 p-1" style="background-color:   #F86828 !important;">
            <p style="font-weight: 600;">Total  KIV KLB </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLB - 6121' && $_POST['searchBA'] != '' ? '0': $count['kiv_klb_count']?></div>
        </div>
    </div>
    
    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLT - 6122' , 'Inprogress' ) ">
        <div class=" m-2 p-1" style="background-color:  #92C400 !important;">
            <p style="font-weight: 600;">Total  Inprogress KLT </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLT - 6122' && $_POST['searchBA'] != '' ? '0': $count['klt_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLT - 6122' , 'Complete' ) ">
        <div class=" m-2 p-1" style="background-color:  #92C400 !important;">
            <p style="font-weight: 600;">Total  Complete KLT </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLT - 6122' && $_POST['searchBA'] != '' ? '0': $count['total_klt_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLT - 6122' , 'KIV' ) ">
        <div class=" m-2 p-1" style="background-color:  #92C400 !important;">
            <p style="font-weight: 600;">Total  KIV KLT </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLT - 6122' && $_POST['searchBA'] != '' ? '0': $count['kiv_klt_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLP - 6123' , 'Inprogress' ) ">
        <div class=" m-2 p-1" style="background-color:  #9e9e9e !important;">
            <p style="font-weight: 600;">Total  Inprogress KLP </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLP - 6123' && $_POST['searchBA'] != '' ? '0': $count['klp_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLP - 6123' , 'Complete' ) ">
        <div class=" m-2 p-1" style="background-color:  #9e9e9e !important;">
            <p style="font-weight: 600;">Total  Complete KLP </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLP - 6123' && $_POST['searchBA'] != '' ? '0': $count['total_klp_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLP - 6123' , 'KIV' ) ">
        <div class=" m-2 p-1" style="background-color:  #9e9e9e !important;">
            <p style="font-weight: 600;">Total  KIV KLP </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLP - 6123' && $_POST['searchBA'] != '' ? '0': $count['kiv_klp_count']?></div>
        </div>
    </div>
    
    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLS - 6124' , 'Inprogress' ) ">
        <div class=" m-2 p-1" style="background-color:  #FFC400 !important;">
            <p style="font-weight: 600;">Total Inprogress KLS </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLS - 6124' && $_POST['searchBA'] != '' ? '0': $count['kls_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLS - 6124' , 'Complete' ) ">
        <div class=" m-2 p-1" style="background-color: #FFC400  !important;">
            <p style="font-weight: 600;">Total Complete KLS </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLS - 6124' && $_POST['searchBA'] != '' ? '0': $count['total_kls_count']?></div>
        </div>
    </div>

    <div class="col-md-2 "  style="cursor: pointer;" onclick="adminSearch('KLS - 6124' , 'KIV' ) ">
        <div class=" m-2 p-1" style="background-color:  #FFC400  !important;">
            <p style="font-weight: 600;">Total KIV KLS </p>
            <div class="text-center"><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != 'KLS - 6124' && $_POST['searchBA'] != '' ? '0': $count['kiv_kls_count']?></div>
        </div>
    </div>

</div>

<!-- OH Pending Completion Stats -->
<div class="row mt-2 mb-2">
    <div class="col-12">
        <div style="background-color: #fff8e1; border: 1px solid #ffc107; border-radius: 6px; padding: 10px;">
            <p class="text-center mb-2" style="font-weight: 700; font-size: 0.9rem; color: #6d4c00;">OH — Pending QR / PIAT / ERMS (Active Records)</p>
            <table class="table table-sm table-bordered mb-0 text-center" style="font-size: 0.85rem;">
                <thead style="background-color: #6c757d; color: white;">
                    <tr>
                        <th>BA</th>
                        <th>No QR</th>
                        <th>No PIAT</th>
                        <th>No ERMS</th>
                        <th>Total OH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $baList = ['KLB - 6121', 'KLT - 6122', 'KLP - 6123', 'KLS - 6124'];
                    $baColors = ['KLB - 6121' => '#F86828', 'KLT - 6122' => '#92C400', 'KLP - 6123' => '#9e9e9e', 'KLS - 6124' => '#FFC400'];
                    foreach ($baList as $baKey):
                        $r = isset($ohByBa[$baKey]) ? $ohByBa[$baKey] : ['no_qr' => 0, 'no_piat' => 0, 'no_erms' => 0, 'total_oh' => 0];
                    ?>
                    <tr>
                        <td><strong style="color: <?php echo $baColors[$baKey]; ?>;"><?php echo $baKey; ?></strong></td>
                        <td class="<?php echo $r['no_qr'] > 0 ? 'text-danger fw-bold' : 'text-success'; ?>" style="cursor:pointer;" onclick="filterOHPending('no_qr','<?php echo $baKey; ?>')"><?php echo $r['no_qr']; ?></td>
                        <td class="<?php echo $r['no_piat'] > 0 ? 'text-danger fw-bold' : 'text-success'; ?>" style="cursor:pointer;" onclick="filterOHPending('no_piat','<?php echo $baKey; ?>')"><?php echo $r['no_piat']; ?></td>
                        <td class="<?php echo $r['no_erms'] > 0 ? 'text-danger fw-bold' : 'text-success'; ?>" style="cursor:pointer;" onclick="filterOHPending('no_erms','<?php echo $baKey; ?>')"><?php echo $r['no_erms']; ?></td>
                        <td style="cursor:pointer;" onclick="filterOHPending('total','<?php echo $baKey; ?>')"><strong><?php echo $r['total_oh']; ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


