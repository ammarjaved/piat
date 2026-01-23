<?php
session_start();
if (!isset($_SESSION['user_name']) && !isset($_SESSION['user_id'])) {
    header('location:./auth/login.php');
    exit;
}
include './services/connection.php';

// Data Fetching Logic - Embedded directly
$col_name = 'both';
$from = '';
$to = '';
$from_siap = '';
$to_siap = '';
$from_paid = '';
$to_paid = '';

if (isset($_POST['date_type']) && $_POST['date_type'] != '') {
    $dateType = $_POST['date_type'];
    $from = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to = isset($_POST['to_date']) ? $_POST['to_date'] : '';
    
    if ($dateType == 'CSP') {
        $col_name = 'csp_paid_date';
    } elseif ($dateType == 'Completion') {
        $col_name = 'tarikh_siap';
    } elseif ($dateType == 'Both') {
        $col_name = 'both';
        $from_siap = $from;
        $to_siap = $to;
        $from_paid = $from;
        $to_paid = $to;
    }
}

// Fetch records based on filter or default
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submitButton'] == 'filter') {
    $ba = isset($_POST['searchBA']) ? $_POST['searchBA'] : '';
    
    if ($col_name == 'both') {
        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba AND ((csp_paid_date >= :from_paid AND csp_paid_date <= :to_paid) OR (tarikh_siap >= :from_siap AND tarikh_siap <= :to_siap)) and (status in ('Inprogress','KIV') or complete_date>='2025-01-01') ORDER BY csp_paid_date DESC");
        $stmt->bindParam(':from_paid', $from_paid);
        $stmt->bindParam(':to_paid', $to_paid);
        $stmt->bindParam(':from_siap', $from_siap);
        $stmt->bindParam(':to_siap', $to_siap);
        $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba AND $col_name >= :from AND $col_name <= :to and (status in ('Inprogress','KIV') or complete_date>='2026-01-01') ORDER BY csp_paid_date DESC");
        $stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);
    }
} else {
    // without filter
    if ($_SESSION['user_name'] == 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr where status in ('Inprogress','KIV') or complete_date>='2025-01-01' ORDER BY id DESC");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba and (status in ('Inprogress','KIV') or complete_date>='2025-01-01') ORDER BY csp_paid_date DESC, id DESC");
        $stmt->bindValue(':ba', '%' . $_SESSION['user_ba'] . '%', PDO::PARAM_STR);
    }
    $stmt->execute();
}

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Apply permit type filter
if (isset($_POST['permit_type']) && $_POST['permit_type'] != '') {
    $records = array_filter($records, function($record) {
        return $record['permit_sn'] === $_POST['permit_type'];
    });
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
    
    <title>AD KL SN, QR and PIAT Monitoring</title>
    
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    
    <script>
        var username='<?php echo $_SESSION['user_name']?>';
    </script>    
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="">AD KL SN, QR and PIAT Monitoring</a>
            <a href="./auth/logout.php" class="btn btn-sm btn-secondary">logout</a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container shadow p-5 my-5 bg-white">
        <!-- Show Message if Session has Message -->
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

        <!-- Action Buttons -->
        <?php include './includes/action-buttons.php'; ?>

        <!-- Filter Form -->
        <?php include './includes/filter-form.php'; ?>

        <!-- Dashboard Count -->
        <?php 
        if ($_SESSION['user_name'] == 'admin') {
            include './admin/dashboard-count.php';
        } else {
            include './user/dashboard-count.php';
        }
        ?>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="sn-tab" data-bs-toggle="tab" data-bs-target="#sn-monitoring" 
                    type="button" role="tab" aria-controls="sn-monitoring" aria-selected="true">
                    SN Monitoring
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="qr-tab" data-bs-toggle="tab" data-bs-target="#qr-table" 
                    type="button" role="tab" aria-controls="qr-table" aria-selected="false">
                    QR
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-charts" 
                    type="button" role="tab" aria-controls="dashboard-charts" aria-selected="false">
                    Dashboard
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            <!-- SN Monitoring Tab -->
            <div class="tab-pane fade show active" id="sn-monitoring" role="tabpanel" aria-labelledby="sn-tab">
                <?php include './tables/sn-table.php'; ?>
            </div>

            <!-- QR Tab -->
            <div class="tab-pane fade" id="qr-table" role="tabpanel" aria-labelledby="qr-tab">
                <?php include './tables/qr-table.php'; ?>
            </div>

            <!-- Dashboard Tab -->
            <div class="tab-pane fade" id="dashboard-charts" role="tabpanel" aria-labelledby="dashboard-tab">
                <div class="py-3">
                    <div class="row" id="dashboard-content">
                        <div class="col-12 text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <?php include './includes/modals.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./assets/js/main.js"></script>
    <script src="./assets/js/dashboard.js"></script>
</body>
</html>