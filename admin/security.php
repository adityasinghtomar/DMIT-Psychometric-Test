<?php
/**
 * Security Logs - DMIT Psychometric Test System
 * Monitor security events and audit trails
 */

require_once '../config/config.php';

Security::requireAuth();
Security::requireRole('admin');

$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$eventType = $_GET['event_type'] ?? '';
$severity = $_GET['severity'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Build filter conditions
    $whereConditions = [];
    $params = [];
    
    if (!empty($eventType)) {
        $whereConditions[] = 'event_type = ?';
        $params[] = $eventType;
    }
    
    if (!empty($severity)) {
        $whereConditions[] = 'severity = ?';
        $params[] = $severity;
    }
    
    if (!empty($dateFrom)) {
        $whereConditions[] = 'DATE(created_at) >= ?';
        $params[] = $dateFrom;
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = 'DATE(created_at) <= ?';
        $params[] = $dateTo;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM security_events $whereClause";
    $stmt = $conn->prepare($countQuery);
    $stmt->execute($params);
    $totalRecords = $stmt->fetch()['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // Get security events
    $query = "
        SELECT se.*, u.first_name, u.last_name, u.username
        FROM security_events se
        LEFT JOIN users u ON se.user_id = u.id
        $whereClause
        ORDER BY se.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $securityEvents = $stmt->fetchAll();
    
    // Get event type counts for last 24 hours
    $stmt = $conn->prepare("
        SELECT event_type, COUNT(*) as count 
        FROM security_events 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        GROUP BY event_type
        ORDER BY count DESC
    ");
    $stmt->execute();
    $eventCounts = $stmt->fetchAll();
    
    // Get severity distribution
    $stmt = $conn->prepare("
        SELECT severity, COUNT(*) as count 
        FROM security_events 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY severity
    ");
    $stmt->execute();
    $severityCounts = $stmt->fetchAll();
    
} catch (Exception $e) {
    $securityEvents = [];
    $eventCounts = [];
    $severityCounts = [];
    $totalPages = 0;
    error_log("Security logs error: " . $e->getMessage());
}

$pageTitle = 'Security Logs - ' . APP_NAME;
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-tachometer-alt"></i> Main Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-cog"></i> Admin Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users"></i> User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="security.php">
                            <i class="fas fa-shield-alt"></i> Security Logs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cogs"></i> System Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Security Logs</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Security Overview -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-chart-bar"></i> Event Types (Last 24 Hours)</h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($eventCounts)): ?>
                                <p class="text-muted">No events in the last 24 hours.</p>
                            <?php else: ?>
                                <?php foreach ($eventCounts as $event): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><?php echo ucfirst(str_replace('_', ' ', $event['event_type'])); ?></span>
                                    <span class="badge bg-primary"><?php echo $event['count']; ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-exclamation-triangle"></i> Severity Distribution (Last 7 Days)</h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($severityCounts)): ?>
                                <p class="text-muted">No events in the last 7 days.</p>
                            <?php else: ?>
                                <?php foreach ($severityCounts as $severity): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>
                                        <?php
                                        $badgeClass = [
                                            'low' => 'bg-success',
                                            'medium' => 'bg-warning',
                                            'high' => 'bg-danger',
                                            'critical' => 'bg-dark'
                                        ][$severity['severity']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($severity['severity']); ?></span>
                                    </span>
                                    <span><?php echo $severity['count']; ?> events</span>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6><i class="fas fa-filter"></i> Filters</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Event Type</label>
                            <select class="form-select" name="event_type">
                                <option value="">All Types</option>
                                <option value="login_attempt" <?php echo $eventType === 'login_attempt' ? 'selected' : ''; ?>>Login Attempt</option>
                                <option value="login_success" <?php echo $eventType === 'login_success' ? 'selected' : ''; ?>>Login Success</option>
                                <option value="login_failure" <?php echo $eventType === 'login_failure' ? 'selected' : ''; ?>>Login Failure</option>
                                <option value="account_locked" <?php echo $eventType === 'account_locked' ? 'selected' : ''; ?>>Account Locked</option>
                                <option value="password_reset" <?php echo $eventType === 'password_reset' ? 'selected' : ''; ?>>Password Reset</option>
                                <option value="suspicious_activity" <?php echo $eventType === 'suspicious_activity' ? 'selected' : ''; ?>>Suspicious Activity</option>
                                <option value="data_access" <?php echo $eventType === 'data_access' ? 'selected' : ''; ?>>Data Access</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Severity</label>
                            <select class="form-select" name="severity">
                                <option value="">All Levels</option>
                                <option value="low" <?php echo $severity === 'low' ? 'selected' : ''; ?>>Low</option>
                                <option value="medium" <?php echo $severity === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="high" <?php echo $severity === 'high' ? 'selected' : ''; ?>>High</option>
                                <option value="critical" <?php echo $severity === 'critical' ? 'selected' : ''; ?>>Critical</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>">
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="security.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Events Table -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Security Events (<?php echo $totalRecords; ?> total)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($securityEvents)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <h5>No Security Events Found</h5>
                            <p class="text-muted">No events match your current filters.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Event Type</th>
                                        <th>User</th>
                                        <th>IP Address</th>
                                        <th>Severity</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($securityEvents as $event): ?>
                                    <tr>
                                        <td>
                                            <small><?php echo formatDate($event['created_at'], 'M d, Y g:i:s A'); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo ucfirst(str_replace('_', ' ', $event['event_type'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($event['user_id']): ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($event['first_name'] . ' ' . $event['last_name']); ?></strong>
                                                    <br><small class="text-muted">@<?php echo htmlspecialchars($event['username']); ?></small>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">Anonymous</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <code><?php echo htmlspecialchars($event['ip_address']); ?></code>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = [
                                                'low' => 'bg-success',
                                                'medium' => 'bg-warning',
                                                'high' => 'bg-danger',
                                                'critical' => 'bg-dark'
                                            ][$event['severity']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo ucfirst($event['severity']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($event['details']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                        onclick="showDetails('<?php echo htmlspecialchars(json_encode($event['details'])); ?>')">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <?php 
                                $filterParams = array_filter([
                                    'event_type' => $eventType,
                                    'severity' => $severity,
                                    'date_from' => $dateFrom,
                                    'date_to' => $dateTo
                                ]);
                                echo generatePagination($page, $totalPages, 'security.php', $filterParams); 
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="eventDetails" class="bg-light p-3 rounded"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showDetails(details) {
    try {
        const parsed = JSON.parse(details);
        document.getElementById('eventDetails').textContent = JSON.stringify(parsed, null, 2);
    } catch (e) {
        document.getElementById('eventDetails').textContent = details;
    }
    new bootstrap.Modal(document.getElementById('detailsModal')).show();
}
</script>

<?php include '../includes/footer.php'; ?>
