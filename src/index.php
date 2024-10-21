<?php
session_start();
if (!isset($_SESSION['user_name']) && !isset($_SESSION['user_id'])) {
    header('location:./auth/login.php');
}
include './services/connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"
        integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous">
    </script>

    <title>AD KL SN, QR and PIAT Monitoring</title>

    <style>
        .container.shadow.p-5.my-5.bg-white {
            padding: 20px !important;
        }

        @media  (min-width: 2200px){
        .container, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
            /* min-width: 2000px !important; */
            max-width: 2000px !important;
        }
        }

        @media only screen and (max-width: 445px) {

            .dataTables_filter input {
                font-size: 0.6rem !important;
            }

            .dataTables_filter label,
            #myTable_length label,
            th {
                font-size: 13px !important;
            }

            td {
                font-size: 12px !important;
            }

            h3 {
                font-size: 19px !important;
            }
        }

        body {
            background: #e9e9e9;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="">AD KL SN, QR and PIAT Monitoring</a>
            <a href="./auth/logout.php" class="btn btn-sm btn-secondary">logout</a>
        </div>
    </nav>



    <!-- START MAIN CONTAINER -->
    <div class="container shadow p-5 my-5 bg-white ">

        <!-- SHOW MESSAGE IF SESSION HAS MESSAGE -->
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
        ?>

        <h3 class="text-center"><?php echo $_SESSION['user_name']; ?></h3>

        <!-- FOR DOWNLOAD EXCELS START -->
        <div class="text-end mb-3 d-flex justify-content-end">
            <div class="m-2">

                <a href="./sn-monitoring/create.php" class="btn btn-success btn-sm ">ADD SN</a>
            </div>
            <div class="m-2"><a href="./qr-foams/create.php" class="btn btn-success btn-sm ">ADD QR AND PIAT</a> </div>
            <!-- <div class="m-2"><a href="./qr-foams/create.php" class="btn btn-success btn-sm ">ADD QR AND PIAT</a> </div> -->
            <div class="m-2">
                <button type="button" class='btn btn-sm btn-success' data-bs-toggle='modal' data-bs-target='#addVendorModal' aria-expanded='false'>
                    Add Vendor
                </button>
            </div>

            <div class="m-2">
                <form action="./services/generateExcel.php" method="POST">
                    <input type="hidden" name="exc_ba" id="exc_ba" value="<?php echo isset($_POST['searchBA']) ? $_POST['searchBA'] : ''; ?>">
                    <input type="hidden" name="exc_from" id="exc_from" value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : ''; ?>">
                    <input type="hidden" name="exc_date_type" id="exc_date_type" value="<?php echo isset($_POST['date_type']) ? $_POST['date_type'] : ''; ?>">
                    <input type="hidden" name="exc_to" id="exc_to" value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : ''; ?>">
                    <button href="./services/generateExcel.php" class="btn btn-success btn-sm" type="submit"
                        value="download-qr" name="submit-button">Download
                        QR</button>

                    <button href="./services/generateExcel.php" class="btn btn-success btn-sm mx-2" value="download-sn"
                        type="submit" name="submit-button">Download
                        SN</button>
                </form>
                
            </div>  
            <div class="m-2">
            <button id="myreset" class="btn btn-secondary " type="button" 
            name='submitButton' value="reset">Reset</button>
            </div>    
        </div>

        <!-- FOR DOWNLOAD EXCELS END -->

        <!-- Top FILTER SECTION  START -->
        <form action="" method="post" onsubmit=" searchFoam()">
            <div class="text-end mb-3 row">

                <div class="m-1 col-md-2">
                    <label for="">Select BA :</label> <br>
                    <select name="searchBA" id="searchBA" class="form-select">
                        <?php if($_SESSION['user_name'] == "admin"){ ?>
                        <option value="<?php echo isset($_POST['searchBA']) ? $_POST['searchBA'] : ''; ?>" hidden><?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != '' ? $_POST['searchBA'] : 'All Ba'; ?></option>
                        <option value="KLB - 6121">KLB - 6121</option>
                        <option value="KLT - 6122">KLT - 6122</option>
                        <option value="KLP - 6123">KLP - 6123</option>
                        <option value="KLS - 6124">KLS - 6124</option>
                        <option value="">All Ba</option>
                        <?php } 
                            else {
                                    echo "<option value='{$_SESSION['user_ba']}'>{$_SESSION['user_ba']}</option>";
                                }?>
                    </select>

                </div>
                <div class="m-2 col-md-2">
                    <label for="">Date Type :</label> <br>
                    <span class="text-danger " id="date_type_error"></span>
                    <select name="date_type" id="date_type" class="form-select">
                        <option value="<?php echo isset($_POST['date_type']) && $_POST['date_type'] != '' ? $_POST['date_type'] : 'Both'; ?>" hidden><?php echo isset($_POST['date_type']) && $_POST['date_type'] != '' ? $_POST['date_type'] : 'Selet dateType'; ?></option>
                        <option value="Both">Both</option>
                        <option value="CSP">CSP Date</option>
                        <option value="Completion">Completion Date</option>
                    </select>
                </div>
                <div class="m-2 col-md-2">
                    <label for="">From Date :</label> <br>
                    <input type="date" name="from_date" id="from_date" class="form-control"
                        value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : ''; ?>">
                </div>
                <div class="m-2 col-md-2">
                    <label for="">To Date :</label> <br>
                    <input type="date" name="to_date" id="to_date" class="form-control"
                        value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : ''; ?>">
                </div>

                <div class="m-2 col-md-2">
                    <label for="">Aging greater than :</label> <br>
                    <input type="number" name="aging" id="aging" class="form-control"
                        value="<?php echo isset($_POST['aging']) ? $_POST['aging'] : ''; ?>">
                </div>

                <div class="col-md-1 pt-2 text-start" style="display: inline">

                    <button class="btn btn-secondary mt-4 btn-sm" type="submit" id="mysubmit" name='submitButton'
                        value="filter">Filter</button>
                    <!-- <a href="./index.php" >--> 
                        <!-- </a> -->
                </div>

            </div>
        </form>

        <!-- Top FILTER SECTION  END -->
           
        <!-- include top count and onclick filters -->
        <?php if ($_SESSION['user_name'] == 'admin') {
            include './admin/dashboard-count.php';
        } else {
            include './user/dashboard-count.php';
        } ?>


        <!-- TABLE TABS HEADER START -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                    type="button" role="tab" aria-controls="profile" aria-selected="false">SN
                    Monitoring</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link " id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
                    role="tab" aria-controls="home" aria-selected="true">QR</button>
            </li>

        </ul>

        <!-- TABLE TABS HEADER END -->


        
        <div class="tab-content" id="myTabContent">

            
                <!-- QR TABLE START -->
            <div class="tab-pane fade  " id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="table-responsive table-bordered py-3" style="overflow-y:auto ; ">
                    <table id="myTable" class="table table-striped table-responsive table-bordered" data-table>
                        <thead>
                            <tr>
                                <?php
                            if ($_SESSION['user_name'] == "admin") { ?>
                                <th>BA</th>
                                <?php   } ?>
                                <th>SN NO</th>
                                <th>JENIS SN</th>
                                <th>JENIS SAMBUNGAN</th>
                                <th>CSP DATE</th>
                                <th>COMPLETION DATE</th>
                                <th>CONSTRUCTION STATUS</th>
                                <th>QR</th>
                                <th>PIAT</th>
                                <th>ERMS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submitButton'] == 'filter') {
                                $ba = isset($_POST['searchBA']) ? $_POST['searchBA'] : '';
                            
                                $record = '';
                            
                                if ($col_name == 'both') {
                                    $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba AND csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid OR ba LIKE :ba AND tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap  ORDER BY csp_paid_date DESC');
                                    $stmt->bindParam(':from_paid', $from_paid);
                                    $stmt->bindParam(':to_paid', $to_paid);
                                    $stmt->bindParam(':from_siap', $from_siap);
                                    $stmt->bindParam(':to_siap', $to_siap);
                                    $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);
                                    $stmt->execute();
                                } else {
                                    $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba AND ' . $col_name . ' >= :from AND ' . $col_name . ' <= :to  ORDER BY csp_paid_date DESC');
                                    $stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);
                                }
                            } else {
                                // without filter
                                if ($_SESSION['user_name'] == 'admin') {
                                    $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr    ORDER BY id DESC');
                                } else {
                                    $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
                            
                                    $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba ORDER BY csp_paid_date DESC, id DESC');
                            
                                    // $stmt->bindValue(':created', '%' . $_SESSION['user_id'] . '%', PDO::PARAM_STR);
                                    $stmt->bindValue(':ba', '%' . $_SESSION['user_ba'] . '%', PDO::PARAM_STR);
                                }
                                $stmt->execute();
                            }
                            
                            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($records as $record) {
                                if ($record['jenis_sambungan'] != 'UG') {
                                    # code...
                            
                                    echo '<tr>';
                                    if ($_SESSION['user_name'] == 'admin') {
                                        echo "<td>{$record['ba']}</td>";
                                    }
                            
                                    echo "<td><a class='text-decoration-none text-dark' href='./qr-foams/edit.php?no_sn={$record['no_sn']}'>";
                                    echo $record['no_sn'];
                                    echo '</a></td>';
                            
                                    echo "<td>{$record['jenis_sn']}</td>";
                                    echo "<td>{$record['jenis_sambungan']}</td>";
                                    echo "<td>{$record['csp_paid_date']}</td>";
                                    echo "<td>{$record['tarikh_siap']}</td>";
                            
                                    echo "<td>{$record['status']}</td>";
                            
                                    echo "<td class='text-center'>";
                                    if ($record['tarikh_siap'] != '') {
                                        echo ' <span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }
                            
                                    echo '</td>';
                                    echo '<td class="algin-middle text-center">';
                            
                                    if ($record['piat_status'] == 'true') {
                                        echo '<span class="check " style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }
                                    echo '</td>';
                                    echo "<td class='text-center'>";
                                    if ($record['erms_status'] == 'done') {
                                        echo ' <span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }
                                    echo '</td>';
                            
                                    echo "<td class='text-center'><div class='dropdown'>
                                                                                        <button class='btn   ' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                                                                                        <img src='../images/three-dots-vertical.svg'  >
                                                                                        </button>
                                                                                        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>
                                                                                          <li><a class='dropdown-item' href='./services/generateExcel.php?id={$record['id']}'>Download Excel</a></li>";
                            
                                    echo "<li><a class='dropdown-item' href='./qr-foams/edit.php?no_sn={$record['no_sn']}'>";
                                    echo $record['tarikh_siap'] != '' ? 'Edit QR' : 'Add QR';
                                    echo '</a></li>';
                                    if ($record['piat_status'] == 'true') {
                                        echo "  <li><a class='dropdown-item' href='./generate-pdf/previewPDF.php?no_sn={$record['no_sn']}' target='_blank'>Preview PDF</a></li>";
                                    } elseif ($record['qr'] == 'true') {
                                        echo "  <li><a class='dropdown-item' href='./services/foamRedirect.php?sn={$record['no_sn']}'>Fill Checklist</a></li>";
                                    }
                                    echo "  <li><a class='dropdown-item' href='./piat-foam/detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";
                            
                                    echo "  <li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}' >Edit SN</a></li>";
                                    echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'> Delete </button'></li>";
                                    echo '</ul></div></td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                </div>

            </div>
                <!-- QR TABLE END -->



                <!-- SN TABLE START -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="table-responsive table-bordered py-3" style="overflow-y:auto ; ">
                    <table id="snTable" class="table table-striped table-responsive table-bordered ">
                        <thead>
                            <tr>
                                <th>BA</th>
                                <th>SN NO</th>
                                <th>JENIS SN</th>
                                <th>JENIS SAMBUNGAN</th>
                                <th>AGING (days)</th>
                                <th>CSP DATE</th>
                                <th>COMPLETION DATE</th>
                                <th>CONSTRUCTION STATUS</th>
                                <th>REMARKS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
 
                            $pdo = null;

                        function checkAgging($record){
                            $agingDateTime = new DateTime($record['csp_paid_date']);
                            
                            $todayDateTime = $record['tarikh_siap'] != '' ? new DateTime($record['tarikh_siap']) : new DateTime();
                    
                            $interval = $agingDateTime->diff($todayDateTime);
                            $differenceInDays = $interval->format('%a');
                            return  $differenceInDays;
                        }

                            
                            foreach ($records as $record) {
                                $myaging =checkAgging($record);
                                if(isset($_POST['aging'])){
                                 if($myaging < $_POST['aging']){
                                    continue;
                                 } 
                                }

                                echo '<tr>';
                                echo "<td>{$record['ba']}</td>";
                                echo "<td><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}'  >{$record['no_sn']}</a></td>";
                                echo "<td>{$record['jenis_sn']}</td>";
                                echo "<td>{$record['jenis_sambungan']}</td>";
                                if ($record['csp_paid_date'] != '') {
                                    $agingDateTime = new DateTime($record['csp_paid_date']);
                            
                                    $todayDateTime = $record['tarikh_siap'] != '' ? new DateTime($record['tarikh_siap']) : new DateTime();
                            
                                    $interval = $agingDateTime->diff($todayDateTime);
                                    $differenceInDays = $interval->format('%a');
                                    echo '<td> ' . $differenceInDays + 1 . '</td>';
                                } else {
                                    echo "<td>{$record['aging_days']}</td>";
                                }
                            
                                echo "<td>{$record['csp_paid_date']}</td>";
                                echo "<td>{$record['tarikh_siap']}</td>";
                                echo "<td>{$record['status']}</td>";
                                $remark = $record['remark'];
                                if ($remark) {
                                    if (strlen($remark) > 15) {
                                        $remark = substr($remark, 0, 15) . '...';
                                    }
                                }
                                echo "<td><a type='button' class='dropdown-item' data-bs-toggle='modal' data-remark='{$record['remark']}' data-id='{$record['id']}' data-bs-target='#remarkModal'>{$remark}</a></td>";
                            
                                echo "<td class='text-center'><div class='dropdown'>
                                                                                      <button class='btn   ' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                                                                                      <img src='../images/three-dots-vertical.svg'  >
                                                                                      </button>
                                                                                      <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>";
                            
                                echo "<li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}'>Edit SN</a></li>";
                            
                                echo "<li><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";
                                echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'> Delete </button'></li>";
                                echo "</ul></div></td>";
                                echo '</tr>';
                            }
                            ?>

                        </tbody>
                    </table>
                </div>



            </div>
                <!-- SN TABLE END -->
                
        </div>

    </div>


    <!-- MODAL FOR REMOVE RECORED -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Remove Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="./services/removeSn.php" method="post">
                    <div class="modal-body">
                        Are You Sure ?
                        <input type="hidden" name="sn" id="modal-sn">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Remove</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- MODAL FOR ADD VENDOR -->
        <div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel"
        aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVendorModalLabel">Add Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="./services/addVendor.php" method="post">
                    <div class="modal-body">
                        <label for="vendor"><strong> Add Vendor Name</strong></label>
                        <input type="text" class="form-control" name="vendor" id="vendor" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL FOR HOW UPDATE REMARKS -->
    <div class="modal fade" id="remarkModal" tabindex="-1" aria-labelledby="remrkModalLabel" aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remrkModalLabel">Remarks </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="./services/update-remarks.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="update-remarks-id">
                        <label class="form-label" for="remark-detail"><strong> Remarks : </strong> </label>
                        <textarea name="remarks" id="remark-detail" cols="30" rows="10" class="form-control"></textarea>
       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" >update</button>


                    </div>
                </form>
            </div>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.js"></script>

    <script>


        $(document).ready(function() {

            $('#myreset').click(function(){
                localStorage.removeItem('selectedDateType');
                localStorage.removeItem('selectedFromDate');
                localStorage.removeItem('selectedToDate');
                localStorage.removeItem('selectedAgging');
                location.reload();
            })
        const dateTypeSelect = document.getElementById('date_type');
        const fromDateSelect = document.getElementById('from_date');
        const todateSelect = document.getElementById('to_date');
        const agging = document.getElementById('aging');
        


// Load the saved value from localStorage
const savedDateType = localStorage.getItem('selectedDateType');
        if (savedDateType) {
            dateTypeSelect.value = savedDateType;
        }

        // Save the selected value to localStorage when changed
        dateTypeSelect.addEventListener('change', function() {
            localStorage.setItem('selectedDateType', this.value);
        });


        const saveFromDate = localStorage.getItem('selectedFromDate');
        if (saveFromDate) {
            fromDateSelect.value = saveFromDate;
        }

        // Save the selected value to localStorage when changed
        fromDateSelect.addEventListener('change', function() {
            localStorage.setItem('selectedFromDate', this.value);
        });


        const saveToDate = localStorage.getItem('selectedToDate');
        if (saveToDate) {
            todateSelect.value = saveToDate;
        }

        // Save the selected value to localStorage when changed
        todateSelect.addEventListener('change', function() {
            localStorage.setItem('selectedToDate', this.value);
        });


        const saveAgging = localStorage.getItem('selectedAgging');
        if (saveAgging) {
            agging.value = saveAgging;
        }

        // Save the selected value to localStorage when changed
        agging.addEventListener('change', function() {
            localStorage.setItem('selectedAgging', this.value);
        });

            

            $('#myTable , #snTable').DataTable({
                aaSorting: [
                    [3, 'desc']
                ],
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                // initComplete: function () {                    
                //     this.api().page(10).draw( 'page' );
                // }

            });
            // $("").DataTable({
            //     aaSorting: [
            //         [5, 'desc']
            //     ],
            //     "lengthMenu": [
            //         [10, 25, 50, -1],
            //         [10, 25, 50, "All"]
            //     ],
            //     "page": 10
            // })

        const savedButtonclick = localStorage.getItem('buttonClicked');
        if(savedButtonclick){
        if (savedButtonclick=='true') {
           localStorage.setItem('buttonClicked', 'false');
        }else{
            localStorage.setItem('buttonClicked', 'true');
            const button = document.getElementById('mysubmit');
            button.click();
        }
    }

            $('#searchButton').on('click', function() {
                var searchTerm = $('#searchInput').val();
                var table = $('#myTable').DataTable();
                table.search(searchTerm).draw();
            });

            var currentPage = $('#myTable').DataTable().page.info().page;
            console.log(currentPage);

                //on diaplay Remove modal
            $('#exampleModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('sn');
                var modal = $(this);
                $('#modal-sn').val(id)
            });

                //on diaplay remarks modal
            $('#remarkModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var detail = button.data('remark');
                var modal = $(this);
                $('#remark-detail').html(detail)
                $('#update-remarks-id').val(button.data('id'))
            });
   

            var savedPage = localStorage.getItem('savedPage');
            console.log(savedPage);
            // If a page number is saved, use it; otherwise, default to the first page (0-indexed)
            var defaultPage = savedPage ? parseInt(savedPage, 10) : 0;

            $(".paginate_button  [data-dt-idx='9']").trigger("click");

            $('#myTable').on('page.dt', function () {
            // var currentPage = $('#myTable').DataTable().page.info().page;
            console.log(currentPage);
            localStorage.setItem('savedPage', currentPage);
        });
            
        });

      

        function genrateExcel() {
            var ba = $("#searchBA").val()
            var from = $("#from_date").val()
            var to = $("#to_date").val()

            $("#exc_ba").val(ba)
            $('#exc_from').val(from)
            $("#exc_to").val(to)

            return true
        }


     
       

        function searchFoam() {
            var searchValid = true;
            var getDateType = $('#date_type').val();
            if (getDateType == '') {
                $('#date_type_error').html("Select date type");
                searchValid = false;
            }
            return searchValid;
        }


        var userba = '<?php echo $_SESSION['user_ba'] ?>';

        function adminSearch(ba, status) {
            var table = $('#myTable').DataTable();
            var table2 = $('#snTable').DataTable();

            if (userba == '') {
                table.columns(0).search(ba)
                table.columns(6).search(status); // Filter Column 2
            }else{ 
                table.columns(5).search(status);
            }
 
            

            table2.columns(0).search(ba); // Filter Column 1
            table2.columns(7).search(status); //

            table.draw();
            table2.draw();
        }
    </script>
</body>

</html>
