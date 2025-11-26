<?php
session_start();

// Redirect ke login jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Sample activities data
$activities = [
    ['id' => 1, 'title' => 'Meeting Tim', 'time' => 'Hari ini, 10:00', 'completed' => true],
    ['id' => 2, 'title' => 'Presentasi Proyek', 'time' => 'Besok, 14:00', 'completed' => false],
    ['id' => 3, 'title' => 'Review Bulanan', 'time' => '28 Feb, 09:00', 'completed' => false],
    ['id' => 4, 'title' => 'Laporan Keuangan', 'time' => '01 Mar, 16:00', 'completed' => false],
];

// Add new activity
if (isset($_POST['add_activity'])) {
    $new_activity = [
        'id' => count($activities) + 1,
        'title' => $_POST['activity_title'],
        'time' => $_POST['activity_time'],
        'completed' => false
    ];
    $activities[] = $new_activity;
}

// Delete activity
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    // In a real application, you would delete from database
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CatatanKu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-body">

<!-- Navigation -->
<nav class="dashboard-nav">
    <div class="nav-container">
        <div class="nav-brand">
            <i class="fas fa-tasks"></i>
            <span>CatatanKu</span>
        </div>
        <div class="nav-links">
            <span class="welcome-text">Halo, <?php echo $_SESSION['username']; ?></span>
            <a href="?" class="nav-link logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Keluar
            </a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="dashboard-container">
    <!-- Sidebar (Removed Settings and Profile) -->
    <aside class="sidebar">
        <div class="sidebar-menu">
            <a href="#" class="menu-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-calendar"></i>
                <span>Kalender</span>
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>Statistik</span>
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-bell"></i>
                <span>Notifikasi</span>
            </a>
        </div>
    </aside>

    <!-- Content Area -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Selamat Datang di CatatanKu</h1>
            <p>Kelola kegiatan harian Anda dengan mudah dan efisien</p>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Kegiatan</h3>
                    <span class="stat-number"><?php echo count($activities); ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Selesai</h3>
                    <span class="stat-number"><?php echo count(array_filter($activities, function($a) { return $a['completed']; })); ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Belum Selesai</h3>
                    <span class="stat-number"><?php echo count(array_filter($activities, function($a) { return !$a['completed']; })); ?></span>
                </div>
            </div>
        </div>

        <!-- Add Activity Form -->
        <div class="add-activity-card">
            <h3>Tambah Kegiatan Baru</h3>
            <form method="POST" class="activity-form">
                <div class="form-group">
                    <input type="text" name="activity_title" placeholder="Judul kegiatan" required>
                    <input type="text" name="activity_time" placeholder="Waktu (contoh: Hari ini, 10:00)" required>
                    <button type="submit" name="add_activity" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Tambah
                    </button>
                </div>
            </form>
        </div>

        <!-- Activities List -->
        <div class="activities-section">
            <h2>Daftar Kegiatan</h2>
            <div class="activities-list">
                <?php foreach ($activities as $activity): ?>
                <div class="activity-item <?php echo $activity['completed'] ? 'completed' : ''; ?>">
                    <div class="activity-check">
                        <i class="fas fa-<?php echo $activity['completed'] ? 'check-circle' : 'circle'; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <h4><?php echo $activity['title']; ?></h4>
                        <span class="activity-time"><?php echo $activity['time']; ?></span>
                    </div>
                    <div class="activity-actions">
                        <button class="btn-icon edit-btn">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="?delete=<?php echo $activity['id']; ?>" class="btn-icon delete-btn">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle activity completion
    const activityChecks = document.querySelectorAll('.activity-check');
    activityChecks.forEach(check => {
        check.addEventListener('click', function() {
            const icon = this.querySelector('i');
            const activityItem = this.closest('.activity-item');
            
            if (icon.classList.contains('fa-circle')) {
                icon.className = 'fas fa-check-circle';
                activityItem.classList.add('completed');
            } else {
                icon.className = 'fas fa-circle';
                activityItem.classList.remove('completed');
            }
        });
    });

    // Edit activity
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const activityItem = this.closest('.activity-item');
            const title = activityItem.querySelector('h4').textContent;
            const time = activityItem.querySelector('.activity-time').textContent;
            
            // In a real app, you would show an edit form/modal
            alert(`Edit: ${title}\nWaktu: ${time}`);
        });
    });

    // Confirm delete
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) {
                e.preventDefault();
            }
        });
    });
});
</script>

<style>
/* ====== VARIABLES ====== */
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #10b981;
    --accent: #f59e0b;
    --danger: #ef4444;
    --dark: #1e293b;
    --darker: #0f172a;
    --light: #f8fafc;
    --gray: #64748b;
    --gray-light: #cbd5e1;
    --card-bg: rgba(255, 255, 255, 0.08);
    --card-border: rgba(255, 255, 255, 0.12);
    --shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    --transition: all 0.3s ease;
}

/* ====== RESET & BASE ====== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    color: var(--light);
    line-height: 1.6;
    background: linear-gradient(135deg, var(--darker), var(--dark));
    min-height: 100vh;
}

/* ====== NAVIGATION ====== */
.dashboard-nav {
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--card-border);
    padding: 1rem 0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--light);
}

.nav-brand i {
    color: var(--primary);
    font-size: 1.75rem;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.welcome-text {
    color: var(--gray-light);
    font-weight: 500;
}

.logout-btn {
    background: var(--danger);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logout-btn:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

/* ====== DASHBOARD LAYOUT ====== */
.dashboard-container {
    display: flex;
    max-width: 1400px;
    margin: 0 auto;
    min-height: calc(100vh - 80px);
}

/* ====== SIDEBAR ====== */
.sidebar {
    width: 280px;
    background: var(--card-bg);
    border-right: 1px solid var(--card-border);
    padding: 2rem 0;
    backdrop-filter: blur(10px);
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 0 1.5rem;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    color: var(--gray-light);
    text-decoration: none;
    border-radius: 12px;
    transition: var(--transition);
    font-weight: 500;
}

.menu-item:hover {
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary);
}

.menu-item.active {
    background: rgba(99, 102, 241, 0.2);
    color: var(--primary);
    border-left: 3px solid var(--primary);
}

.menu-item i {
    width: 20px;
    text-align: center;
}

/* ====== MAIN CONTENT ====== */
.main-content {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
}

/* ====== WELCOME SECTION ====== */
.welcome-section {
    margin-bottom: 2rem;
}

.welcome-section h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.welcome-section p {
    color: var(--gray-light);
    font-size: 1.1rem;
}

/* ====== STATS GRID ====== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    backdrop-filter: blur(10px);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 1.5rem;
    color: white;
}

.stat-info h3 {
    font-size: 0.9rem;
    color: var(--gray-light);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--light);
}

/* ====== ADD ACTIVITY FORM ====== */
.add-activity-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
}

.add-activity-card h3 {
    margin-bottom: 1.5rem;
    color: var(--light);
    font-size: 1.25rem;
}

.activity-form .form-group {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
}

.activity-form input {
    flex: 1;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    padding: 1rem;
    color: var(--light);
    font-size: 1rem;
    transition: var(--transition);
}

.activity-form input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

.activity-form input::placeholder {
    color: var(--gray);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

/* ====== ACTIVITIES LIST ====== */
.activities-section h2 {
    margin-bottom: 1.5rem;
    color: var(--light);
    font-size: 1.5rem;
}

.activities-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    backdrop-filter: blur(10px);
    transition: var(--transition);
}

.activity-item:hover {
    transform: translateX(5px);
    box-shadow: var(--shadow);
}

.activity-item.completed {
    opacity: 0.7;
    border-color: var(--secondary);
}

.activity-check {
    cursor: pointer;
    color: var(--gray);
    transition: var(--transition);
}

.activity-item.completed .activity-check {
    color: var(--secondary);
}

.activity-check:hover {
    color: var(--primary);
}

.activity-content {
    flex: 1;
}

.activity-content h4 {
    color: var(--light);
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.activity-item.completed .activity-content h4 {
    text-decoration: line-through;
    color: var(--gray);
}

.activity-time {
    color: var(--gray);
    font-size: 0.9rem;
}

.activity-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1);
    color: var(--gray-light);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-icon:hover {
    background: var(--primary);
    color: white;
    transform: scale(1.1);
}

.delete-btn {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.delete-btn:hover {
    background: var(--danger);
    color: white;
}

/* ====== RESPONSIVE DESIGN ====== */
@media (max-width: 768px) {
    .dashboard-container {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid var(--card-border);
    }
    
    .sidebar-menu {
        flex-direction: row;
        overflow-x: auto;
        padding: 0 1rem;
    }
    
    .menu-item {
        white-space: nowrap;
    }
    
    .main-content {
        padding: 1rem;
    }
    
    .activity-form .form-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .nav-links {
        gap: 1rem;
    }
    
    .welcome-text {
        display: none;
    }
}

@media (max-width: 480px) {
    .nav-container {
        padding: 0 1rem;
    }
    
    .main-content {
        padding: 1rem 0.5rem;
    }
    
    .activity-item {
        padding: 1rem;
    }
    
    .activity-actions {
        flex-direction: column;
    }
}
</style>
</body>
</html>