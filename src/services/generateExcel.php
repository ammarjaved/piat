<?php
ob_start();
session_start();
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;



if(!isset($_SESSION['user_name'])){
    header("location:../index.php");
}
 include('./connection.php');


 if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if($_SESSION['user_name']== 'admin'){
$ba = isset($_POST['exc_ba']) ? $_POST['exc_ba'] : ''; 
    }else{
        $ba = $_SESSION['user_ba'];
    }
      
                        
          $from = isset($_POST['exc_from']) ? $_POST['exc_from'] : '';
          $to = isset($_POST['exc_to']) ? $_POST['exc_to'] : '';
          $record ='';
          if( $from == '' || $to == ''){
            $stmt = $pdo->prepare("SELECT MAX(tarikh_siap) , MIN(tarikh_siap) FROM public.ad_service_qr ");
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
          }
          $from = $from == '' ? $record['min'] : $from;
          $to = $to == '' ? $record['max'] : $to;
          
          $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba 
                    AND tarikh_siap::date >= :from AND tarikh_siap::date <= :to"); 
                    
                    
          $stmt->execute([':ba' => "%$ba%",':from' => $from,':to' => $to,]); 
        
 }elseif(isset($_REQUEST['id'])){

     $id = $_REQUEST['id'];
     $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE id = :id");
     $stmt->bindParam(':id', $id);
     $stmt->execute();
     
  } else{
 
     $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr ");
     $stmt->execute();
  }
 
   

  

       
        
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
        $pdo = null;
 
        if($records){
            $name = isset($_REQUEST['id']) ? $records[0]['no_sn'] : "All Sn";
			
            try{
			
                $spreadsheet = IOFactory::load('../../assets/Excel- template.xlsx');
  


                $sheet = $spreadsheet->getActiveSheet();
                $i = 8 ;
				
                foreach( $records as $record){
                $sheet->setCellValue('B'.$i, $i-7);
                $sheet->setCellValue('C'.$i, $record['ba']);
                $sheet->setCellValue('D'.$i, $record['jenis_sambungan']);
                $sheet->setCellValue('E'.$i, $record['jenis_sn']);
                $sheet->setCellValue('F'.$i, $record['no_sn']);
                $sheet->setCellValue('G'.$i, $record['alamat']);
                $sheet->setCellValue('H'.$i, $record['tarikh_siap']);
                $sheet->setCellValue('I'.$i, $record['piat']);
                $sheet->setCellValue('J'.$i, $record['nama_feeder_pillar']);
                $sheet->setCellValue('K'.$i, $record['nama_jalan']);  
                $sheet->setCellValue('N'.$i, $record['seksyen_dari']);
                $sheet->setCellValue('O'.$i, $record['seksyen_ke']);
                $sheet->setCellValue('P'.$i, $record['bil_saiz_tiang_a']);
                $sheet->setCellValue('Q'.$i, $record['bil_saiz_tiang_b']);
                $sheet->setCellValue('R'.$i, $record['bil_saiz_tiang_c']);
                $sheet->setCellValue('S'.$i, $record['bil_jenis_spun']);
                $sheet->setCellValue('T'.$i, $record['bil_jenis_konkrit']);
                $sheet->setCellValue('U'.$i, $record['bil_jenis_besi']);
                $sheet->setCellValue('V'.$i, $record['bil_jenis_kayu']);
                $sheet->setCellValue('X'.$i, $record['abc_span_a']);
                $sheet->setCellValue('Y'.$i, $record['abc_span_b']);
                $sheet->setCellValue('Z'.$i, $record['abc_span_c']);  
                $sheet->setCellValue('AA'.$i, $record['abc_span_d']);
                $sheet->setCellValue('AB'.$i, $record['pvc_span_a']);
                $sheet->setCellValue('AC'.$i, $record['pvc_span_b']);
                $sheet->setCellValue('AD'.$i, $record['pvc_span_c']);
                $sheet->setCellValue('AE'.$i, $record['bare_span_a']);
                $sheet->setCellValue('AF'.$i, $record['bare_span_b']);
                $sheet->setCellValue('AG'.$i, $record['bare_span_c']);
                $sheet->setCellValue('AH'.$i, $record['jumlah_span']);
                $sheet->setCellValue('AI'.$i, $record['talian_utama']);
                $sheet->setCellValue('AJ'.$i, $record['bil_umbang']);
                $sheet->setCellValue('AK'.$i, $record['bil_black_box']);
                $sheet->setCellValue('AL'.$i, $record['bil_lvpt']);
                $sheet->setCellValue('AN'.$i, $record['bilangan_serbis']);
                $sheet->setCellValue('AO'.$i, $record['catatan']);
                $i++;

                }

                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('../../assets/Excel/'.$name .'.xlsx');

                 ob_end_clean();

                // Send headers
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
                header('Cache-Control: max-age=0');
                header('Content-Length: ' . filesize('../../assets/Excel/' . $name . '.xlsx'));
    
                // Output the file
                readfile('../../assets/Excel/' . $name . '.xlsx');
                exit;

            } catch (Exception $e) {
                header("location : ../index.php");
                exit;
                echo "Error: " . $e->getMessage();
            }

        }



?>