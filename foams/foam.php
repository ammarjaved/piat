<?php
  include("header.php");
 
      if (!isset($_SESSION['no_sn'])) {

        header("location: ./foam-1.php");
        
        exit();  
      }
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
                  
      

                  


    <div class="table-responsive" style="overflow-y:auto " >                     <!-- PROJECT DESCRIPTION # 1 -->
        <table class="table caption-top">

              <caption class="text-dark ">A. PROJECT DESCRIPTION</caption>
          <tbody> 
   
            <tr>
              <td class=""><label for=""><strong> PIAT Date : </strong></label> <input type="date" name="piat_date" id="piat_date" value="<?php echo isset($_SESSION['tarikh_siap']) ? $_SESSION['tarikh_siap'] : '' ?>"></td>
              <td class=""><label for="project-no"><strong> Project No : </strong></label> <span id="project_no" name="project_no"  type="text" value=""><?php echo isset($_SESSION['no_sn']) ? $_SESSION['no_sn'] : '' ?></span>
            <input  id="project_no" name="project_no"  type="hidden" value="<?php echo isset($_SESSION['no_sn']) ? $_SESSION['no_sn'] : '' ?>">
            </td>
            </tr>

            <tr>
              <td class="" colspan="2"><strong>Project Name : </strong><label for=""><?php echo isset($_SESSION['alamat']) ? $_SESSION['alamat'] : '' ?></label><input type="hidden" name="project_name" id="project_name" value="<?php echo isset($_SESSION['alamat']) ? $_SESSION['alamat'] : '' ?>"></td>  
            </tr>
          
          </tbody>
        </table>
      </div>                                                                      <!-- END PROJECT DESCRIPTION # 1 -->



      <div class="table-responsive" style="overflow-y:auto ;" >                   <!-- PROJECT DESCRIPTION # 2 -->
        <table class="table">
          
              <tbody>
            <tr>
               <th class=""> Feeder Circuit:   <input type="text" name="feeder_circuit" id="feeder_circuit" value="<?php echo isset($_SESSION['nama_jalan']) ? $_SESSION['nama_jalan'] : '' ?>"></th>
               <th class=""> From: <input type="date" name="feeder_circuit_from" id="feeder_circuit_from" class="datepicker" ></th>
               <th class=""> To: <input type="date" name="feeder_circuit_to" id="feeder_circuit_to" class="datepicker"></th>
               <th> Length: <input type="text" name="feeder_circuit_length" id="feeder_circuit_length" value="<?php echo isset($_SESSION['span_sum']) ? $_SESSION['span_sum'] : '' ?>"></th>
            </tr>

            <tr>
              <th class="">Voltage Level: </th>
              <td class=""><input type="text" name="voltage_level" id="voltage_level" class="" value="240"></td>
              <th class="">Cable Type: </th>
              <td class="col-3"><span><?php echo isset($_SESSION['cable_type']) ? $_SESSION['cable_type'] : '' ?></span> <input type="hidden" name="cable_type" id="cable_type" value="<?php echo isset($_SESSION['cable_type']) ? $_SESSION['cable_type'] : '' ?>"></td>
            </tr>
            
          </tbody>
        </table>
      </div>                                                                        <!-- END PROJECT DESCRIPTION # 2 -->


     <div class="text-end">
      <button id="add-row-btn" class="btn btn-sm btn-success" type="button" onclick="addRow()">Add Row</button>
    </div>      
    
    <div class="table-responsive" style="overflow-y:auto ;" >                     <!-- ATTENDANCE TABLE -->
       
        <table class="table table-responsive caption-top">
          <caption class=""> B. ATTENDANCE   </caption>
          <thead>
            <tr>
              <th class="">Unit / Department /
                Company </th>
              <th class="">Name</th>
              <th class="">Phone No</th>
              <th>Sign</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody id="table-body">
          
            
            <!-- Add more table rows as needed -->
          </tbody>
        </table>
      </div>

      <div class="table-responsive"   >
                            
        <table class="table caption-top">
          <caption class="">C. INSPECTION CHECKLIST</caption>
          <thead class="">
            <tr>
              <th class="" rowspan="2">No.</th>
              <th class="" colspan="2" rowspan="2">Description </th>
              <th class="">Pass </th>
              
              <th>Fail  </th>
              
              <th>Not Applicable </th>
              
              <th rowspan="2">Category <br>
                MJ :Major <br>
                MN :Minor</th>

            </tr>
            <tr>
              <th class="text-center"><input type="radio" name="select_all" id="all_pass" value="pass"></th>
              <th class="text-center"><input type="radio" name="select_all" id="all_fail" value="fail"></th>
              <th class="text-center"><input type="radio" name="select_all" id="all_not_applicable" value="not_applicable"></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="" rowspan="4">1.</td>
              <td class="" rowspan="4">Pole
                Condition</td>
              <td class=""> a) Numbering as per manual.</td>
              <td class="text-center"><input type="radio" name="check_list[0]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[0]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[0]" value="not_applicable" id=""></td>
              <td>MN
              </td>
            </tr>
            <tr>
              <td class="">b) Sufficient depth.</td>
              <td class="text-center"><input type="radio" name="check_list[1]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[1]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[1]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>c) Not Leaning
                <ul>
                  <li>Usage of kicking block at soft soil (paddy field/swamp/peat soil).</li>
                  <li>Usage of stay insulator at stay wire.</li>
                </ul>
                </td>
                <td class="text-center"><input type="radio" name="check_list[2]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[2]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[2]" value="not_applicable" id=""></td>
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
              <td class="text-center"><input type="radio" name="check_list[3]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[3]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[3]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>


            <tr>
              <td rowspan="3">2.</td>
              <td rowspan="3">Cable
                Clearance</td>
              <td>a) Minimum clearance from conductors to ground :
                <ul>
                  <li>5.18m (Roadside)</li>
                  <li>5.49m (Crossing road)</li>
                  <li>4.57m (Can't be access by vehicle)</li>
                </ul>
              </td>
              <td class="text-center"><input type="radio" name="check_list[4]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[4]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[4]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>b) SAVR free from encumbrances. (1m radius clearance)</td>
              <td class="text-center"><input type="radio" name="check_list[5]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[5]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[5]" value="not_applicable" id=""></td>
              <td>MN
              </td>
            </tr>
            <tr>
              <td>c) Underground cable raised at pole to be covered in uPVC Class B.</td>
              <td class="text-center"><input type="radio" name="check_list[6]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[6]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[6]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>

            <tr>
              <td rowspan="2">3.</td>
              <td rowspan="2">Earthing</td>
              <td>a) Connection between earth cable and neutral cable using IPC Bare ABC. </td>
              <td class="text-center"><input type="radio" name="check_list[7]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[7]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[7]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>

            <tr>
              
              <td>b) Proper install earthing wire at base  pole. </td>
              <td class="text-center"><input type="radio" name="check_list[8]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[8]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[8]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>4.</td>
              <td colspan="2">Lightning Arrester with earthing system (javelin rod). (must install at 1  st pole, transition UG-ABC & end pole) </td>
              <td class="text-center"><input type="radio" name="check_list[9]" value="pass" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[9]" value="fail" id=""></td>
              <td class="text-center"><input type="radio" name="check_list[9]" value="not_applicable" id=""></td>
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
               
              <td class="text-center"><input type="radio" name="check_list[10]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[10]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[10]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>

            <tr>
              <td >6.</td>
              <td colspan="2">Usage of Pre Insulated Connector (PIC) Mid Span for
                connection of Main to Main Line at pole.</td>
                <td class="text-center"><input type="radio" name="check_list[11]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[11]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[11]" value="not_applicable" id=""></td>
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
              <td class="text-center"><input type="radio" name="check_list[12]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[12]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[12]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>b) Install end cap at the end cable.</td>
              <td class="text-center"><input type="radio" name="check_list[13]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[13]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[13]" value="not_applicable" id=""></td>
                <td>MJ</td>
            </tr>
            <tr>
              <td>c) Sufficient numbers of IPC .
                (Phase : 1, Neutral : 2)
                </td>
                <td class="text-center"><input type="radio" name="check_list[14]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[14]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[14]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>d) Usage of cable tie.</td>
              <td class="text-center"><input type="radio" name="check_list[15]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[15]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[15]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td rowspan="3">8.</td>
              <td rowspan="3">Installation
                using Dead
                End Clamp</td>
              <td>a) Terminal Pole.</td>
              <td class="text-center"><input type="radio" name="check_list[16]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[16]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[16]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>b) Tiang Sudut bagi sudut peralihan > 45
                darjah (ke depan) atau > 30 darjah (ke
                belakang)</td>
                <td class="text-center"><input type="radio" name="check_list[17]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[17]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[17]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>c) Tiang Pembahagian. </td>
              <td class="text-center"><input type="radio" name="check_list[18]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[18]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[18]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>9.</td>
              <td colspan="2">Connection between cable underground & overhead
                using Pre Insulated Connector Transition Type.</td>
              
                <td class="text-center"><input type="radio" name="check_list[19]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[19]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[19]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>
            <tr>
              <td>10.</td>
              <td colspan="2">Usage of 3 unit nylon tie for each suspension clamp. <br>
            <img src="../images/table-img-1.png" class="img-res" height="200" alt="">
            </td>
               
            <td class="text-center"><input type="radio" name="check_list[20]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[20]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[20]" value="not_applicable" id=""></td>
              <td>MJ</td>
            </tr>

            <tr><td rowspan="3">11.</td>
            <td rowspan="3">Conductors at Five-FootWay Main </td>
            <td>Conductors at Five-FootWay Main </td>
            <td class="text-center"><input type="radio" name="check_list[21]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[21]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[21]" value="not_applicable" id=""></td>
            <td>MJ</td></tr>
              <tr>
                <td>b) Usage of Cradle (two legged & three
                  legged) for dead-end clamp installation
                  for ABC.</td>
                  <td class="text-center"><input type="radio" name="check_list[22]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[22]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[22]" value="not_applicable" id=""></td>
                <td>MJ
                </td>
              </tr>
              <tr>
                <td>c) Usage of PVC casing & shackle for PVC
                  wire.</td>
                  <td class="text-center"><input type="radio" name="check_list[23]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[23]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[23]" value="not_applicable" id=""></td>
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
              <td class="text-center"><input type="radio" name="check_list[24]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[24]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[24]" value="not_applicable" id=""></td>
                <td>MJ</td>
              </tr>
              <tr>
                <td>b) Usage of UV scotch tape at connector
                  block.</td>
                  <td class="text-center"><input type="radio" name="check_list[25]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[25]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[25]" value="not_applicable" id=""></td>
                <td>MJ</td>
                
              </tr>
              <tr>
                <td>c) Usage of correct IPC size. </td>
                <td class="text-center"><input type="radio" name="check_list[26]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[26]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[26]" value="not_applicable" id=""></td>
                <td>MJ
                </td>
              </tr>
              <tr>
                <td>d) Proper earthing connection for LED
                  lantern.</td>
                  <td class="text-center"><input type="radio" name="check_list[27]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[27]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[27]" value="not_applicable" id=""></td>
                <td>MN
                </td>
              </tr>
              <tr>
                <td>e) Panel Meter securely locked.</td>
                <td class="text-center"><input type="radio" name="check_list[28]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[28]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[28]" value="not_applicable" id=""></td>
                <td>MN
                </td>
              </tr>
              <tr><td rowspan="2">13.</td>
              <td rowspan="2">Test Result</td>
              <td>b) Continuity Test.</td>
              <td class="text-center"><input type="radio" name="check_list[29]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[29]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[29]" value="not_applicable" id=""></td>
              <td>MJ</td></tr>
              <tr>
                <td>a) Insulation Resistance Test.</td>
                <td class="text-center"><input type="radio" name="check_list[30]" value="pass" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[30]" value="fail" id=""></td>
                <td class="text-center"><input type="radio" name="check_list[30]" value="not_applicable" id=""></td>
                <td>MJ</td>
              </tr>
          </tbody>
        </table>
     </div>

     <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
      <table class="table caption-top">  
        <caption class="text-dark ">D. RESULT</caption>
            <tr>
              <td class="" rowspan="2"><input type="radio" name="result" id="result_pass" value="pass"> <label for="result_pass"><strong>PASS</strong></label><br>(All Comply)</td>
              <td class="" rowspan="2"><input type="radio" name="result" id="result_conditional_pass" value="conditional_pass"> <label for="result_conditional_pass"><strong>CONDITIONAL PASS</strong></label><br>(If any minor non-compliance)</td>
              <td class="" rowspan="2"><input type="radio" name="result" id="result_fail" value="false"> <label for="result_fail"><strong>FAIL</strong></label><br>(If any major non-compliance)              </td>
              
              <td ><strong> Attempt No.</strong></td>
            </tr>
            <tr>
              <td ><input type="text" name="attempt_no" id="" value="1"></td>
            </tr>
            <tr>
              <td colspan="4"> <label for="prepare_by">Prepare By : </label> <select name="prepare_by" id="prepare_by" onchange="prepareBy(this)" style="margin-bottom: 13px !important">
                      <option value="" hidden>Select User</option>
                      <?php
                        if (isset($_SESSION['user_data'])) {
                          foreach ($_SESSION['user_data'] as $user) {
                            ?>
                            <option value="<?php echo $user['name'] ?>"><?php echo $user['name']; ?></option>
                            <?php
                          }
                        }
                      ?>

              </select>
              <div style="height: 70px;">
                        <span id="sign" style="font-family: Harlow Solid Italic !important; padding-left:20px"></span><br>
                        <input type="hidden" id="prep_sign" name="prep_sign">
                        <span id="prep_name" style=" padding-left:10px"></span>
            </div>
            (Asset Development)
              <!-- <textarea name="prepare_by" id="prepare_by" cols="30" rows="6" class="form-control" style="border : 1px solid black !important"></textarea> -->
              </td>
            </tr>
          

        </table>
    </div> 

     <div class="text-center mt-b">
      <button type="submit" class="btn btn-sm btn-success">Submit</button>
     </div>

    </form>
    </div>

      
      
      
</div>



</body>
<script>
  var check = '';
  var prepare = true;
  $('document').ready(function() {

    addRow();
 
    $(".datepicker").val(new Date().toISOString().slice(0, 10));

    $('input[name="select_all"]').click(function() {
      if(this.value == check){
        $('input[name^="check_list"]').filter(`:radio[value="${this.value}"]`).prop('checked', false);
        $(`#${this.id}`).prop('checked',false);
        check = '';
      }else{
        $('input[name^="check_list"]').filter(`:radio[value="${this.value}"]`).prop('checked', true);
        $(`#${this.id}`).prop('checked',true);
        check = `${this.value}`;
      }
      });

      $('input[name^="check_list"]').click(function() {
        if(this.value !== check){
        $('#all_pass, #all_fail, #all_not_applicable').prop('checked', false);
        check = '';
      }
      });
   
    
  });

  function addRow() {
    var users = <?php echo json_encode($_SESSION['user_data']); ?>;
    const tableBody = document.getElementById('table-body');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
      <td class=""><input type="text" name="company[]" value="" id="company"></td>
      <td class="">
        <select name="company_name[]" id="company_name" class="form-select" onchange="userChange(this)" style="min-width:150px !important">
          <option hidden>Select Name</option>
          ${
            users.map((user) => {
              return `<option value="${user.name}">${user.name}</option>`;
            })
          }
        </select>
      </td>
      <td class=""><input type="text" name="company_phone_no[]" id="company_phone_no"></td>
      <td class="col-2" class="text-center"><strong></strong><input name="company_sign[]" type="hidden"></td>
      <td class="text-center"><button class="btn btn-sm remove-row-btn" onclick="removeRow(this)">
        <i class="fa fa-minus-circle remove-row-btn" style="font-size:28px;color:red"></i></button></td>
    `;
    tableBody.appendChild(newRow);
  }

  function userChange(userName) {
    const users = <?php echo json_encode($_SESSION['user_data']); ?>;

    for (let user of users) {
      if (user.name === userName.value) {
        userName.parentNode.nextElementSibling.firstElementChild.value = user.phone
     
        break
      }
    }
    

    userName.parentNode.nextElementSibling.nextElementSibling.firstElementChild.innerHTML = "Attend"
    userName.parentNode.nextElementSibling.nextElementSibling.lastElementChild.value = "Attend"
    userName.parentNode.parentNode.firstElementChild.firstElementChild.value = "SPUADKL"
      if(prepare){
        $("#prep_name").html(userName.value)
        var option = $('#prepare_by option[value="' + userName.value + '"]');
  
  if (option.length) {
    option.prop('selected', true);
  }
        prepare = false

      }
  }

  function prepareBy(prep){
    const users = <?php echo json_encode($_SESSION['user_data']); ?>;

    for (let user of users) {
      if (user.name === prep.value) {
        // $('#sign').html(user.sign)
        $('#prep_sign').val(user.sign)
        $('#prep_name').html(user.name)
        break
      }
    }
  }

</script>

</html>