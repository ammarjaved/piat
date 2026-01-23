// Dashboard Charts Management
let snMonitoringChart = null;
let snInProgressChart = null;
let dashboardTable1 = null;
let dashboardTable2 = null;

// Load dashboard data when tab is clicked
document.getElementById('dashboard-tab').addEventListener('click', function() {
    loadDashboardData();
});

function loadDashboardData() {
    fetch('./api/get-dashboard-data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderDashboard(data);
            } else {
                showError(data.error || 'Failed to load dashboard data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Network error occurred');
        });
}

function renderDashboard(data) {
    const content = `
        <div class="col-12">
            <h4 class="text-center mb-4">SN Monitoring Dashboard</h4>
        </div>
        
        <!-- Charts Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Status by BA</h5>
                    <canvas id="snMonitoringChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">In Progress - Aging Distribution</h5>
                    <canvas id="snInProgressChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tables Section -->
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">SN Monitoring Summary</h5>
                    <div class="table-responsive">
                        <table id="dashboardTable1" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>BA</th>
                                    <th>Completed</th>
                                    <th>In Progress</th>
                                    <th>KIV</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.snMonitoring.map(item => `
                                    <tr>
                                        <td>${item.ba}</td>
                                        <td>${item.completed}</td>
                                        <td>${item.inprogress}</td>
                                        <td>${item.kiv}</td>
                                        <td>${parseInt(item.completed) + parseInt(item.inprogress) + parseInt(item.kiv)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">In Progress - Aging Distribution</h5>
                    <div class="table-responsive">
                        <table id="dashboardTable2" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>BA</th>
                                    <th>1-5 Days</th>
                                    <th>6-14 Days</th>
                                    <th>15-30 Days</th>
                                    <th>30-60 Days</th>
                                    <th>>60 Days</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.snInProgress.map(item => `
                                    <tr>
                                        <td>${item.ba}</td>
                                        <td>${item['1-5']}</td>
                                        <td>${item['6-14']}</td>
                                        <td>${item['15-30']}</td>
                                        <td>${item['30-60']}</td>
                                        <td>${item['>60']}</td>
                                        <td>${parseInt(item['1-5']) + parseInt(item['6-14']) + parseInt(item['15-30']) + parseInt(item['30-60']) + parseInt(item['>60'])}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('dashboard-content').innerHTML = content;
    
    // Render charts
    renderSNMonitoringChart(data.snMonitoring);
    renderSNInProgressChart(data.snInProgress);
    
    // Initialize DataTables after content is rendered
    setTimeout(initDashboardDataTables, 100);
}

function initDashboardDataTables() {
    // Destroy existing tables if they exist
    if (dashboardTable1) {
        dashboardTable1.destroy();
    }
    if (dashboardTable2) {
        dashboardTable2.destroy();
    }
    
    // Initialize DataTables with custom settings
    dashboardTable1 = $('#dashboardTable1').DataTable({
        "pageLength": 10,
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "ordering": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "search": "Search:",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });
    
    dashboardTable2 = $('#dashboardTable2').DataTable({
        "pageLength": 10,
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "ordering": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "search": "Search:",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });
}

function renderSNMonitoringChart(data) {
    const ctx = document.getElementById('snMonitoringChart');
    
    // Destroy existing chart
    if (snMonitoringChart) {
        snMonitoringChart.destroy();
    }
    
    const labels = data.map(item => item.ba);
    
    snMonitoringChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Completed',
                    data: data.map(item => item.completed),
                    backgroundColor: '#198754',
                    borderColor: '#146c43',
                    borderWidth: 1
                },
                {
                    label: 'In Progress',
                    data: data.map(item => item.inprogress),
                    backgroundColor: '#fd7e14',
                    borderColor: '#dc6502',
                    borderWidth: 1
                },
                {
                    label: 'KIV',
                    data: data.map(item => item.kiv),
                    backgroundColor: '#0dcaf0',
                    borderColor: '#0aa2c0',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 50
                    }
                }
            }
        }
    });
}

function renderSNInProgressChart(data) {
    const ctx = document.getElementById('snInProgressChart');
    
    // Destroy existing chart
    if (snInProgressChart) {
        snInProgressChart.destroy();
    }
    
    const labels = data.map(item => item.ba);
    
    snInProgressChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '1-5 Days',
                    data: data.map(item => item['1-5']),
                    backgroundColor: '#198754',
                    borderColor: '#146c43',
                    borderWidth: 1
                },
                {
                    label: '6-14 Days',
                    data: data.map(item => item['6-14']),
                    backgroundColor: '#0dcaf0',
                    borderColor: '#0aa2c0',
                    borderWidth: 1
                },
                {
                    label: '15-30 Days',
                    data: data.map(item => item['15-30']),
                    backgroundColor: '#ffc107',
                    borderColor: '#cc9a06',
                    borderWidth: 1
                },
                {
                    label: '30-60 Days',
                    data: data.map(item => item['30-60']),
                    backgroundColor: '#fd7e14',
                    borderColor: '#dc6502',
                    borderWidth: 1
                },
                {
                    label: '>60 Days',
                    data: data.map(item => item['>60']),
                    backgroundColor: '#dc3545',
                    borderColor: '#b02a37',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    }
                }
            }
        }
    });
}

function showError(message) {
    document.getElementById('dashboard-content').innerHTML = `
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                <strong>Error:</strong> ${message}
            </div>
        </div>
    `;
}