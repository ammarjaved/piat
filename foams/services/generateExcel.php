<?php



require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


if(isset($_REQUEST['id'])){
    include('./connection.php');

    $id = $_REQUEST['id'];

        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $pdo = null;
 
        if($record){
           
            try{
                $spreadsheet = IOFactory::load('../../assets/Excel- template.xlsx');


                $sheet = $spreadsheet->getActiveSheet();


                $sheet->setCellValue('C8', $record['ba']);
                $sheet->setCellValue('D8', $record['jenis_sambungan']);
                $sheet->setCellValue('E8', $record['jenis_sn']);
                $sheet->setCellValue('F8', $record['no_sn']);
                $sheet->setCellValue('G8', $record['alamat']);
                $sheet->setCellValue('H8', $record['tarikh_siap']);
                $sheet->setCellValue('I8', $record['piat']);
                $sheet->setCellValue('J8', $record['nama_feeder_pillar']);
                $sheet->setCellValue('K8', $record['nama_jalan']);  
                $sheet->setCellValue('N8', $record['seksyen_dari']);
                $sheet->setCellValue('O8', $record['seksyen_ke']);
                $sheet->setCellValue('P8', $record['bil_saiz_tiang_a']);
                $sheet->setCellValue('Q8', $record['bil_saiz_tiang_b']);
                $sheet->setCellValue('R8', $record['bil_saiz_tiang_c']);
                $sheet->setCellValue('S8', $record['bil_jenis_spun']);
                $sheet->setCellValue('T8', $record['bil_jenis_konkrit']);
                $sheet->setCellValue('U8', $record['bil_jenis_besi']);
                $sheet->setCellValue('V8', $record['bil_jenis_kayu']);
                $sheet->setCellValue('X8', $record['abc_span_a']);
                $sheet->setCellValue('Y8', $record['abc_span_b']);
                $sheet->setCellValue('Z8', $record['abc_span_c']);  
                $sheet->setCellValue('AA8', $record['abc_span_d']);
                $sheet->setCellValue('AB8', $record['pvc_span_a']);
                $sheet->setCellValue('AC8', $record['pvc_span_b']);
                $sheet->setCellValue('AD8', $record['pvc_span_c']);
                $sheet->setCellValue('AE8', $record['bare_span_a']);
                $sheet->setCellValue('AF8', $record['bare_span_b']);
                $sheet->setCellValue('AG8', $record['bare_span_c']);
                $sheet->setCellValue('AH8', $record['jumlah_span']);
                $sheet->setCellValue('AI8', $record['talian_utama']);
                $sheet->setCellValue('AJ8', $record['bil_umbang']);
                $sheet->setCellValue('AK8', $record['bil_black_box']);
                $sheet->setCellValue('AL8', $record['bil_lvpt']);
                $sheet->setCellValue('AN8', $record['bilangan_serbis']);
                $sheet->setCellValue('AO8', $record['catatan']);



                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('../../assets/Excel/'.$record['no_sn'].'.xlsx');

                ob_end_clean();

                // Send headers
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $record['no_sn'] . '.xlsx"');
                header('Cache-Control: max-age=0');
                header('Content-Length: ' . filesize('../../assets/Excel/' . $record['no_sn'] . '.xlsx'));
    
                // Output the file
                readfile('../../assets/Excel/' . $record['no_sn'] . '.xlsx');
                exit;

            } catch (Exception $e) {
                header("location : ../index.php");
                exit;
                echo "Error: " . $e->getMessage();
            }

        }
}else{
    header("location : ../index.php");
    exit();
}


?>