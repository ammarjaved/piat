<div class="table-responsive table-bordered py-3" style="overflow-y:auto;">
    <table id="snTable" class="table table-striped table-bordered">
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
            // Function to check aging
            function checkAging($record) {
                if (!$record['csp_paid_date']) return 0;
                
                $agingDateTime = new DateTime($record['csp_paid_date']);
                $todayDateTime = new DateTime();
                $interval = $agingDateTime->diff($todayDateTime);
                return intval($interval->format('%a'));
            }
            
            foreach ($records as $record) {
                $myaging = checkAging($record);
                
                // Apply aging filter
                if(isset($_POST['aging']) && $_POST['aging'] != '') {
                    if($_POST['aging'] === '>60') {
                        if($myaging <= 60) {
                            continue;
                        }
                    } else {
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
                
                // SN NO with link to detail
                echo "<td><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}'>";
                echo "{$record['no_sn']}</a></td>";
                
                echo "<td>{$record['jenis_sn']}</td>";
                echo "<td>{$record['permit_sn']}</td>";
                echo "<td>{$record['jenis_sambungan']}</td>";
                
                // Calculate and display aging
                if ($record['csp_paid_date'] != '') {
                    $agingDateTime = new DateTime($record['csp_paid_date']);
                    $todayDateTime = $record['tarikh_siap'] != '' ? new DateTime($record['tarikh_siap']) : new DateTime();
                    $interval = $agingDateTime->diff($todayDateTime);
                    $differenceInDays = $interval->format('%a');
                    echo '<td>' . ($differenceInDays + 1) . '</td>';
                } else {
                    echo "<td>{$record['aging_days']}</td>";
                }
                
                echo "<td>{$record['csp_paid_date']}</td>";
                echo "<td>{$record['tarikh_siap']}</td>";
                echo "<td>{$record['status']}</td>";
                
                // Remarks with truncation
                $remark = $record['remark'];
                if ($remark) {
                    if (strlen($remark) > 15) {
                        $remark = substr($remark, 0, 15) . '...';
                    }
                }
                echo "<td><a type='button' class='dropdown-item btn btn-warning btn-sm text-dark' 
                    style='display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; 
                    font-weight: 600; background-color: #ffc107; border: 2px solid #ff9800; 
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);' 
                    data-bs-toggle='modal' 
                    data-remark='{$record['remark']}' 
                    data-id='{$record['id']}' 
                    data-bs-target='#remarkModal'>{$remark}</a></td>";
                
                // Actions dropdown
                echo "<td class='text-center'>
                    <div class='dropdown'>
                        <button class='btn' type='button' id='dropdownMenuButton1' 
                            data-bs-toggle='dropdown' aria-expanded='false'>
                            <img src='../images/three-dots-vertical.svg'>
                        </button>
                        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>
                            <li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}'>Edit SN</a></li>
                            <li><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}'>Detail</a></li>
                            <li><button type='button' class='dropdown-item' data-bs-toggle='modal' 
                                data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'>Delete</button></li>
                        </ul>
                    </div>
                </td>";
                
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>