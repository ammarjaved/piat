<?php 
ob_start();
    session_start();

    if(isset($_SESSION['sn']) || isset($_REQUEST['sn'])){

        include("connection.php");

        $sn = isset($_REQUEST['sn'])? $_REQUEST['sn'] : $_SESSION['sn'];

        if( isset($_SESSION['sn'])){
            unset($_SESSION['sn']);
        } 


        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE no_sn = :sn");
        $stmt->bindParam(':sn', $sn);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($record ) {

              
                if($record['jenis_sambungan'] == "OH"){

                    $bal = "";
                    if($record['ba'] == "KLB - 6121"){
                        $bal = "KLB";
                    }elseif ($record['ba'] == "KLT - 6122") {
                        $bal = "KLT";
                    } elseif ($record['ba'] == "KLP - 6123") {
                        $bal = "KLP";
                    }
                    elseif ($record['ba'] == "KLS - 6124") {
                        $bal = "KLS";
                    }
        
        
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE station = :bal");
                    $stmt->bindParam(':bal', $bal);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
         
                
                    $span_sum = 0;
                    $cable_type = '';
        
                    if ($record['abc_span_a'] != "") {
                        $span_sum += intval($record['abc_span_a']);
                        $cable_type .= 'ABC-3X185-'.$record['abc_span_a'];
                    }
                    if ($record['abc_span_b'] != "") {
                        $span_sum += intval($record['abc_span_b']);
                        $cable_type .= ', ABC-3X95-'.$record['abc_span_b'];
                    }
                    if ($record['abc_span_c'] != "") {
                        $span_sum += intval($record['abc_span_c']);
                        $cable_type .= ', ABC-3X16-'.$record['abc_span_c'];
                    }
                    if ($record['abc_span_d'] != "") {
                        $span_sum += intval($record['abc_span_d']);
                        $cable_type .= ', ABC-1X16-'.$record['abc_span_d'];
                    }
        
                    if ($record['pvc_span_a'] != "") {
                        $span_sum += intval($record['pvc_span_a']);
                        $cable_type .= ', PVC-19/064-'.$record['pvc_span_a'];
                    }
                    if ($record['pvc_span_b'] != "") {
                        $span_sum += intval($record['pvc_span_b']);
                        $cable_type .= ', PVC-7/083-'.$record['pvc_span_b'];
                    }
                    if ($record['pvc_span_c'] != "") {
                        $span_sum += intval($record['pvc_span_c']);
                        $cable_type .= ', PVC-7/044-'.$record['pvc_span_c'];
                    }
        
                    if ($record['bare_span_a'] != "") {
                        $span_sum += intval($record['bare_span_a']);
                        $cable_type .= ', BARE-7/173-'.$record['bare_span_a'];
                    }

                    if ($record['bare_span_b'] != "") {
                        $span_sum += intval($record['bare_span_b']);
                        $cable_type .= ', BARE-7/122-'.$record['bare_span_b'];
                    }

                    if ($record['bare_span_c'] != "") {
                        $span_sum += intval($record['bare_span_c']);
                        $cable_type .= ', BARE-3/132-'.$record['bare_span_c'];
                    }
        
                
        
                    $_SESSION['nama_jalan'] = $record['nama_jalan'];
                    $_SESSION['cable_type'] = $cable_type;
                    $_SESSION['span_sum'] = $span_sum;
                    $_SESSION['user_data'] = $rows;
                    $_SESSION['no_sn'] = $sn;
                    $_SESSION['alamat'] = $record['alamat'];
                    $_SESSION['tarikh_siap'] = $record['tarikh_siap'];
                    $_SESSION['sn_id'] = $record['id'];

                    $stmt = $pdo->prepare("SELECT * FROM public.inspection_checklist WHERE ad_service_id = :snId");
                    $stmt->bindParam(':snId', $sn_id['id']);
                    $stmt->execute();
                    $foam = $stmt->fetch(PDO::FETCH_ASSOC);
                    $pdo = null;
        
                    if($foam){
                        $_SESSION['foam'] = $foam;
                        header("Location: ../editFoam.php");
                    }else{
                        header("Location: ../foam.php")     ;
                    }
        
                    exit();
                }
            }

    }










?>