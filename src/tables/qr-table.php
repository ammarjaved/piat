<div class="table-responsive table-bordered py-3" style="overflow-y:auto;">
    <table id="myTable" class="table table-striped table-responsive table-bordered" data-table>
        <thead>
            <tr>
                <?php if ($_SESSION['user_name'] == "admin") { ?>
                <th>BA</th>
                <?php } ?>
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
            foreach ($records as $record) {
                // Only show non-UG records
                if ($record['jenis_sambungan'] != 'UG') {
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
                    
                    // QR Status
                    echo "<td class='text-center'>";
                    if ($record['tarikh_siap'] != '') {
                        echo '<span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                    } else {
                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                    }
                    echo '</td>';
                    
                    // PIAT Status
                    echo '<td class="align-middle text-center">';
                    if ($record['piat_status'] == 'true') {
                        echo '<span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                    } else {
                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                    }
                    echo '</td>';
                    
                    // ERMS Status
                    echo "<td class='text-center'>";
                    if ($record['erms_status'] == 'done') {
                        echo '<span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                    } else {
                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                    }
                    echo '</td>';
                    
                    // Actions Dropdown
                    echo "<td class='text-center'>
                        <div class='dropdown'>
                            <button class='btn' type='button' id='dropdownMenuButton1' 
                                data-bs-toggle='dropdown' aria-expanded='false'>
                                <img src='../images/three-dots-vertical.svg'>
                            </button>
                            <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>
                                <li><a class='dropdown-item' href='./services/generateExcel.php?id={$record['id']}'>Download Excel</a></li>";
                    
                    echo "<li><a class='dropdown-item' href='./qr-foams/edit.php?no_sn={$record['no_sn']}'>";
                    echo $record['tarikh_siap'] != '' ? 'Edit QR' : 'Add QR';
                    echo '</a></li>';
                    
                    if ($record['piat_status'] == 'true') {
                        echo "<li><a class='dropdown-item' href='./generate-pdf/previewPDF.php?no_sn={$record['no_sn']}' target='_blank'>Preview PDF</a></li>";
                    } elseif ($record['qr'] == 'true') {
                        echo "<li><a class='dropdown-item' href='./services/foamRedirect.php?sn={$record['no_sn']}'>Fill Checklist</a></li>";
                    }
                    
                    echo "<li><a class='dropdown-item' href='./piat-foam/detail.php?no_sn={$record['no_sn']}'>Detail</a></li>";
                    echo "<li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}'>Edit SN</a></li>";
                    echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' 
                        data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'>Delete</button></li>";
                    
                    echo '</ul></div></td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>