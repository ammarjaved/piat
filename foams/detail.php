<?php include("header.php") ?>

<div class="d-flex justify-content-end ">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/piat/foams/">index</a></li>
    <li class="breadcrumb-item active" aria-current="page">detail</li>
  </ol>
</nav>
</div>

<div class="container shadow p-5 my-5 bg-white foam-1 mt-2"> 
<?php
       


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

      if(isset($_REQUEST['no_sn'])){
        include('./services/connection.php');
   
       $sn_no = $_REQUEST['no_sn'];
       
       
        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE no_sn = :sn_no  ");
        $stmt->bindParam(':sn_no',$sn_no);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        // print_r($record);
        // exit;
        
    
        if(!$record){
            echo "dfsdfsdf";
    
            header("location: ./index.php");
        exit;
        }

      }
    ?>
<h3 class="text-center">AD Service QR 2023</h3> 

    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                      <!-- TABLE # 1 -->
      <table class="table">  
      <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section A</caption>
            <thead>
                <th colspan="3" class="text-center">Section A</th>
                       
            </thead>
            <tbody>
            <tr>
              <th class="col-md-6">BA *<br> <span class="text-danger"></span></th>
              <td  colspan="2">
                <?php echo !empty($record['ba']) ? $record['ba'] : ''; ?>
                    
             </td>
            </tr>
            <tr>
                <th>Jenis SN *<br> <span class="text-danger"></span></th>
                <td > <?php echo $record['jenis_sn'] == "LKKK" ? '<span class="check">&#x2713; </span>' : ''; ?> <label for="jenis_sn_lkkk">LKKK</label></td>
                <td >  <?php echo $record['jenis_sn'] == "Express" ? '<span class="check">&#x2713; </span>' : ''; ?><label for="jenis_sn_express">Express</label></td>
            </tr>
            <tr>
                <th>Jenis Sambungan *<br> <span class="text-danger"></span></th>
                <td ><?php echo $record['jenis_sambungan'] == "OH" ? '<span class="check">&#x2713; </span>' : ''; ?><label for="jenis_sambungan_oh">OH/Combine Service</label></td>
                <td ><?php echo $record['jenis_sambungan'] == "UG" ? '<span class="check">&#x2713; </span>' : ''; ?> <label for="jenis_sambungan_ug">UG</label></td>
            </tr>
            <tr>
                <th>No. SN *<br> <span class="text-danger"></span></th>
                <td colspan="2">
               <?php echo !empty($record['no_sn']) ? $record['no_sn'] : ''; ?></td>
            </tr>
            <tr>
                <th>Alamat *<br> <span class="text-danger"></span></th>
                <td colspan="2"><?php echo !empty($record['alamat']) ? $record['alamat'] : ''; ?></td>
            </tr>
            <tr>
                <th>Tarikh Siap *<br> <span class="text-danger"></span></th>
                
                <td colspan="2"><?php echo $record['tarikh_siap'] ?></td>
            </tr>
            <tr>
                <th>PIAT *<br> <span class="text-danger"></span></th>
                <td >   <?php echo $record['piat'] == "yes" ? "<span class='check'>&#x2713; </span>" : ''; ?> <label for="piat_yes">Yes</label></td>
                <td > <?php echo $record['piat'] == "no" ? "<span class='check'>&#x2713; </span>" : ''; ?> <label for="piat_no">No</label></td>
            </tr>
            <tr>
                <th>Nama Pencawang / Nama Feeder Pillar *<br> <span  style="font-family: Harlow Solid Italic !important; font-size:13px">Jika LKKK Sahaja </span>  <br> <span class="text-danger"></span></th>
                    <td colspan="2"><?php echo $record['nama_feeder_pillar'] ?></td>
            </tr>
            <tr>
                <th>Nama Feeder / Nama Jalan *<br><span  style="font-family: Harlow Solid Italic !important; font-size:13px">Jika LKKK Sahaja </span>  <br> <span class="text-danger"></span></th>
                <td colspan="2" class="align-middle"><?php echo $record['nama_jalan'] ?></td>
            </tr>
             
            </tbody>

        </table>
    </div>            

    
                                                         <!-- TABLE # 2 -->

    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section B</caption>
            <thead>
                <th colspan="3" class="text-center">Seksyen</th>
            </thead>
            <tr>
                <th class="col-md-6">Dari (No Tiang / Nama Feeder)</th>
                <td colspan="2"><?php echo $record['seksyen_dari'] ?></td>
            </tr>

            <tr>
                <th>Ke (No Tiang/ Nama Feeder/ No Rumah/ No Kedai)</th>
                <td colspan="2"><?php echo $record['seksyen_ke'] ?></td>
            </tr>
        </table>
    </div>




                                                             <!-- TABLE # 3 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section C</caption>
            <thead>
                <th colspan="3" class="text-center">Bil Saiz Tiang</th>
            </thead>

            <tr>
                <th class="col-md-6">Bil Saiz Tiang 7.5</th>
                <td ><?php echo $record['bil_saiz_tiang_a'] ?></td>
            </tr>


            <tr>
                <th>Bil Saiz Tiang 9</th>
                <td ><?php echo $record['bil_saiz_tiang_b'] ?></td>
            </tr>
            <tr>
                <th>Bil Saiz Tiang 10</th>
                <td ><?php echo $record['bil_saiz_tiang_c'] ?></td>
            </tr>
        </table>
    </div>




                                                            <!-- TABLE # 4 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section D</caption>
            <thead>
                <th colspan="3" class="text-center">Bil Jenis Tiang</th>
            </thead>

            <tr>
                <th class="col-6">Span</th>
                <td ><?php echo $record['bil_jenis_spun'] ?></td>
            </tr>
            <tr>
                <th>Konkrit</th>
                <td ><?php echo $record['bil_jenis_konkrit'] ?></td>
            </tr>
            <tr>
                <th>Besi</th>
                <td ><?php echo $record['bil_jenis_besi'] ?></td>
            </tr>
            <tr>
                <th>Kayu</th>
                <td ><?php echo $record['bil_jenis_kayu'] ?></td>
            </tr>
            

        </table>
    </div>

                                                                <!-- TABLE # 5 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section E</caption>
            <thead>
                <th colspan="3" class="text-center">ABC (Span)</th>
            </thead>

            <tr>
                <th>3 X 185</th>
                <td > <?php echo $record['abc_span_a'] ?></td>
            </tr>

            <tr>
                <th>3 X 95</th>
                <td ><?php echo $record['abc_span_b'] ?></td>
            </tr>

            <tr>
                <th>3 X 16</th>
                <td ><?php echo $record['abc_span_c'] ?></td>
            </tr>

            <tr>
                <th class="col-6">1 X 16</th>
                <td ><?php echo $record['abc_span_d'] ?></td>
            </tr>

        </table>
    </div>

                                                                <!-- TABLE # 6 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section F</caption>
            <thead>
                <th colspan="3" class="text-center">PVC (Span)</th>
            </thead>
            <tr>
                <th>19/064</th>
                <td ><?php echo $record['pvc_span_a'] ?></td>
            </tr>
            <tr>
                <th>7/083 </th>
                <td ><?php echo $record['pvc_span_b'] ?></td>
            </tr>
            <tr>
                <th class="col-6">7/044</th>
                <td ><?php echo $record['pvc_span_c'] ?></td>
            </tr>
        </table>
    </div>

                                                                <!-- TABLE # 7 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-700 mb-2 text-left">Section G</caption>
            <thead>
                <th colspan="3" class="text-center">BARE (Span)</th>
            </thead>
            <tr>
                <th>7/173</th>
                <td ><?php echo $record['bare_span_a'] ?></td>
            </tr>
            <tr>
                <th>7/122</th>
                <td ><?php echo $record['bare_span_b'] ?></td>
            </tr>
            <tr>
                <th class="col-6">3/132</th>
                <td ><?php echo $record['bare_span_c'] ?></td>
            </tr>
        </table>
    </div>

    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-700 mb-2 text-left">Section H</caption>
            <thead>
                <th colspan="3" class="text-center">Section H</th>
            </thead>

            <tr>
                <th>Jumlah Span<br> <span class="text-danger"></span></th>
                <td colspan="2"><?php echo $record['jumlah_span'] ?></td>
            </tr>

            <tr>
                <th>Talian Utama (M) / Serbis (S)<br> <span class="text-danger"></span></th>
                <td><?php echo $record['talian_utama'] == 'M' ? '<span class="check">&#x2713; </span>': '' ?> <label for="talian_utama_m"> M</label></td>
                <td><?php echo $record['talian_utama'] == 'S' ? '<span class="check">&#x2713; </span>': ''?><label for="talian_utama_s"> S</label></td>
            </tr>
            <tr>
                <th>Bil Umbang <br> <span class="text-danger"></span></th>
                <td colspan="2"><?php echo $record['bil_umbang'] ?></td>
            </tr>
            <tr>
                <th>Bil Black Box<br> <span class="text-danger"></span></th>
                <td colspan="2"><?php echo $record['bil_black_box'] ?></td>
            </tr>
           <tr>
                <th>Bil LVPT Cap Bank<br> <span class="text-danger"></span></th>
                <td colspan="2"><?php echo $record['bil_lvpt'] ?></td>
            </tr>
            <tr>
                <th>Bilangan Serbis Melibatkan 1 pengguna sahaja<br> <span class="text-danger"></span></th>
                <td colspan="2"><?php echo $record['bilangan_serbis'] ?></td>
            </tr>
            <tr>
                <th class="col-6">Catatan<br> <span class="text-danger"></span></th>
                <td colspan="2"><?php echo $record['catatan'] ?></td>
            </tr> 

        </table>
    </div>
</div>

     <?php


     $sn_no = $_REQUEST['no_sn'];
     $stmt = $pdo->prepare("SELECT * FROM public.inspection_checklist WHERE project_no = :sn_no  ");
     $stmt->bindParam(':sn_no',$sn_no);
     $stmt->execute();
     $record = $stmt->fetch(PDO::FETCH_ASSOC);

     $pdo = null;
   

        if($record){
            
            $company = json_decode($record['company']); 
            $name = json_decode($record['company_name']);
            $phone_no = json_decode($record['company_phone_no']); 
            $sign = json_decode($record['company_sign']); 
            $check_list= json_decode($record['inspection_checklist']);


            ?>
       



    
     
<div class="container shadow p-5  my-5 bg-white foam-1"> 
    
    <h3 class="text-center">PIAT CHECKLIST LV OVERHEAD</h3>
    <form action="./services/submitFoam.php" method="post">

    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                      <!-- TABLE # 1 -->
      <table class="table">  

            <tr>
              <td class="">PRECOMMISSIONING, INSPECTION AND TESTING (PIAT) CHECKLIST</td>
              <td rowspan="2" class="col-2"><img src="../images/table-img-3.png"   class="img-res-2" alt=""></td>
            </tr>

            <tr>
              <td class="" >LOW VOLTAGE (LV) OVERHEAD LINES</td>
            </tr>

        </table>
    </div>                                                                          <!-- END TABLE # 1 -->
                  
      

                  


                     <!-- PROJECT DESCRIPTION # 1 -->
        <table class="table caption-top">

              <caption class="text-dark ">A. PROJECT DESCRIPTION</caption>
          <tbody> 
   
            <tr>
              <td class=""><label for=""><strong> PIAT Date : </strong></label> <?php echo !empty($record['piat_date']) ? $record['piat_date'] : ''; ?></td>
              <td class=""><label for="project-no"><strong> Project No : </strong></label> <?php echo !empty($record['project_no']) ? $record['project_no'] : ''; ?>
            </td>
            </tr>

            <tr>
              <td class="" colspan="2"><strong>Project Name : </strong><?php echo !empty($record['project_name']) ? $record['project_name'] : ''; ?></td>  
            </tr>
          
          </tbody>
        </table>
                                                                  <!-- END PROJECT DESCRIPTION # 1 -->



      <div class="table-responsive" style="overflow-y:auto ;" >                   <!-- PROJECT DESCRIPTION # 2 -->
        <table class="table">
          
              <tbody>
            <tr>
               <td class=""><strong> Feeder Circuit:  </strong><?php echo !empty($record['feeder_circuit']) ? $record['feeder_circuit'] : ''; ?></td>
               <td class=""><strong> From: </strong><?php echo !empty($record['feeder_circuit_from']) ? $record['feeder_circuit_from'] : ''; ?></td>
               <td class=""><strong> To: </strong><?php echo !empty($record['feeder_circuit_to']) ? $record['feeder_circuit_to'] : ''; ?></td>
               <td><strong> Length: </strong><?php echo !empty($record['feeder_circuit_length']) ? $record['feeder_circuit_length'] : ''; ?></td>
            </tr>

            <tr>
              <th class="">Voltage Level: </th>
              <td class=""><?php echo !empty($record['voltage_level']) ? $record['voltage_level'] : ''; ?></td>
              <th class="">Cable Type: </th>
              <td class="col-3"><?php echo !empty($record['cable_type']) ? $record['cable_type'] : ''; ?></td>
            </tr>
            
          </tbody>
        </table>
      </div>                                                                        <!-- END PROJECT DESCRIPTION # 2 -->


     <div class="text-end">

    </div>      <div class="table-responsive" style="overflow-y:auto ; width: 100%" >                     <!-- ATTENDANCE TABLE -->
       
        <table class="table table-responsive caption-top">
          <caption class=""> B. ATTENDANCE   </caption>
          <thead>
            <tr>
              <th class="">Unit / Department /
                Company </th>
              <th class="">Name</th>
              <th class="">Phone No</th>
              <th>Sign</th>

            </tr>
          </thead>
          <tbody >
            <?php 
            
              

                for($i = 0 ; $i < sizeof($company); $i++){
                    echo "<tr>";
                    echo "<td>{$company[$i]}</td>";
                    echo "<td>{$name[$i]}</td>";
                    echo "<td>{$phone_no[$i]}</td>";
                    echo "<td><strong > {$sign[$i]}</strong></td>";
                    echo "</tr>";
                }
                ?>
        
        
            
          
     
          </tbody>
        </table>
      </div>

      <div class="table-responsive"  id="for-remove-class" >
                            
        <table class="table caption-top">
          <caption class="">C. INSPECTION CHECKLIST</caption>
          <thead class="">
            <tr>
              <th class="">No.</th>
              <th class="" colspan="2">Description </th>
              <th class="">Pass</th>
              <th>Fail</th>
              <th>Not
                Applicable</th>
              <th>Category <br>
                MJ :Major <br>
                MN :Minor</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="" rowspan="4">1.</td>
              <td class="" rowspan="4">Pole
                Condition</td>
              <td class=""> a) Numbering as per manual.</td>
              <td class="text-center"> <?php echo $check_list['0'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['0'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['0'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MN
              </td>
            </tr>
            <tr>
              <td class="">b) Sufficient depth.</td>
              <td class="text-center"> <?php echo $check_list['1'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['1'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['1'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>c) Not Leaning
                <ul>
                  <li>Usage of kicking block at soft soil
                (paddy field/swamp/peat soil).</li>
                  <li>Usage of stay insulator at stay wire.</li>
                </ul>
                </td>
                <td class="text-center"> <?php echo $check_list['2'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['2'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['2'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MJ</td>
            </tr>
           
            <tr>
              <td>

                <div class="">
                  <table class="table caption-top">
                    <caption class="text-sm font-medium text-gray-500 mb-2 text-left">d) Maximum Span Distance</caption>
                    <thead>
                      <tr>
                        <th class="">Conductor
                          Size (mmp)
                          </th>
                        <th class="">Pole
                          Height (m)</th>
                        <th class="">Distance
                          (m)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="">3x185+16+
                          120
                          </td>
                        <td class="">9</td>
                        <td class="">35
                        </td>
                      </tr>
                      <tr>
                        <td class="">3x95+16+70</td>
                        <td class="">9</td>
                        <td class="">40
                        </td>
                      </tr>
                      <tr>
                        <td>3x16+25 </td>
                        <td>7.5</td>
                        <td>45</td>
                      </tr>
                      <tr>
                        <td>1x16+25</td>
                        <td>7.5</td>
                        <td>50</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
              <td class="text-center"> <?php echo $check_list['3'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['3'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['3'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>


            <tr>
              <td rowspan="3">2.</td>
              <td rowspan="3">Cable Clearance</td>
              <td>a) Minimum clearance from conductors
                to ground :
                <ul>
                  <li>5.18m (Roadside)</li>
                  <li>5.49m (Crossing road)</li>
                  <li>4.57m (Can't be access by vehicle)</li>
                </ul>
              </td>
              <td class="text-center"> <?php echo $check_list['4'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['4'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['4'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>b) SAVR free from encumbrances.
                (1m radius clearance)</td>
                <td class="text-center"> <?php echo $check_list['5'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['5'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['5'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MN
              </td>
            </tr>
            <tr>
              <td>c) Underground cable raised at pole to be
                covered in uPVC Class B.</td>
                <td class="text-center"> <?php echo $check_list['6'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['6'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['6'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>

            <tr>
              <td rowspan="2">3.</td>
              <td rowspan="2">Earthing</td>
              <td>a) Connection between earth cable and 
                neutral cable using IPC Bare ABC.
                </td>
                <td class="text-center"> <?php echo $check_list['7'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['7'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['7'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>

            <tr>
              
              <td>b) Proper install earthing wire at base 
                pole.
                
                </td>
                <td class="text-center"> <?php echo $check_list['8'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['8'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['8'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>4.</td>
              <td colspan="2">Lightning Arrester with earthing system (javelin rod).
                (must install at 1
                st pole, transition UG-ABC & end pole)
                </td>
                <td class="text-center"> <?php echo $check_list['9'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['9'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['9'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>5.</td>
              <td colspan="2"><div class="">
                <table class="table caption-top">
                  <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Right location of Black Box:</caption>
                  <thead>
                    <tr>
                      <th class="">Transformer
                        Size
                        </th>
                      <th class="">Conductor Size
                        (mm2
                        )</th>
                      <th class="">Black Box
                        Location</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="">100kVA
                        </td>
                      <td class="">16 / 95 / 185</td>
                      <td class="">Tiang 1
                      </td>
                    </tr>
                    <tr>
                      <td class="" rowspan="3">300kVA
                        H-Pole</td>
                      <td class="">16 </td>
                      <td class="">Tiang 1

                      </td>
                    </tr>
                    <tr>
                      <td>95</td>
                      <td>Tiang 2</td>
                    </tr>
                    <tr>
                      <td>185</td>
                      <td>Tiang 3
                      </td>
                    </tr>
                    <tr>
                      <td rowspan="2">300kVA
                        Ground Mounted
                        </td>
                      <td>16 / 95 </td>
                      <td>Tiang 2</td>
                    </tr>
                    <tr>
                      <td>185</td>
                      <td>Tiang 3
                      </td>
                    </tr>
                    <tr>
                      <td rowspan="3">500kVA</td>
                      <td>16</td>
                      <td>Tiang 2</td>
                    </tr>
                    <tr>
                      <td>95</td>
                      <td>Tiang 3
                      </td>
                    </tr>
                    <tr>
                      <td>185</td>
                      <td>Tiang 5
                      </td>
                    </tr>
                    <tr><td rowspan="3">750 kVA /
                      1000kVA
                      </td>
                    <td>16</td>
                    <td>Tiang 2</td></tr>
                    <tr>
                      <td>95 </td>
                      <td>Tiang 4</td>
                    </tr>
                    <tr>
                      <td>185</td>
                      <td>Tiang 6
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div></td>
               
              <td class="text-center"> <?php echo $check_list['10'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['10'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['10'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>

            <tr>
              <td >6.</td>
              <td colspan="2">Usage of Pre Insulated Connector (PIC) Mid Span for
                connection of Main to Main Line at pole.</td>
                <td class="text-center"> <?php echo $check_list['11'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['11'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['11'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td rowspan="4">7.</td>
              <td rowspan="4">Installation
                using
                Insulation
                Piercing
                Connector
                (IPC)</td>
              <td>a) Shear head break.</td>
              <td class="text-center"> <?php echo $check_list['12'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['12'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['12'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>b) Install end cap at the end cable.</td>
              <td class="text-center"> <?php echo $check_list['13'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['13'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['13'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>c) Sufficient numbers of IPC .
                (Phase : 1, Neutral : 2)
                </td>
                <td class="text-center"> <?php echo $check_list['14'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['14'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['14'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>d) Usage of cable tie.</td>
              <td class="text-center"> <?php echo $check_list['15'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['15'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['15'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td rowspan="3">8.</td>
              <td rowspan="3">Installation
                using Dead
                End Clamp</td>
              <td>a) Terminal Pole.</td>
              <td class="text-center"> <?php echo $check_list['16'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['16'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['16'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>b) Tiang Sudut bagi sudut peralihan > 45 darjah (ke depan) atau > 30 darjah (ke belakang)</td>
              <td class="text-center"> <?php echo $check_list['17'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['17'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['17'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>c) Tiang Pembahagian. </td>
              <td class="text-center"> <?php echo $check_list['18'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['18'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['18'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>9.</td>
              <td colspan="2">Connection between cable underground & overhead using Pre Insulated Connector Transition Type.</td>
              <td class="text-center"> <?php echo $check_list['19'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['19'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['19'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>10.</td>
              <td colspan="2">Usage of 3 unit nylon tie for each suspension clamp. <br>
            <img src="../images/table-img-1.png" class="img-res" height="200" alt="">
            </td>
            <td class="text-center"> <?php echo $check_list['20'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['20'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['20'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td>
            </tr>

            <tr><td rowspan="3">11.</td>
            <td rowspan="3">Conductors at Five-FootWay Main </td>
            <td>Conductors at Five-FootWay Main </td>
            <td class="text-center"> <?php echo $check_list['21'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['21'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['21'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
            <td>MJ</td></tr>
              <tr>
                <td>b) Usage of Cradle (two legged & three
                  legged) for dead-end clamp installation
                  for ABC.</td>
                  <td class="text-center"> <?php echo $check_list['22'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['22'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['22'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MJ
                </td>
              </tr>
              <tr>
                <td>c) Usage of PVC casing & shackle for PVC wire.</td>
                <td class="text-center"> <?php echo $check_list['23'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['23'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['23'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MJ
                </td>
              </tr>
              <tr>
                <td rowspan="5"> 12.</td>
                <td rowspan="5">Street
                  Lighting</td>
                <td>a) Installation of street light bracket must
                  be on top of SAVR (if SAVR installed at
                  same side)
                <br>
                <img src="../images/table-img-2.png" class="img-res" height="200" alt="">
              </td>
              <td class="text-center"> <?php echo $check_list['24'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['24'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['24'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MJ</td>
              </tr>
              <tr>
                <td>b) Usage of UV scotch tape at connector block.</td>
                <td class="text-center"> <?php echo $check_list['25'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['25'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['25'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MJ
                </td>
              </tr>
              <tr>
                <td>c) Usage of correct IPC size. </td>
                <td class="text-center"> <?php echo $check_list['26'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['26'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['26'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MJ
                </td>
              </tr>
              <tr>
                <td>d) Proper earthing connection for LED
                  lantern.</td>
                  <td class="text-center"> <?php echo $check_list['27'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['27'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['27'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MN
                </td>
              </tr>
              <tr>
                <td>e) Panel Meter securely locked.</td>
                <td class="text-center"> <?php echo $check_list['28'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['28'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['28'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MN
                </td>
              </tr>
              <tr><td rowspan="2">13.</td>
              <td rowspan="2">Test Result</td>
              <td>b) Continuity Test.</td>
              <td class="text-center"> <?php echo $check_list['29'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['29'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['29'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
              <td>MJ</td></tr>
              <tr>
                <td>a) Insulation Resistance Test.</td>
                <td class="text-center"> <?php echo $check_list['30'] =="pass" ? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['30'] =="fail"? '<span class="check">&#x2713;</span>' : '' ?>  </td>
              <td class="text-center"> <?php echo $check_list['30'] =="not_applicable"? '<span class="check">&#x2713;</span>' : '' ?> </td>
                <td>MJ</td>
              </tr>
          </tbody>
        </table>
     </div>

     <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
      <table class="table caption-top">  
        <caption class="text-dark ">D. RESULT</caption>
            <tr>
              <td class="" rowspan="2"> <?php echo $record['result'] == "pass" ? '<span class="check">&#x2713;</span>' : ''; ?>   <label for="result_pass"><strong>PASS</strong></label><br>(All Comply)</td>
              <td class="" rowspan="2"><?php echo $record['result'] == "conditional_pass" ? '<span class="check">&#x2713;</span>' : ''; ?>  <label for="result_conditional_pass"><strong>CONDITIONAL PASS</strong></label><br>(If any minor non-compliance)</td>
              <td class="" rowspan="2"> <?php echo $record['result'] == "fail"? '<span class="check">&#x2713;</span>' : ''; ?><label for="result_fail"><strong>FAIL</strong></label><br>(If any major non-compliance)              </td>
              
              <td ><strong> Attempt No.</strong></td>
            </tr>
            <tr>
              <td ><?php echo !empty($record['attempt_no']) ? $record['attempt_no'] : ''; ?></td>
            </tr>
            <tr>
              <td colspan="4"> <label for="prepare_by">Prepare By : </label>
              <div style="height: 70px;">
                        <span id="sign" style="font-family: Harlow Solid Italic !important; padding-left:20px"><?php echo !empty($record['prep_sign']) ? $record['prep_sign'] : ''; ?></span><br>

                        <span id="prep_name" style=" padding-left:10px"><?php echo !empty($record['prepare_by']) ? $record['prepare_by'] : ''; ?></span>
            </div>
            (Asset Development)
              <!-- <textarea name="prepare_by" id="prepare_by" cols="30" rows="6" class="form-control" style="border : 1px solid black !important"></textarea> -->
              </td>
            </tr>
          

        </table>
    </div> 

     <div class="text-center mt-b">
      
     </div>

    </form>
    </div>
</div>
    <?php }

      


?>

</div>
 
 

 
</body>
</html>