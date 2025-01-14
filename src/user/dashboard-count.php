<?php
$ba = $_SESSION['user_ba'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitButton']) && $_POST['submitButton'] == 'filter') {
    if (!isset($_POST['date_type'])) {
      
      $_SESSION['message'] = 'inserted failed';
       $_SESSION['alert'] = 'alert-danger';
    }else{
    $from = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to = isset($_POST['to_date']) ? $_POST['to_date'] : '';
    $date = '';

    if ($from == '' || $to == '') {
        // if dates are null and only ba is selected then first get min and max date
        $stmt = $pdo->prepare("SELECT MAX(tarikh_siap) AS max_date, MIN(tarikh_siap) AS min_date FROM public.ad_service_qr where tarikh_siap != '' and (status in ('Inprogress','KIV') or complete_date>='2025-01-01')");
        $stmt->execute();
        $comp_date = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT MAX(csp_paid_date) AS max_date, MIN(csp_paid_date) AS min_date FROM public.ad_service_qr where status in ('Inprogress','KIV') or complete_date>='2025-01-01'");
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
            $col_name = 'both';
           // $col_name = '';
        }
     }
  

     if ($col_name == 'both') {

          $baseQuery ="SELECT";
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
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND ((tarikh_siap >= :from_paid AND tarikh_siap <= :to_paid) OR (csp_paid_date >= :from_siap AND csp_paid_date <= :to_siap)) and (status in ('Inprogress','KIV') or complete_date>='2025-01-01') {$agingClause}) AS count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'Complete'  AND ((tarikh_siap >= :from_paid AND tarikh_siap <= :to_paid ) OR (csp_paid_date >= :from_siap AND csp_paid_date <= :to_siap)) and complete_date>='2025-01-01' {$agingClause}) AS complete_count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'Inprogress' AND ((tarikh_siap >= :from_paid AND tarikh_siap <= :to_paid)  OR (csp_paid_date >= :from_siap AND csp_paid_date <= :to_siap)) {$agingClause}) AS inprocess_count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'KIV' AND ((tarikh_siap >= :from_paid AND tarikh_siap <= :to_paid)  OR (csp_paid_date >= :from_siap AND csp_paid_date <= :to_siap)) {$agingClause}) AS kiv_piat"
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

// //Replace parameters in query
// foreach ($params as $param => $value) {
//     $query = str_replace($param, "'$value'", $query);
// }

// // Print the final query
// echo $query;
// exit();

   // Combine the query
   $query = $baseQuery . ' ' . implode(',', $subqueries);

   // Prepare and execute with proper binding
   $stmt = $pdo->prepare($query);


     $stmt->bindParam(':from_paid' ,$from_siap);
     $stmt->bindParam(':to_paid',$to_siap);
     $stmt->bindParam(':from_siap' ,$from_paid);
     $stmt->bindParam(':to_siap',$to_paid);
     $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);
    // $stmt->execute();
    if (isset($_POST['aging']) && $_POST['aging'] !== '' && $_POST['aging'] !== '>60') {
        $stmt->bindParam(':aging_min', $min, PDO::PARAM_INT);
        $stmt->bindParam(':aging_max', $max, PDO::PARAM_INT);
    }

    try {
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle error appropriately
        error_log("Query execution failed: " . $e->getMessage());
        throw $e;
    }

 
     }else{

         $baseQuery ="SELECT";
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
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND ".$col_name." >= :from AND ".$col_name." <= :to and (status in ('Inprogress','KIV') or complete_date>='2025-01-01') {$agingClause}) AS count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND (status = 'Complete' OR status = '1') AND ".$col_name." >= :from AND ".$col_name." <= :to and complete_date>='2025-01-01' {$agingClause}) AS complete_count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'Inprogress' AND ".$col_name." >= :from AND ".$col_name." <= :to {$agingClause}) AS inprocess_count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'KIV' AND ".$col_name." >= :from AND ".$col_name." <= :to {$agingClause}) AS kiv_piat"
         ];
         $query = $baseQuery . ' ' . implode(',', $subqueries);

         // Prepare and execute with proper binding
         $stmt = $pdo->prepare($query);
         $stmt->bindParam(':from', $from);
         $stmt->bindParam(':to', $to);
         $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);
         if (isset($_POST['aging']) && $_POST['aging'] !== '' && $_POST['aging'] !== '>60') {
            $stmt->bindParam(':aging_min', $min, PDO::PARAM_INT);
            $stmt->bindParam(':aging_max', $max, PDO::PARAM_INT);
        }
        
         try {
            //$stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);
           $stmt->execute();
           $count = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle error appropriately
            error_log("Query execution failed: " . $e->getMessage());
            throw $e;
        }


     }


}
} else {
    $baseQuery ="SELECT";

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
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba = :ba and (status in ('Inprogress','KIV') or complete_date>='2025-01-01') {$agingClause}) AS count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba = :ba AND (status = 'Complete' OR status = '1') and complete_date>='2025-01-01' {$agingClause}) AS complete_count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba = :ba AND status = 'Inprogress' {$agingClause}) AS inprocess_count",
        "(SELECT COUNT(*) FROM ad_service_qr WHERE ba = :ba AND status = 'KIV' {$agingClause}) AS kiv_piat"
    ];
    $query = $baseQuery . ' ' . implode(',', $subqueries);

    // Prepare and execute with proper binding
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':ba', $_SESSION['user_ba']);
    if (isset($_POST['aging']) && $_POST['aging'] !== '' && $_POST['aging'] !== '>60') {
        $stmt->bindParam(':aging_min', $min, PDO::PARAM_INT);
        $stmt->bindParam(':aging_max', $max, PDO::PARAM_INT);
    }

    try {
        $stmt->execute();    
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle error appropriately
        error_log("Query execution failed: " . $e->getMessage());
        throw $e;
    }
}


// $count = $stmt->fetch(PDO::FETCH_ASSOC);
 
?>

 


<div class="row text-center m-4">

    <div class="col-md-12   " onclick="adminSearch('<?php echo $_SESSION['user_ba'] ?>', '')" style="cursor: pointer;">
      
        <div class="mb-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600; " class="mb-2"> Total </p>
            <div class="text-center"><?php echo $count['count']; ?></div>
        </div>
    </div>
    <div class="col-md-4 " onclick="adminSearch('<?php echo $_SESSION['user_ba'] ?>','Complete')" style="cursor: pointer;">
    
        <div class=" m-2 p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total Complete </p>
            <div class="text-center"><?php echo $count['complete_count']; ?></div>
        </div>
     
    </div>

    <div class="col-md-4 " onclick="adminSearch('<?php echo $_SESSION['user_ba'] ?>','Inprogress')" style="cursor: pointer;">
    
        <div class=" mx-1 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total Inprogress </p>
            <div class="text-center"><?php echo $count['inprocess_count']; ?></div>
        </div>
 
    </div>
    <div class="col-md-4 " onclick="adminSearch('<?php echo $_SESSION['user_ba'] ?>','KIV')" style="cursor: pointer;">
 
        <div class="ml-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total KIV </p>
            <div class="text-center"><?php echo $count['kiv_piat']; ?></div>
        </div>
    
    </div>


</div>
