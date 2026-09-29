<?php
session_start();
if (!isset($_SESSION['user_name']) && !isset($_SESSION['user_id'])) {
    header('location:./auth/login.php');
}
include './services/connection.php';
include './services/access.php';

// View-only accounts (role = viewer) see everything an admin sees (all BAs,
// dashboard) but must not get any add / edit / delete controls.
$isViewer    = is_viewer();
$isAdminView = is_admin_view();
?>


<?php
// Fetch data for dashboard statistics
$dashboardData = [];

// Apply the top filter form (BA / date range / aging / permit / jenis sambungan)
// to the dashboard summary and aging analysis queries so they match the tables
$dashboardFilterSql = '';
$dashboardParams = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitButton']) && $_POST['submitButton'] == 'filter') {
    $dashboardFilters = [];

    // BA scope: non-admin users are always limited to their own BA
    $filterBa = $isAdminView ? (isset($_POST['searchBA']) ? $_POST['searchBA'] : '') : $_SESSION['user_ba'];
    $dashboardFilters[] = 'ba LIKE :ba';
    $dashboardParams[':ba'] = '%' . $filterBa . '%';

    // Date range (same logic as the SN/QR table filter)
    $from = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    if ($from == '' || $to == '') {
        // if dates are empty then first get min and max date
        $stmt = $pdo->prepare("SELECT MAX(tarikh_siap) AS max_date, MIN(tarikh_siap) AS min_date FROM public.ad_service_qr where tarikh_siap != ''");
        $stmt->execute();
        $comp_date = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare('SELECT MAX(csp_paid_date) AS max_date, MIN(csp_paid_date) AS min_date FROM public.ad_service_qr');
        $stmt->execute();
        $csp_date = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $dashboard_date_type = isset($_POST['date_type']) ? $_POST['date_type'] : '';
    if ($dashboard_date_type == 'CSP') {
        $from = $from == '' ? $csp_date['min_date'] : $from;
        $to = $to == '' ? $csp_date['max_date'] : $to;
        $dashboardFilters[] = 'csp_paid_date >= :from AND csp_paid_date <= :to';
        $dashboardParams[':from'] = $from;
        $dashboardParams[':to'] = $to;
    } elseif ($dashboard_date_type == 'Completion') {
        $from = $from == '' ? $comp_date['min_date'] : $from;
        $to = $to == '' ? $comp_date['max_date'] : $to;
        $dashboardFilters[] = 'tarikh_siap >= :from AND tarikh_siap <= :to';
        $dashboardParams[':from'] = $from;
        $dashboardParams[':to'] = $to;
    } else {
        $from_siap = $from == '' ? $comp_date['min_date'] : $from;
        $to_siap = $to == '' ? $comp_date['max_date'] : $to;
        $from_paid = $from == '' ? $csp_date['min_date'] : $from;
        $to_paid = $to == '' ? $csp_date['max_date'] : $to;
        $dashboardFilters[] = '((tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap) OR (csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid))';
        $dashboardParams[':from_siap'] = $from_siap;
        $dashboardParams[':to_siap'] = $to_siap;
        $dashboardParams[':from_paid'] = $from_paid;
        $dashboardParams[':to_paid'] = $to_paid;
    }

    // Aging bucket
    if (isset($_POST['aging']) && $_POST['aging'] != '') {
        if ($_POST['aging'] === '>60') {
            $dashboardFilters[] = '(CURRENT_DATE - NULLIF(csp_paid_date, \'\')::date) > 60';
        } else {
            $range = explode(',', $_POST['aging']);
            $dashboardFilters[] = '(CURRENT_DATE - NULLIF(csp_paid_date, \'\')::date) BETWEEN :aging_min AND :aging_max';
            $dashboardParams[':aging_min'] = intval($range[0]);
            $dashboardParams[':aging_max'] = intval($range[1]);
        }
    }

    // Permit type
    if (isset($_POST['permit_type']) && $_POST['permit_type'] != '') {
        $dashboardFilters[] = 'permit_sn = :permit_type';
        $dashboardParams[':permit_type'] = $_POST['permit_type'];
    }

    // Jenis sambungan
    if (isset($_POST['jenis_sambungan_filter']) && $_POST['jenis_sambungan_filter'] != '') {
        $dashboardFilters[] = 'jenis_sambungan = :jenis_sambungan';
        $dashboardParams[':jenis_sambungan'] = $_POST['jenis_sambungan_filter'];
    }

    $dashboardFilterSql = ' AND ' . implode(' AND ', $dashboardFilters);
}

// Query for SN status counts by BA
$statusQuery = "SELECT ba, 
                COUNT(CASE WHEN status = 'Complete' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'Inprogress' THEN 1 END) as inprogress,
                COUNT(CASE WHEN status = 'KIV' THEN 1 END) as kiv,
                COUNT(*) as total
                FROM public.ad_service_qr 
                WHERE (status IN ('Inprogress','KIV') OR complete_date >= '2026-01-01' OR tarikh_siap >= '2026-01-01')" . $dashboardFilterSql . "
                GROUP BY ba
                ORDER BY ba";
                
$statusStmt = $pdo->prepare($statusQuery);
$statusStmt->execute($dashboardParams);
$statusData = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

// Query for aging analysis
// Query for aging analysis - ADD +1 to match display
$agingQuery = "SELECT ba,
                COUNT(CASE WHEN age_days >= 1 AND age_days <= 5 THEN 1 END) as age_1_5,
                COUNT(CASE WHEN age_days >= 6 AND age_days <= 14 THEN 1 END) as age_6_14,
                COUNT(CASE WHEN age_days >= 15 AND age_days <= 30 THEN 1 END) as age_15_30,
                COUNT(CASE WHEN age_days >= 31 AND age_days <= 60 THEN 1 END) as age_31_60,
                COUNT(CASE WHEN age_days > 60 THEN 1 END) as age_gt_60
                FROM (
                    SELECT ba,
                    CASE 
                        WHEN tarikh_siap != '' AND tarikh_siap IS NOT NULL
                        THEN DATE_PART('day', tarikh_siap::timestamp - csp_paid_date::timestamp) + 1
                        ELSE DATE_PART('day', CURRENT_DATE - csp_paid_date::timestamp) + 1
                    END as age_days
                    FROM public.ad_service_qr 
                    WHERE status = 'Inprogress' 
                    AND (status IN ('Inprogress','KIV') OR complete_date >= '2026-01-01' OR tarikh_siap >= '2026-01-01')" . $dashboardFilterSql . "
                ) as subquery
                GROUP BY ba
                ORDER BY ba";
                
$agingStmt = $pdo->prepare($agingQuery);
$agingStmt->execute($dashboardParams);
$agingData = $agingStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for charts
$chartLabels = [];
$completedData = [];
$inprogressData = [];
$kivData = [];
$totalData = [];

foreach ($statusData as $row) {
    $chartLabels[] = $row['ba'];
    $completedData[] = (int)$row['completed'];
    $inprogressData[] = (int)$row['inprogress'];
    $kivData[] = (int)$row['kiv'];
    $totalData[] = (int)$row['total'];
}

// Prepare aging chart data
$agingLabels = [];
$age1_5Data = [];
$age6_14Data = [];
$age15_30Data = [];
$age31_60Data = [];
$ageGt60Data = [];

foreach ($agingData as $row) {
    $agingLabels[] = $row['ba'];
    $age1_5Data[] = (int)$row['age_1_5'];
    $age6_14Data[] = (int)$row['age_6_14'];
    $age15_30Data[] = (int)$row['age_15_30'];
    $age31_60Data[] = (int)$row['age_31_60'];
    $ageGt60Data[] = (int)$row['age_gt_60'];
}
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

        .aging-cell:hover, .aging-cell-total:hover {
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s ease;
}

.aging-cell:not([data-ba=""]):not(:empty), 
.aging-cell-total:not([data-ba=""]):not(:empty) {
    position: relative;
}

.aging-cell:not([data-ba=""]):not(:empty)::after, 
.aging-cell-total:not([data-ba=""]):not(:empty)::after {
    content: "🔍";
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.aging-cell:not([data-ba=""]):not(:empty):hover::after, 
.aging-cell-total:not([data-ba=""]):not(:empty):hover::after {
    opacity: 1;
}

#filteredRecordsTable {
    font-size: 0.9rem;
}

/* Ensure remark modal appears above the records modal */
#remarkModal {
    z-index: 1060 !important;
}

#remarkModal .modal-backdrop {
    z-index: 1055 !important;
}

#recordsModal {
    z-index: 1050 !important;
}

#recordsModal .modal-backdrop {
    z-index: 1045 !important;
}
    </style>

    <head>
    <!-- Existing head content... -->
    
    <!-- Add Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Add this style for dashboard -->
    <style>
        .dashboard-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 15px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }
        
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
        
        .table-responsive {
            overflow-y: visible;
        }
    </style>

    <script>
        var username='<?php echo $_SESSION['user_name']?>';
        // Viewer sees the same columns/tables as admin (read-only)
        var isAdminView = <?php echo $isAdminView ? 'true' : 'false'; ?>;
    </script>
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
            <a href="./piat_old.php" class="btn btn-success btn-sm ">OLD PIAT</a>
            </div>
            <?php if (!$isViewer): ?>
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
            <?php endif; ?>

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
                        <?php if($isAdminView){ ?>
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
                        <!-- <option value="<?php echo isset($_POST['date_type']) && $_POST['date_type'] != '' ? $_POST['date_type'] : 'Both'; ?>" hidden><?php echo isset($_POST['date_type']) && $_POST['date_type'] != '' ? $_POST['date_type'] : 'Selet dateType'; ?></option> -->
                        <option value="<?php echo isset($_POST['date_type']) && $_POST['date_type'] != '' ? $_POST['date_type'] : ''; ?>" hidden>
                            <?php echo isset($_POST['date_type']) && $_POST['date_type'] != '' ? $_POST['date_type'] : 'Select dateType'; ?>
                        </option>
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
                        <!-- value="<?php echo isset($_POST['aging']) ? $_POST['aging'] : ''; ?>"> -->
                        <select name="aging" id="aging" class="form-select">
                        <option value="<?php echo isset($_POST['aging']) ? $_POST['aging'] : ''; ?>" hidden>
                            <?php echo isset($_POST['aging']) && $_POST['aging'] != '' ? $_POST['aging'] : 'Select aging'; ?>
                        </option>
                        <option value="1,7">1-5 days</option>
                        <option value="8,14">6-14 days</option>
                        <option value="14,30">15-30 days</option>
                        <option value="30,60">30-60 days</option>
                        <option value=">60">>60 days</option>
                    </select>    
                </div>
                <div class="m-2 col-md-2">
                <label for="">Permit Type :</label> <br>
                <select name="permit_type" id="permit_type" class="form-select">
                    <option value="<?php echo isset($_POST['permit_type']) ? $_POST['permit_type'] : ''; ?>" hidden>
                        <?php echo isset($_POST['permit_type']) && $_POST['permit_type'] != '' ? $_POST['permit_type'] : 'Select Permit'; ?>
                    </option>
                    <option value="">Both</option>
                    <option value="PBT">PBT</option>
                    <option value="DBKL">DBKL</option>
                </select>
            </div>

                <div class="m-2 col-md-2">
                <label for="">Jenis Sambungan :</label> <br>
                <select name="jenis_sambungan_filter" id="jenis_sambungan_filter" class="form-select">
                    <option value="<?php echo isset($_POST['jenis_sambungan_filter']) ? $_POST['jenis_sambungan_filter'] : ''; ?>" hidden>
                        <?php echo isset($_POST['jenis_sambungan_filter']) && $_POST['jenis_sambungan_filter'] != '' ? $_POST['jenis_sambungan_filter'] : 'All'; ?>
                    </option>
                    <option value="">All</option>
                    <option value="OH">OH</option>
                    <option value="UG">UG</option>
                </select>
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
        <?php if ($isAdminView) {
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

        <?php if ($isAdminView) : ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link " id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button"
                role="tab" aria-controls="dashboard" aria-selected="true">Dashboard</button>
        </li>
        <?php endif; ?>

        </ul>

        <!-- TABLE TABS HEADER END -->


        
        <div class="tab-content" id="myTabContent">

            
                <!-- QR TABLE START -->
            <div class="tab-pane fade  " id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="table-responsive table-bordered py-3">
                    <table id="myTable" class="table table-striped table-responsive table-bordered" data-table>
                        <thead>
                            <tr>
                                <?php
                            if ($isAdminView) { ?>
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
                            //   echo  $from_siap.'-'.$to_siap.'-'.$from_paid.'-'.$to_paid.'3';
                             ///  echo json_encode($_POST);
                                if ($col_name == 'both') {
                                   // echo  $from_siap.'-'.$to_siap.'-'.$from_paid.'-'.$to_paid;
                                    $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba AND ((csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid)  OR (tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)) and (status in ('Inprogress','KIV') or complete_date>='2026-01-01' or tarikh_siap>='2026-01-01')  ORDER BY csp_paid_date DESC");
                                    $stmt->bindParam(':from_paid', $from_paid);
                                    $stmt->bindParam(':to_paid', $to_paid);
                                    $stmt->bindParam(':from_siap', $from_siap);
                                    $stmt->bindParam(':to_siap', $to_siap);
                                    $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);
                                    $stmt->execute();
                                    
                                } else {
                                 //   echo  $from.'-'.$to.'-'.$col_name;
                                    $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba AND $col_name >= :from AND  $col_name <= :to and (status in ('Inprogress','KIV') or complete_date>='2026-01-01' or tarikh_siap>='2026-01-01') ORDER BY csp_paid_date DESC");
                                    $stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);
                                }
                            } else {
                                // without filter
                                if ($isAdminView) {
                                  //  echo  $from_siap.'-'.$to_siap.'-'.$from_paid.'-'.$to_paid.'1';

                                    $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr where status in ('Inprogress','KIV') or complete_date>='2026-01-01' or tarikh_siap>='2026-01-01'  ORDER BY id DESC");
                                } else {
                                 //   echo  $from_siap.'-'.$to_siap.'-'.$from_paid.'-'.$to_paid.'2';

                                    $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
                            
                                    $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba and (status in ('Inprogress','KIV') or complete_date>='2026-01-01' or tarikh_siap>='2026-01-01') ORDER BY csp_paid_date DESC, id DESC");
                            
                                    // $stmt->bindValue(':created', '%' . $_SESSION['user_id'] . '%', PDO::PARAM_STR);
                                    $stmt->bindValue(':ba', '%' . $_SESSION['user_ba'] . '%', PDO::PARAM_STR);
                                }
                                $stmt->execute();
                            }

                            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                           if (isset($_POST['permit_type']) && $_POST['permit_type'] != '') {
                                $records = array_values(array_filter($records, function($record) {
                                    return $record['permit_sn'] === $_POST['permit_type'];
                                }));
                            }

                            if (isset($_POST['jenis_sambungan_filter']) && $_POST['jenis_sambungan_filter'] != '') {
                                $jsFilter = $_POST['jenis_sambungan_filter'];
                                $records = array_values(array_filter($records, function($record) use ($jsFilter) {
                                    return $record['jenis_sambungan'] === $jsFilter;
                                }));
                            }
                            
                            // Get ALL in-progress records for aging analysis (drill-down),
                            // restricted by the same top filter as the dashboard tables
                                $agingExtraSql = $dashboardFilterSql;
                                $agingExtraParams = $dashboardParams;
                                if (!$isAdminView && $agingExtraSql === '') {
                                    // no filter submitted: still limit non-admin users to their own BA
                                    $agingExtraSql = ' AND ba LIKE :ba';
                                    $agingExtraParams[':ba'] = '%' . $_SESSION['user_ba'] . '%';
                                }
                                $agingStmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE status = 'Inprogress' AND (status IN ('Inprogress','KIV') OR complete_date >= '2026-01-01' OR tarikh_siap >= '2026-01-01')" . $agingExtraSql . " ORDER BY csp_paid_date DESC");
                                $agingStmt->execute($agingExtraParams);
                                $allAgingRecords = $agingStmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <script>
                                const allSNRecords = <?php echo json_encode($records); ?>;
                                const allAgingRecords = <?php echo json_encode($allAgingRecords); ?>;
                                console.log('Total SN records from PHP:', allSNRecords.length);
                                console.log('Total aging records from PHP:', allAgingRecords.length);
                                </script>
                                <?php

                                foreach ($records as $record) {
                                // echo $record['jenis_sambungan'];
                                // echo json_encode( $records);
                                //  exit();

                                if ($record['jenis_sambungan'] != 'UG') {
                                    # code...
                            
                                    echo '<tr>';
                                    if ($isAdminView) {
                                        echo "<td>{$record['ba']}</td>";
                                    }
                            
                                    $qrSnLink = $isViewer ? "./piat-foam/detail.php?no_sn={$record['no_sn']}" : "./qr-foams/edit.php?no_sn={$record['no_sn']}";
                                    echo "<td><a class='text-decoration-none text-dark' href='{$qrSnLink}'>";
                                    echo $record['no_sn'];
                                    echo '</a></td>';
                            
                                    echo "<td>{$record['jenis_sn']}</td>";
                                    echo "<td>{$record['jenis_sambungan']}</td>";
                                    echo "<td>{$record['csp_paid_date']}</td>";
                                    echo "<td>{$record['tarikh_siap']}</td>";
                            
                                    echo "<td>{$record['status']}</td>";
                            
                                    echo "<td class='text-center'>";
                                    if ($record['tarikh_siap'] != '') {
                                        echo '<span class="d-none">qr_done</span><span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="d-none">qr_pending</span><span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }
                                    echo '</td>';
                                    echo '<td class="algin-middle text-center">';
                                    if ($record['piat_status'] == 'true') {
                                        echo '<span class="d-none">piat_done</span><span class="check " style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="d-none">piat_pending</span><span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }
                                    echo '</td>';
                                    echo "<td class='text-center'>";
                                    if ($record['erms_status'] == 'done') {
                                        echo '<span class="d-none">erms_done</span><span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="d-none">erms_pending</span><span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }
                                    echo '</td>';
                            
                                    echo "<td class='text-center'><div class='dropdown'>
                                                                                        <button class='btn   ' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                                                                                        <img src='../images/three-dots-vertical.svg'  >
                                                                                        </button>
                                                                                        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>
                                                                                          <li><a class='dropdown-item' href='./services/generateExcel.php?id={$record['id']}'>Download Excel</a></li>";

                                    if (!$isViewer) {
                                        echo "<li><a class='dropdown-item' href='./qr-foams/edit.php?no_sn={$record['no_sn']}'>";
                                        echo $record['tarikh_siap'] != '' ? 'Edit QR' : 'Add QR';
                                        echo '</a></li>';
                                    }
                                    if ($record['piat_status'] == 'true') {
                                        echo "  <li><a class='dropdown-item' href='./generate-pdf/previewPDF.php?no_sn={$record['no_sn']}' target='_blank'>Preview PDF</a></li>";
                                    } elseif ($record['qr'] == 'true' && !$isViewer) {
                                        echo "  <li><a class='dropdown-item' href='./services/foamRedirect.php?sn={$record['no_sn']}'>Fill Checklist</a></li>";
                                    }
                                    echo "  <li><a class='dropdown-item' href='./piat-foam/detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";

                                    if (!$isViewer) {
                                        echo "  <li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}' >Edit SN</a></li>";
                                        echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'> Delete </button'></li>";
                                    }
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
                <div class="table-responsive table-bordered py-3">
                    <table id="snTable" class="table table-striped table-responsive table-bordered ">
                        <thead>
                            <tr>
                                <th>BA</th>
                                <th>SN NO</th>
                                <th>JENIS SN</th>
                                <th>Permit Type</th>
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
 
                         

                        function checkAgging($record){
                            $agingDateTime = new DateTime($record['csp_paid_date']);
                            
                          //  $todayDateTime = $record['tarikh_siap'] != '' ? new DateTime($record['tarikh_siap']) : new DateTime();
                            $todayDateTime = new DateTime();
                    
                            $interval = $agingDateTime->diff($todayDateTime);
                            $differenceInDays = $interval->format('%a');
                            return  $differenceInDays;
                        }

                            
                            foreach ($records as $record) {
                                $myaging =checkAgging($record);
                                // if(isset($_POST['aging'])){
                                //  if($myaging < $_POST['aging']){
                                //     continue;
                                //  } 
                                // }
                                
                                if(isset($_POST['aging']) && $_POST['aging']!='') {
                                    // echo $_POST['aging'];
                                    // echo 'hi';
                                    if($_POST['aging'] === '>60') {
                                        // Handle greater than 60 days case
                                        if($myaging <= 60) {
                                            continue;
                                        }
                                    } else {
                                        // Handle range cases (1,7), (8,14), etc.
                                        $range = explode(',', $_POST['aging']);
                                        $min = intval($range[0]);
                                        $max = intval($range[1]);
                                        
                                        if($myaging < $min || $myaging > $max) {
                                            continue;
                                        }
                                    }
                                }

                                echo '<tr>';
                                echo "<td>{$record['ba']}</td>";
                                echo "<td><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}'  >{$record['no_sn']}</a></td>";                           
                                echo "<td>{$record['jenis_sn']}</td>";
                                 echo "<td>{$record['permit_sn']}</td>";
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
                               // Properly escape remark for HTML attribute
                                $escapedRemark = htmlspecialchars($record['remark'], ENT_QUOTES, 'UTF-8');
                                $displayRemark = $record['remark'];
                                if ($displayRemark && strlen($displayRemark) > 15) {
                                    $displayRemark = substr($displayRemark, 0, 15) . '...';
                                }

                                if ($isViewer) {
                                    // Read-only: show the remark text without the edit modal trigger
                                    echo "<td>" . htmlspecialchars($displayRemark ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                                } else {
                                    echo "<td><a type='button' class='dropdown-item btn btn-warning btn-sm text-dark' style='display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 600; background-color: #ffc107; border: 2px solid #ff9800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);' data-bs-toggle='modal' data-remark=\"{$escapedRemark}\" data-id=\"{$record['id']}\" data-sn=\"{$record['no_sn']}\" data-address=\"" . htmlspecialchars($record['alamat'] ?? '', ENT_QUOTES, 'UTF-8') . "\" data-bs-target='#remarkModal'>{$displayRemark}</a></td>";
                                }

                                echo "<td class='text-center'><div class='dropdown'>
                                                                                      <button class='btn   ' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                                                                                      <img src='../images/three-dots-vertical.svg'  >
                                                                                      </button>
                                                                                      <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>";
                            
                                if (!$isViewer) {
                                    echo "<li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}'>Edit SN</a></li>";
                                }
                                echo "<li><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";
                                if (!$isViewer) {
                                    echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'> Delete </button'></li>";
                                }
                                echo "</ul></div></td>";
                                echo '</tr>';
                            }
                            ?>

                        </tbody>
                    </table>
                </div>



            </div>
                <!-- SN TABLE END -->

         
<div class="tab-pane fade" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
    <div class="container-fluid">
        
        <!-- Summary Statistics Row -->
        <div class="row mb-4">
            <?php
            // Calculate totals
            $totalCompleted = array_sum($completedData);
            $totalInprogress = array_sum($inprogressData);
            $totalKIV = array_sum($kivData);
            $grandTotal = array_sum($totalData);
            
            // Calculate percentages
            $completedPercent = $grandTotal > 0 ? round(($totalCompleted / $grandTotal) * 100, 1) : 0;
            $inprogressPercent = $grandTotal > 0 ? round(($totalInprogress / $grandTotal) * 100, 1) : 0;
            $kivPercent = $grandTotal > 0 ? round(($totalKIV / $grandTotal) * 100, 1) : 0;
            ?>
            
            <!-- <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);">
                    <h3><?php echo $totalCompleted; ?></h3>
                    <p>Completed</p>
                    <small><?php echo $completedPercent; ?>% of total</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);">
                    <h3><?php echo $totalInprogress; ?></h3>
                    <p>In Progress</p>
                    <small><?php echo $inprogressPercent; ?>% of total</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                    <h3><?php echo $totalKIV; ?></h3>
                    <p>KIV</p>
                    <small><?php echo $kivPercent; ?>% of total</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);">
                    <h3><?php echo $grandTotal; ?></h3>
                    <p>Total SN</p>
                    <small>All BA Total</small>
                </div>
            </div>
        </div> -->
        
        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Status Distribution Chart -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h5 class="mb-3">SN Status Distribution by BA</h5>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Aging Analysis Chart -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h5 class="mb-3">In Progress SN Aging Analysis</h5>
                    <div class="chart-container">
                        <canvas id="agingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tables Row -->
        <div class="row">
            <!-- Status Count Table -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h5 class="mb-3">SN Monitoring Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>BA</th>
                                    <th>Completed</th>
                                    <th>Inprogress</th>
                                    <th>KIV</th>
                                    <th>Total</th>
                                    <th>Completion %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statusData as $row): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['ba']); ?></strong></td>
                                    <td class="text-success"><?php echo $row['completed']; ?></td>
                                    <td class="text-primary"><?php echo $row['inprogress']; ?></td>
                                    <td class="text-warning"><?php echo $row['kiv']; ?></td>
                                    <td><strong><?php echo $row['total']; ?></strong></td>
                                    <td>
                                        <?php 
                                        $completionPercent = $row['total'] > 0 ? 
                                            round(($row['completed'] / $row['total']) * 100, 1) : 0;
                                        echo $completionPercent . '%';
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo $totalCompleted; ?></strong></td>
                                    <td><strong><?php echo $totalInprogress; ?></strong></td>
                                    <td><strong><?php echo $totalKIV; ?></strong></td>
                                    <td><strong><?php echo $grandTotal; ?></strong></td>
                                    <td><strong><?php echo $completedPercent; ?>%</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Aging Analysis Table -->
           <div class="col-md-6">
    <div class="dashboard-card">
        <h5 class="mb-3">SN Inprogress Aging Analysis</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>BA</th>
                        <th>1-5 Days</th>
                        <th>6-14 Days</th>
                        <th>15-30 Days</th>
                        <th>31-60 Days</th>
                        <th>>60 Days</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agingData as $row): 
                        $rowTotal = $row['age_1_5'] + $row['age_6_14'] + $row['age_15_30'] + 
                                   $row['age_31_60'] + $row['age_gt_60'];
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['ba']); ?></strong></td>
                        <td class="<?php echo $row['age_1_5'] > 0 ? 'bg-success text-white' : ''; ?> aging-cell" 
                            style="cursor: pointer;" 
                            data-ba="<?php echo htmlspecialchars($row['ba']); ?>" 
                            data-min="1" 
                            data-max="5">
                            <?php echo $row['age_1_5']; ?>
                        </td>
                        <td class="<?php echo $row['age_6_14'] > 0 ? 'bg-info text-white' : ''; ?> aging-cell" 
                            style="cursor: pointer;" 
                            data-ba="<?php echo htmlspecialchars($row['ba']); ?>" 
                            data-min="6" 
                            data-max="14">
                            <?php echo $row['age_6_14']; ?>
                        </td>
                        <td class="<?php echo $row['age_15_30'] > 0 ? 'bg-warning' : ''; ?> aging-cell" 
                            style="cursor: pointer;" 
                            data-ba="<?php echo htmlspecialchars($row['ba']); ?>" 
                            data-min="15" 
                            data-max="30">
                            <?php echo $row['age_15_30']; ?>
                        </td>
                        <td class="<?php echo $row['age_31_60'] > 0 ? 'bg-danger text-white' : ''; ?> aging-cell" 
                            style="cursor: pointer;" 
                            data-ba="<?php echo htmlspecialchars($row['ba']); ?>" 
                            data-min="31" 
                            data-max="60">
                            <?php echo $row['age_31_60']; ?>
                        </td>
                        <td class="<?php echo $row['age_gt_60'] > 0 ? 'bg-danger text-white' : ''; ?> aging-cell" 
                            style="cursor: pointer;" 
                            data-ba="<?php echo htmlspecialchars($row['ba']); ?>" 
                            data-min="61" 
                            data-max="9999">
                            <?php echo $row['age_gt_60']; ?>
                        </td>
                        <td><strong><?php echo $rowTotal; ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="aging-cell-total" style="cursor: pointer;" data-ba="all" data-min="1" data-max="5">
                            <strong><?php echo array_sum($age1_5Data); ?></strong>
                        </td>
                        <td class="aging-cell-total" style="cursor: pointer;" data-ba="all" data-min="6" data-max="14">
                            <strong><?php echo array_sum($age6_14Data); ?></strong>
                        </td>
                        <td class="aging-cell-total" style="cursor: pointer;" data-ba="all" data-min="15" data-max="30">
                            <strong><?php echo array_sum($age15_30Data); ?></strong>
                        </td>
                        <td class="aging-cell-total" style="cursor: pointer;" data-ba="all" data-min="31" data-max="60">
                            <strong><?php echo array_sum($age31_60Data); ?></strong>
                        </td>
                        <td class="aging-cell-total" style="cursor: pointer;" data-ba="all" data-min="61" data-max="9999">
                            <strong><?php echo array_sum($ageGt60Data); ?></strong>
                        </td>
                        <td><strong>
                            <?php echo array_sum($age1_5Data) + array_sum($age6_14Data) + 
                                   array_sum($age15_30Data) + array_sum($age31_60Data) + 
                                   array_sum($ageGt60Data); ?>
                        </strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>




        </div>
    </div>
</div>

           
     
                
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remrkModalLabel">Remarks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="update-remarks-id">

                    <div class="row mb-3 bg-light p-3 rounded border">
                        <div class="col-md-6">
                            <strong>SN No:</strong>
                            <div id="view-sn-no" class="text-muted">—</div>
                        </div>
                        <div class="col-md-6">
                            <strong>Address:</strong>
                            <div id="view-address" class="text-muted">—</div>
                        </div>
                    </div>
                    
                    <!-- Date Picker -->
                    <div class="mb-3">
                        <label class="form-label" for="remark-date"><strong>Select Date:</strong></label>
                        <input type="date" id="remark-date" class="form-control">
                    </div>
                    
                    <!-- Remarks Textarea -->
                    <label class="form-label" for="remark-detail"><strong>Remarks:</strong></label>
                    <textarea name="remarks" id="remark-detail" cols="30" rows="10" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="recordsModal" tabindex="-1" aria-labelledby="recordsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordsModalLabel">SN Records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="filteredRecordsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>BA</th>
                                <th>SN NO</th>
                                <th>JENIS SN</th>
                                <th>PERMIT TYPE</th>
                                <th>JENIS SAMBUNGAN</th>
                                <th>AGING (days)</th>
                                <th>CSP DATE</th>
                                <th>COMPLETION DATE</th>
                                <th>STATUS</th>
                                <th>REMARKS</th>
                            </tr>
                        </thead>
                        <tbody id="filteredRecordsBody">
                            <!-- Records will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('remark-date');
    const textarea = document.getElementById('remark-detail');
    
    // Add selected date to textarea when date changes
    dateInput.addEventListener('change', function() {
        if (this.value) {
            const selectedDate = this.value; // Format: YYYY-MM-DD
            const formattedDate = new Date(selectedDate).toLocaleDateString('en-GB'); // Format: DD/MM/YYYY
            
            const currentText = textarea.value;
            const trimmedText = currentText.trim();
            const dateText = `[${formattedDate}]`;

            // New date goes in front of the existing remarks
            textarea.value = trimmedText ? dateText + ' \n' + trimmedText : dateText + ' ';

            // Blink the caret right after the newly added date, on the same line
            textarea.focus();
            const caretPos = dateText.length + 1;
            textarea.setSelectionRange(caretPos, caretPos);
        }
    });
});
</script>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.js"></script>

    <script>


        $(document).ready(function() {

        // Handle remark form submission via AJAX
// ===== FIXED: Handle opening remark modal from static table =====
$('#remarkModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);

    // Opened programmatically (dynamic link already filled the fields)
    if (!button || button.length === 0) {
        return;
    }

    var detail = button.data('remark') || '';
    var id = button.data('id') || button.data('data-id');
    var snNo = button.data('sn') || '—';
    var address = button.data('address') || '—';
    
    console.log('Opening remark modal (static) - ID:', id, 'SN:', snNo, 'Address:', address);
    
    $('#remark-detail').val(detail);
    $('#update-remarks-id').val(id);
    $('#view-sn-no').html(snNo);
    $('#view-address').html(address);
    $('#remark-date').val('');
});

// Put the caret at the end of the remark text whenever the modal opens
$('#remarkModal').on('shown.bs.modal', function() {
    var remarkBox = document.getElementById('remark-detail');
    if (remarkBox) {
        remarkBox.focus();
        remarkBox.setSelectionRange(remarkBox.value.length, remarkBox.value.length);
    }
});

// ===== FIXED: Handle opening remark modal from dynamic/filtered table =====
$(document).on('click', '.modal-remark-link', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var detail = $(this).data('remark') || $(this).data('data-remark') || '';
    var id = $(this).data('id') || $(this).data('data-id');
    var snNo = $(this).data('sn') || '—';
    var address = $(this).data('address') || '—';
    
    console.log('Opening remark modal (dynamic) - ID:', id, 'SN:', snNo, 'Address:', address);
    
    $('#remark-detail').val(detail);
    $('#update-remarks-id').val(id);
    $('#view-sn-no').html(snNo);
    $('#view-address').html(address);
    $('#remark-date').val('');
    
    // Show the remark modal
    $('#remarkModal').modal('show');
    
    // Ensure proper z-index when opened from another modal
    if ($('#recordsModal').hasClass('show')) {
        $('#remarkModal').css('z-index', parseInt($('#recordsModal').css('z-index')) + 10);
    }
});
// Handle remark form submission via AJAX
$(document).on('submit', '#remarkModal form', function(e) {
    e.preventDefault();
    
    var remarkId = $('#update-remarks-id').val();
   
    var remarkText = $('#remark-detail').val();
    
    console.log('Submitting - ID:', remarkId, 'Remark:', remarkText);
    
    if (!remarkId || remarkId === '') {
        alert('Error: No record ID found. Please try again.');
        return false;
    }
    
    var formData = {
        id: remarkId,
        remarks: remarkText
    };
    
    var submitButton = $(this).find('button[type="submit"]');
    submitButton.prop('disabled', true).text('Updating...');
    
    $.ajax({
        url: './services/update-remarks.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);
            
            if (response.success) {
                var recordId = response.id;
                var newRemark = response.remark;
                
                // Update in allSNRecords array
                var recordIndex = allSNRecords.findIndex(r => r.id == recordId);
                if (recordIndex !== -1) {
                    allSNRecords[recordIndex].remark = newRemark;
                }
                
                // Update in allAgingRecords array
                var agingIndex = allAgingRecords.findIndex(r => r.id == recordId);
                if (agingIndex !== -1) {
                    allAgingRecords[agingIndex].remark = newRemark;
                }
                
                // Update the display in DataTables
                updateRemarkInDataTables(recordId, newRemark);
                
                // Close the remark modal
                $('#remarkModal').modal('hide');
                
                // Show success message
                showSuccessMessage(response.message);
                
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Response:', xhr.responseText);
            alert('Error updating remark. Please try again.');
        },
        complete: function() {
            submitButton.prop('disabled', false).text('Update');
        }
    });
    
    return false;
});

function updateRemarkInDataTables(recordId, newRemark) {
    var escapedRemark = $('<div>').text(newRemark).html();

    var displayRemark = newRemark;
    if (displayRemark && displayRemark.length > 15) {
        displayRemark = displayRemark.substring(0, 15) + '...';
    }
    
    // Update SN Table (snTable)
    var snTable = $('#snTable').DataTable();
    snTable.rows().every(function() {
        var data = this.data();
        var $row = $(this.node());
        var $remarkBtn = $row.find('a[data-id="' + recordId + '"]');
        
        if ($remarkBtn.length) {
            // Update the button text and ALL data attributes
            $remarkBtn.text(displayRemark || '');
            $remarkBtn.attr('data-remark', newRemark);
            // Also update the data() object directly for jQuery
            $remarkBtn.data('remark', newRemark);
            
            // Force DataTable to recognize the change
            this.invalidate();
        }
    });
    
    // Update QR Table (myTable)
    var qrTable = $('#myTable').DataTable();
    qrTable.rows().every(function() {
        var data = this.data();
        var $row = $(this.node());
        var $remarkBtn = $row.find('a[data-id="' + recordId + '"]');
        
        if ($remarkBtn.length) {
            $remarkBtn.text(displayRemark || '');
            $remarkBtn.attr('data-remark', newRemark);
            // Also update the data() object directly for jQuery
            $remarkBtn.data('remark', newRemark);
            this.invalidate();
        }
    });
    
    // Update filtered records modal table if visible
    $('#filteredRecordsTable tbody tr').each(function() {
        var $remarkBtn = $(this).find('a[data-id="' + recordId + '"]');
        if ($remarkBtn.length) {
            $remarkBtn.text(displayRemark || '');
            $remarkBtn.attr('data-remark', newRemark);
            // Also update the data() object directly for jQuery
            $remarkBtn.data('remark', newRemark);
        }
    });
    
    // Redraw the tables to show changes
    snTable.draw(false); // false = stay on current page
    qrTable.draw(false);
}

// Function to show success message
function showSuccessMessage(message) {
    var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px;">' +
                    message +
                    '<button type="button" class="btn-close" onclick="this.parentNode.remove()"></button>' +
                    '</div>';
    
    $('body').append(alertHtml);
    
    // Auto-hide after 3 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 3000);
}

        $('button[data-bs-toggle="tab"]').on('click', function() {
        const activeTab = $(this).attr('id');
        localStorage.setItem('activeTab', activeTab);
    });

    // Retrieve and set active tab on page load
    const savedTab = localStorage.getItem('activeTab');
    if (savedTab) {
        // Remove 'active' class from all tabs
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');
        
        // Add 'active' class to saved tab
        $(`#${savedTab}`).addClass('active');
        $(`#${savedTab.replace('-tab', '')}`).addClass('show active');
    }

    // When modal is closed, reload page and preserve tab
    $('#recordsModal').on('hidden.bs.modal', function () {
        const currentTab = $('.nav-link.active').attr('id') || 'profile-tab';
        localStorage.setItem('activeTab', currentTab);
        
        setTimeout(function() {
            window.location.reload();
        }, 100);
    });

            $('#myreset').click(function(){
                localStorage.removeItem('selectedDateType');
                localStorage.removeItem('selectedFromDate');
                localStorage.removeItem('selectedToDate');
                localStorage.removeItem('selectedAgging');
                localStorage.removeItem('selectedStatus');
                localStorage.removeItem('selectedBA');
                localStorage.removeItem('buttonClicked');
                    localStorage.removeItem('selectedPermitType');

                window.location.reload(true); 
                window.location.href = window.location.href;
            })

        var permitSelect = document.getElementById('permit_type');
        var dateTypeSelect = document.getElementById('date_type');
        var fromDateSelect = document.getElementById('from_date');
        var todateSelect = document.getElementById('to_date');
        var agging = document.getElementById('aging');
        var selba = document.getElementById('searchBA');

        
        
        var savePermitType = localStorage.getItem('selectedPermitType');
        if (savePermitType) {
            permitSelect.value = savePermitType;
        }

        // Save permit type when changed
        permitSelect.addEventListener('change', function() {
            localStorage.setItem('selectedPermitType', this.value);
        });

// Load the saved value from localStorage
var savedDateType = localStorage.getItem('selectedDateType');
        if (savedDateType) {
            dateTypeSelect.value = savedDateType;
        }

        // Save the selected value to localStorage when changed
        dateTypeSelect.addEventListener('change', function() {
            localStorage.setItem('selectedDateType', this.value);
        });


        var saveFromDate = localStorage.getItem('selectedFromDate');
        if (saveFromDate) {
            fromDateSelect.value = saveFromDate;
        }

        // Save the selected value to localStorage when changed
        fromDateSelect.addEventListener('change', function() {
            localStorage.setItem('selectedFromDate', this.value);
        });


        var saveToDate = localStorage.getItem('selectedToDate');
        if (saveToDate) {
            todateSelect.value = saveToDate;
        }

        // Save the selected value to localStorage when changed
        todateSelect.addEventListener('change', function() {
            localStorage.setItem('selectedToDate', this.value);
        });


        var saveAgging = localStorage.getItem('selectedAgging');
        if (saveAgging) {
            agging.value = saveAgging;
        }

        // Save the selected value to localStorage when changed
        agging.addEventListener('change', function() {
            localStorage.setItem('selectedAgging', this.value);
        });

    if(isAdminView){
        var saveBa = localStorage.getItem('selectedBA');
        if (saveBa) {
            selba.value = saveBa;
        }

        // Save the selected value to localStorage when changed
        selba.addEventListener('change', function() {
            localStorage.setItem('selectedBA', this.value);
        });
    }


            

            $('#myTable , #snTable').DataTable({
                aaSorting: [
                    [3, 'desc']
                ],
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                // initComplete: function () {                    
                //     this.api().page(10).draw( 'page' );
                // }

            });


    // Initialize dashboard DataTables
    $('#dashboardTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });
    
    // Chart instances
    let statusChart = null;
    let agingChart = null;
    
    // Function to initialize charts
    function initializeCharts() {
        // Destroy existing charts if they exist
        if (statusChart) {
            statusChart.destroy();
        }
        if (agingChart) {
            agingChart.destroy();
        }
        
        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            statusChart = new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chartLabels); ?>,
                    datasets: [
                        {
                            label: 'Completed',
                            data: <?php echo json_encode($completedData); ?>,
                            backgroundColor: '#4CAF50',
                            borderColor: '#388E3C',
                            borderWidth: 1
                        },
                        {
                            label: 'In Progress',
                            data: <?php echo json_encode($inprogressData); ?>,
                            backgroundColor: '#2196F3',
                            borderColor: '#1976D2',
                            borderWidth: 1
                        },
                        {
                            label: 'KIV',
                            data: <?php echo json_encode($kivData); ?>,
                            backgroundColor: '#FF9800',
                            borderColor: '#F57C00',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of SN'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'BA'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Aging Analysis Chart (Stacked Bar)
        const agingCtx = document.getElementById('agingChart');
        if (agingCtx) {
            agingChart = new Chart(agingCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($agingLabels); ?>,
                    datasets: [
                        {
                            label: '1-5 Days',
                            data: <?php echo json_encode($age1_5Data); ?>,
                            backgroundColor: '#4CAF50',
                            borderColor: '#388E3C',
                            borderWidth: 1
                        },
                        {
                            label: '6-14 Days',
                            data: <?php echo json_encode($age6_14Data); ?>,
                            backgroundColor: '#2196F3',
                            borderColor: '#1976D2',
                            borderWidth: 1
                        },
                        {
                            label: '15-30 Days',
                            data: <?php echo json_encode($age15_30Data); ?>,
                            backgroundColor: '#FF9800',
                            borderColor: '#F57C00',
                            borderWidth: 1
                        },
                        {
                            label: '31-60 Days',
                            data: <?php echo json_encode($age31_60Data); ?>,
                            backgroundColor: '#ff22ed',
                            borderColor: '#d815be',
                            borderWidth: 1
                        },
                        {
                            label: '>60 Days',
                            data: <?php echo json_encode($ageGt60Data); ?>,
                            backgroundColor: '#F44336',
                            borderColor: '#D32F2F',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'BA'
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of SN'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
    
    // Initialize charts when dashboard tab is shown
    $('#dashboard-tab').on('shown.bs.tab', function() {
        initializeCharts();
    });
    
    // Initialize charts if dashboard tab is active on page load
    if ($('#dashboard-tab').hasClass('active')) {
        setTimeout(function() {
            initializeCharts();
        }, 100);
    }


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

        var savedButtonclick = localStorage.getItem('buttonClicked');
        if(savedButtonclick){
        if (savedButtonclick=='true') {
            //localStorage.removeItem('buttonClicked');
            localStorage.setItem('buttonClicked', 'false');
            savedButtonclick = localStorage.getItem('buttonClicked');
            let dropdown = document.getElementById('searchBA');
            let selectedbsValue = dropdown.value;
            if(localStorage.getItem('selectedStatus')!="null"){
            setTimeout(function(){
                adminSearch(selectedbsValue,localStorage.getItem('selectedStatus'))   
            }, 1000);
        }

        }else{
            localStorage.setItem('buttonClicked', 'true');
            savedButtonclick = localStorage.getItem('buttonClicked');
            let button = document.getElementById('mysubmit');
            let dropdown = document.getElementById('searchBA');
            let selectedbsValue = dropdown.value;
            button.click();
            if(localStorage.getItem('selectedStatus')!="null"){
            setTimeout(function(){
                adminSearch(selectedbsValue,localStorage.getItem('selectedStatus'))   
            }, 1000);
        }
        }
    }

            $('#mysubmit').on('click', function() {
                if(savedButtonclick!='true'){
                    localStorage.setItem('buttonClicked', 'true');
                    savedButtonclick = localStorage.getItem('buttonClicked');
                    window.location.reload(true) ;  
                }    
            });

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
           //on display remarks modal - using event delegation for dynamic elements
           //on display remarks modal - using event delegation for dynamic elements
          $(document).on('click', '.modal-remark-link', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var detail = $(this).data('remark');
    var id = $(this).data('id');
    var snNo = $(this).data('sn') || '—';
    var address = $(this).data('address') || '—';
    
    console.log('Opening remark modal - ID:', id, 'SN:', snNo, 'Address:', address);
    
    $('#remark-detail').val(detail);
    $('#update-remarks-id').val(id);
    $('#view-sn-no').html(snNo);
    $('#view-address').html(address);
    $('#remark-date').val('');
    
    // Show the remark modal
    $('#remarkModal').modal('show');
    
    // Ensure proper z-index when opened from another modal
    if ($('#recordsModal').hasClass('show')) {
        $('#remarkModal').css('z-index', parseInt($('#recordsModal').css('z-index')) + 10);
    }
});

            // When remark modal closes, ensure records modal is still visible
            $('#remarkModal').on('hidden.bs.modal', function () {
                if ($('#recordsModal').hasClass('show')) {
                    $('body').addClass('modal-open');
                }
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





         function calculateAging(cspDate, completionDate) {
        if (!cspDate) return 0;
        
        const startDate = new Date(cspDate);
        const endDate = completionDate ? new Date(completionDate) : new Date();
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    }

    // Handle click on aging cells
  // Handle click on aging cells
// Handle click on aging cells
// Handle click on aging cells
// Handle click on aging cells  
// Handle click on aging cells - NEW APPROACH using PHP data
// Handle click on aging cells - FIXED aging calculation
$('.aging-cell, .aging-cell-total').on('click', function() {
    const ba = $(this).data('ba');
    const minDays = parseInt($(this).data('min'));
    const maxDays = parseInt($(this).data('max'));
    const cellValue = parseInt($(this).text().trim());

    if (!cellValue || cellValue === 0) return;

    console.log('=== FILTERING ===');
    console.log('BA:', ba, '| Range:', minDays, '-', maxDays, '| Expected:', cellValue);
   console.log('Total aging records available:', allAgingRecords.length);

// Filter records using the aging-specific dataset
const filteredRecords = allAgingRecords.filter(record => {
        // Calculate aging EXACTLY like PHP does
        let aging = 0;
        if (record.csp_paid_date) {
            const cspDate = new Date(record.csp_paid_date);
            // For aging table, ALWAYS use tarikh_siap if available (even if empty string), otherwise use today
            // Match PHP logic exactly: check for both null and empty string
          let endDate;
            if (record.tarikh_siap && record.tarikh_siap !== '' && record.tarikh_siap !== null) {
                endDate = new Date(record.tarikh_siap);
            } else {
                // Use today's date at midnight for consistent comparison
                endDate = new Date();
                endDate.setHours(0, 0, 0, 0);
            }

            // cspDate already declared above, just set hours
            cspDate.setHours(0, 0, 0, 0);
            
            // Calculate days difference and add 1 (matching PHP's DATE_PART + 1)
            const timeDiff = endDate.getTime() - cspDate.getTime();
            aging = Math.floor(timeDiff / (1000 * 60 * 60 * 24)) + 1;
        }

        // Check filters - aging table only shows "Inprogress" status
        const matchBA = (ba === 'all' || record.ba === ba);
        const matchAge = (aging >= minDays && aging <= maxDays);
        const matchStatus = record.status.toLowerCase().replace(/\s/g, '') === 'inprogress';

        if (matchBA && matchAge && matchStatus) {
            console.log('✓ Match:', record.no_sn, 'Aging:', aging, 'Status:', record.status);
        }

        return matchBA && matchAge && matchStatus;
    });

    console.log('Filtered count:', filteredRecords.length);
    console.log('Expected count:', cellValue);

    if (filteredRecords.length !== cellValue) {
        console.warn('⚠️ MISMATCH! Check aging calculation');
    }

    // Show modal
showRecordsModal(ba, minDays, maxDays, filteredRecords, allAgingRecords.length, cellValue);
});

function showRecordsModal(ba, minDays, maxDays, records, totalSearched, expected) {
    const ageRangeText = maxDays > 1000 ? `>${minDays-1} days` : `${minDays}-${maxDays} days`;
    const baText = ba === 'all' ? 'All BA' : ba;
    $('#recordsModalLabel').text(`${baText} - ${ageRangeText} (${records.length} records)`);

    const tbody = $('#filteredRecordsBody');
    tbody.empty();

    if (records.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="10" class="text-center text-warning">
                    No matches found<br>
                    <small>Searched: ${totalSearched} records | Expected: ${expected}</small>
                </td>
            </tr>
        `);
    } else {
        records.forEach(record => {
            // Calculate aging for display
            let aging = 0;
            if (record.csp_paid_date) {
                const cspDate = new Date(record.csp_paid_date);
                let endDate;
                if (record.tarikh_siap && record.tarikh_siap !== '' && record.tarikh_siap !== null) {
                    endDate = new Date(record.tarikh_siap);
                } else {
                    endDate = new Date();
                    endDate.setHours(0, 0, 0, 0);
                }
                cspDate.setHours(0, 0, 0, 0);
                const timeDiff = endDate.getTime() - cspDate.getTime();
                aging = Math.floor(timeDiff / (1000 * 60 * 60 * 24)) + 1;
            }

            const remarkText = record.remark && record.remark.length > 15 
                ? record.remark.substring(0, 15) + '...' 
                : (record.remark || '');
            
            // CRITICAL FIX: Properly escape for HTML attribute using JavaScript
            // This handles quotes, newlines, and special characters
            const escapedRemarkForAttr = (record.remark || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\n/g, '&#10;')
                .replace(/\r/g, '');
            
            tbody.append(`
                <tr>
                    <td>${record.ba}</td>
                    <td><a href="./sn-monitoring/edit.php?no_sn=${record.no_sn}" class="text-decoration-none">${record.no_sn}</a></td>
                    <td>${record.jenis_sn}</td>
                    <td>${record.permit_sn || ''}</td>
                    <td>${record.jenis_sambungan}</td>
                    <td><strong>${aging}</strong></td>
                    <td>${record.csp_paid_date || ''}</td>
                    <td>${record.tarikh_siap || ''}</td>
                    <td>${record.status}</td>
                    <td><a type='button' class='dropdown-item btn btn-warning btn-sm text-dark modal-remark-link' style='display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 600; background-color: #ffc107; border: 2px solid #ff9800; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer;' data-remark="${escapedRemarkForAttr}" data-id="${record.id}" data-sn="${record.no_sn}" data-address="${(record.alamat || '').replace(/"/g, '&quot;')}">${remarkText}</a></td>

                </tr>
            `);
        });
    }
    
    // Destroy and reinitialize DataTable
    if ($.fn.DataTable.isDataTable('#filteredRecordsTable')) {
        $('#filteredRecordsTable').DataTable().destroy();
    }
    
    $('#filteredRecordsTable').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "order": [[5, 'desc']]
    });

    $('#recordsModal').modal('show');
}
            
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
            localStorage.setItem('selectedStatus', status);
           // localStorage.setItem('buttonClicked', 'true');
            localStorage.setItem('selectedBA', ba);

            var table = $('#myTable').DataTable();
            var table2 = $('#snTable').DataTable();

            if (userba == '') {
                table.columns(0).search(ba)
                table.columns(6).search(status); // Filter Column 2
            }else{
                table.columns(5).search(status);
            }



            table2.columns(0).search(ba);
            table2.columns(8).search(status);

           table.draw();
            table2.draw();
        }

        // Filter tables for OH pending stats (user boxes + admin table cells)
        // Admin QR table cols (has BA col): 0=BA, 3=JENIS SAMBUNGAN, 7=QR, 8=PIAT, 9=ERMS
        // User  QR table cols (no BA col):  2=JENIS SAMBUNGAN, 6=QR, 7=PIAT, 8=ERMS
        // SN table cols (both):             0=BA, 4=JENIS SAMBUNGAN
        function filterOHPending(type, ba) {
            var qrTable = $('#myTable').DataTable();
            var snTable = $('#snTable').DataTable();
            var isAdmin = isAdminView;

            // Admin QR table has BA col at 0, shifting all others by 1
            var qrSambunganCol = isAdmin ? 3 : 2;
            var qrCol          = isAdmin ? 7 : 6;
            var piatCol        = isAdmin ? 8 : 7;
            var ermsCol        = isAdmin ? 9 : 8;

            // Clear all filters first
            qrTable.columns().search('');
            snTable.columns().search('');
            qrTable.search('');
            snTable.search('');

            // Apply BA filter when admin clicks a specific row
            if (isAdmin && ba) {
                qrTable.columns(0).search(ba, false, false, true);
                snTable.columns(0).search(ba, false, false, true);
            }

            if (type === 'total') {
                snTable.columns(4).search('^OH$', true, false, true);
                snTable.draw();
                document.getElementById('profile-tab').click();
                localStorage.setItem('activeTab', 'profile-tab');
            } else {
                // Filter to OH records only
                qrTable.columns(qrSambunganCol).search('^OH$', true, false, true);

                // Search hidden text labels added to each cell (reliable vs unicode chars)
                if (type === 'no_qr') {
                    qrTable.columns(qrCol).search('qr_pending', false, false, false);
                } else if (type === 'no_piat') {
                    qrTable.columns(piatCol).search('piat_pending', false, false, false);
                } else if (type === 'no_erms') {
                    qrTable.columns(ermsCol).search('erms_pending', false, false, false);
                }

                qrTable.draw();
                document.getElementById('home-tab').click();
                localStorage.setItem('activeTab', 'home-tab');
            }
        }
    </script>
</body>

</html>