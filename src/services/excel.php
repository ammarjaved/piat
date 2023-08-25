<?php 

    include("connection.php");
    require '../../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;

    $inputFileName = '../../assets/SN Completed Jan_June_OH.xlsx';

    $spreadsheet = IOFactory::load($inputFileName);
    $worksheet = $spreadsheet->getSheet(1);


    for ($row = 2; $row < 1342; $row++) {

        $sn = $worksheet->getCell('B'.$row)->getValue();
        $jenis = $worksheet->getCell('J'.$row)->getValue();

        if($jenis == "LKKK Process"){
            $jen = "LKKK";
        }else{
            $jen = "Express";
        }

        $stmt = $pdo->prepare("UPDATE public.ad_service_qr SET jenis_sn = :jenis WHERE no_sn = :sn ");
        $stmt->bindParam(':jenis',$jen);
        $stmt->bindParam(':sn', $sn);
        // $stmt->execute();
        

            echo $sn."  ".$jenis."<br>";
    }


?>