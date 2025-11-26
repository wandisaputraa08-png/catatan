<?php
session_start();
include 'db.php';

// Proteksi: jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Tambah data
if (isset($_POST['tambah'])) {
    $kegiatan = mysqli_real_escape_string($conn, $_POST['kegiatan']);
    $tanggal = $_POST['tanggal'];
    $query = "INSERT INTO catatan (kegiatan, tanggal) VALUES ('$kegiatan', '$tanggal')";
    mysqli_query($conn, $query);
    $_SESSION['message'] = "✅ Kegiatan berhasil ditambahkan!";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Edit data
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $kegiatan = mysqli_real_escape_string($conn, $_POST['kegiatan']);
    $tanggal = $_POST['tanggal'];
    $query = "UPDATE catatan SET kegiatan='$kegiatan', tanggal='$tanggal' WHERE id=$id";
    mysqli_query($conn, $query);
    $_SESSION['message'] = "✏️ Kegiatan berhasil diperbarui!";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM catatan WHERE id=$id");
    $_SESSION['message'] = "🗑️ Kegiatan berhasil dihapus!";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fungsi untuk greeting berdasarkan waktu
function getTimeBasedGreeting() {
    $hour = date('H');
    if ($hour < 12) return 'Pagi';
    if ($hour < 15) return 'Siang';
    if ($hour < 19) return 'Sore';
    return 'Malam';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CatatanKu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="main-bg">

<!-- Background Animation -->
<div class="background-animation">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="shape shape-4"></div>
    <div class="shape shape-5"></div>
    <div class="shape shape-6"></div>
</div>

<!-- Particle Animation -->
<div class="particles-container" id="particles"></div>

<!-- Floating Action Button -->
<div class="fab-container">
    <button class="fab main-fab" id="fabToggle">
        <i class="fas fa-plus"></i>
    </button>
    <div class="fab-options">
        <button class="fab-option" id="addActivity">
            <div class="fab-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <span>Tambah Kegiatan</span>
        </button>
        <button class="fab-option" id="viewStats">
            <div class="fab-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <span>Statistik</span>
        </button>
        <button class="fab-option" id="quickNote">
            <div class="fab-icon">
                <i class="fas fa-sticky-note"></i>
            </div>
            <span>Catatan Cepat</span>
        </button>
        <button class="fab-option" id="openContactModal">
            <div class="fab-icon">
                <i class="fas fa-share-alt"></i>
            </div>
            <span>Social Media</span>
        </button>
    </div>
</div>

<!-- Modal untuk tambah kegiatan -->
<div class="modal" id="addModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <h2>Tambah Kegiatan Baru</h2>
            <button class="close-modal" id="closeModal">&times;</button>
        </div>
        <form method="POST" class="form-add">
            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <input type="text" name="kegiatan" placeholder="Apa yang ingin Anda lakukan?" required class="input">
            </div>
            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <input type="date" name="tanggal" required class="input" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <button type="submit" name="tambah" class="btn btn-primary btn-glow">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </button>
        </form>
    </div>
</div>

<!-- Modal untuk edit kegiatan -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-edit"></i>
            </div>
            <h2>Edit Kegiatan</h2>
            <button class="close-modal" id="closeEditModal">&times;</button>
        </div>
        <form method="POST" class="form-add">
            <input type="hidden" name="id" id="editId">
            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <input type="text" name="kegiatan" id="editKegiatan" placeholder="Deskripsi kegiatan" required class="input">
            </div>
            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <input type="date" name="tanggal" id="editTanggal" required class="input">
            </div>
            <button type="submit" name="edit" class="btn btn-primary btn-glow">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2>Konfirmasi Hapus</h2>
            <button class="close-modal" id="closeDeleteModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="delete-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3>Apakah Anda yakin?</h3>
            <p>Kegiatan "<span id="deleteKegiatanName"></span>" akan dihapus secara permanen.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelDelete">Batal</button>
            <a href="#" class="btn btn-danger btn-glow" id="confirmDelete">
                <i class="fas fa-trash"></i> Ya, Hapus
            </a>
        </div>
    </div>
</div>

<!-- Modal konfirmasi keluar -->
<div class="modal" id="logoutModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h2>Konfirmasi Keluar</h2>
            <button class="close-modal" id="closeLogoutModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="logout-icon">
                <i class="fas fa-door-open"></i>
            </div>
            <h3>Keluar dari sistem?</h3>
            <p>Anda akan keluar dari akun Anda. Pastikan semua perubahan telah disimpan.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelLogout">Batal</button>
            <a href="logout.php" class="btn btn-warning btn-glow">
                <i class="fas fa-sign-out-alt"></i> Ya, Keluar
            </a>
        </div>
    </div>
</div>

<!-- Modal untuk statistik -->
<div class="modal" id="statsModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h2>Statistik Kegiatan</h2>
            <button class="close-modal" id="closeStatsModal">&times;</button>
        </div>
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Kegiatan</h3>
                    <p id="totalActivities">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon today">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <h3>Hari Ini</h3>
                    <p id="todayActivities">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon week">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <h3>Minggu Ini</h3>
                    <p id="weekActivities">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon month">
                    <i class="fas fa-calendar-month"></i>
                </div>
                <div class="stat-info">
                    <h3>Bulan Ini</h3>
                    <p id="monthActivities">0</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk informasi kontak -->
<div class="modal" id="contactModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-share-alt"></i>
            </div>
            <h2>Hubungi Kami</h2>
            <button class="close-modal" id="closeContactModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="contact-info">
                <h3>Terhubung dengan Kami</h3>
                <p>Ikuti kami di media sosial untuk informasi terbaru</p>
                
                <div class="social-links">
                    <a href="https://wa.me/6281234567890" target="_blank" class="social-link whatsapp">
                        <div class="social-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <span>WhatsApp</span>
                    </a>
                    <a href="https://instagram.com/catatanku_app" target="_blank" class="social-link instagram">
                        <div class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <span>Instagram</span>
                    </a>
                    <a href="https://tiktok.com/@catatanku_app" target="_blank" class="social-link tiktok">
                        <div class="social-icon">
                            <i class="fab fa-tiktok"></i>
                        </div>
                        <span>TikTok</span>
                    </a>
                </div>
                
                <div class="contact-details">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>support@catatanku.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+62 812-3456-7890</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Navigation Bar -->
<nav class="top-nav">
    <div class="nav-container">
        <div class="nav-brand">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <span class="logo-text">CatatanKu</span>
            </div>
        </div>
        
        <div class="nav-center">
            <div class="date-display">
                <i class="fas fa-calendar"></i>
                <span id="currentDate"><?php echo date('d F Y'); ?></span>
            </div>
        </div>
        
        <div class="nav-user">
            <div class="user-welcome">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <span>Halo, <strong><?php echo $_SESSION['username']; ?></strong></span>
            </div>
            <div class="user-actions">
                <button class="nav-btn" id="contactBtn" title="Hubungi Kami">
                    <i class="fas fa-share-alt"></i>
                </button>
                <button class="nav-btn" id="userMenuToggle">
                    <i class="fas fa-cog"></i>
                </button>
                <div class="user-menu" id="userMenu">
                    <a href="#" class="menu-item" id="openProfile">
                        <i class="fas fa-user"></i> Profil
                    </a>
                    <a href="#" class="menu-item" id="openSettings">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                    <a href="#" class="menu-item" id="openContactModal">
                        <i class="fas fa-share-alt"></i> Hubungi Kami
                    </a>
                    <div class="menu-divider"></div>
                    <a href="#" class="menu-item logout-item" id="openLogoutModal">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-container">
    <!-- Welcome Section -->
    <section class="welcome-section">
        <div class="welcome-content">
            <div class="welcome-text">
                <h1 class="welcome-title">
                    <span class="greeting">Selamat <?php echo getTimeBasedGreeting(); ?>,</span>
                    <span class="username"><?php echo $_SESSION['username']; ?>! 👋</span>
                </h1>
                <p class="welcome-subtitle">Kelola kegiatan harian Anda dengan mudah dan efisien</p>
                <div class="welcome-stats">
                    <div class="stat-badge">
                        <i class="fas fa-check-circle"></i>
                        <span>Terorganisir</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-bolt"></i>
                        <span>Produktif</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-chart-line"></i>
                        <span>Terpantau</span>
                    </div>
                </div>
            </div>
            <div class="welcome-illustration">
                <div class="illustration-container">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="quick-stats">
        <div class="stat-card quick">
            <div class="stat-glow"></div>
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3>Total Kegiatan</h3>
                <div class="stat-value" id="quickTotal">0</div>
            </div>
        </div>
        <div class="stat-card quick">
            <div class="stat-glow"></div>
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h3>Selesai Hari Ini</h3>
                <div class="stat-value" id="quickCompleted">0</div>
            </div>
        </div>
        <div class="stat-card quick">
            <div class="stat-glow"></div>
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>Menunggu</h3>
                <div class="stat-value" id="quickPending">0</div>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="content-grid">
        <!-- Activities Section -->
        <section class="activities-section">
            <div class="section-header">
                <div class="section-title">
                    <h2><i class="fas fa-list-check"></i> Daftar Kegiatan</h2>
                    <p>Kelola semua kegiatan Anda di satu tempat</p>
                </div>
                <div class="section-actions">
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">
                            <i class="fas fa-layer-group"></i> Semua
                        </button>
                        <button class="filter-btn" data-filter="today">
                            <i class="fas fa-sun"></i> Hari Ini
                        </button>
                        <button class="filter-btn" data-filter="week">
                            <i class="fas fa-calendar-week"></i> Minggu Ini
                        </button>
                        <button class="filter-btn" data-filter="month">
                            <i class="fas fa-calendar-month"></i> Bulan Ini
                        </button>
                    </div>
                </div>
            </div>

            <?php if(isset($_SESSION['message'])): ?>
            <div class="notification success">
                <i class="fas fa-check-circle"></i>
                <span><?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                ?></span>
            </div>
            <?php endif; ?>

            <div class="activities-container" id="activitiesContainer">
                <?php
                $result = mysqli_query($conn, "SELECT * FROM catatan ORDER BY tanggal DESC, id DESC");
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    $date = date('d M Y', strtotime($row['tanggal']));
                    $isToday = date('Y-m-d') == $row['tanggal'] ? 'today' : '';
                    
                    echo "
                    <div class='activity-card {$isToday}' data-date='{$row['tanggal']}' data-id='{$row['id']}'>
                        <div class='activity-glow'></div>
                        <div class='activity-badge'>
                            <div class='activity-number'>{$no}</div>
                            <div class='activity-status'></div>
                        </div>
                        <div class='activity-content'>
                            <h3 class='activity-title'>{$row['kegiatan']}</h3>
                            <div class='activity-meta'>
                                <div class='activity-date'>
                                    <i class='fas fa-calendar'></i> {$date}
                                </div>
                                <div class='activity-actions'>
                                    <button class='action-btn edit-btn' data-id='{$row['id']}' data-kegiatan='{$row['kegiatan']}' data-tanggal='{$row['tanggal']}'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <button class='action-btn delete-btn' data-id='{$row['id']}' data-kegiatan='{$row['kegiatan']}'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>";
                    $no++;
                }
                ?>
            </div>
            
            <?php if(mysqli_num_rows($result) == 0): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>Belum ada kegiatan</h3>
                <p>Mulai tambahkan kegiatan pertama Anda dan tingkatkan produktivitas</p>
                <button class="btn btn-primary btn-glow" id="addFirstActivity">
                    <i class="fas fa-plus"></i> Tambah Kegiatan Pertama
                </button>
            </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<!-- Footer dengan Social Media -->
<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section">
            <div class="footer-logo">
                <i class="fas fa-tasks"></i>
                <span>CatatanKu</span>
            </div>
            <p>Aplikasi manajemen kegiatan harian yang membantu Anda tetap terorganisir dan produktif.</p>
        </div>
        
        <div class="footer-section">
            <h3>Terhubung dengan Kami</h3>
            <div class="social-media">
                <a href="https://wa.me/6281234567890" target="_blank" class="social-icon whatsapp" title="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://instagram.com/catatanku_app" target="_blank" class="social-icon instagram" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://tiktok.com/@catatanku_app" target="_blank" class="social-icon tiktok" title="TikTok">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>
        
        <div class="footer-section">
            <h3>Kontak</h3>
            <div class="contact-info">
                <p><i class="fas fa-envelope"></i> support@catatanku.com</p>
                <p><i class="fas fa-phone"></i> +62 812-3456-7890</p>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2024 CatatanKu. All rights reserved.</p>
    </div>
</footer>

<script>
// Particle Animation
function createParticles() {
    const container = document.getElementById('particles');
    const particleCount = 30;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Random properties
        const size = Math.random() * 4 + 1;
        const posX = Math.random() * 100;
        const posY = Math.random() * 100;
        const duration = Math.random() * 20 + 10;
        const delay = Math.random() * 5;
        
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${posX}%`;
        particle.style.top = `${posY}%`;
        particle.style.animationDuration = `${duration}s`;
        particle.style.animationDelay = `${delay}s`;
        
        container.appendChild(particle);
    }
}

// Pastikan DOM sudah fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded - initializing event listeners');
    
    // Initialize particles
    createParticles();
    
    // FAB Toggle
    const fabToggle = document.getElementById('fabToggle');
    const fabOptions = document.querySelector('.fab-options');
    
    if (fabToggle) {
        fabToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('FAB clicked');
            fabOptions.classList.toggle('show');
        });
    }
    
    // User Menu Toggle
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userMenu = document.getElementById('userMenu');
    
    if (userMenuToggle && userMenu) {
        userMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('User menu clicked');
            userMenu.classList.toggle('show');
        });
        
        // Close user menu when clicking outside
        document.addEventListener('click', function() {
            userMenu.classList.remove('show');
        });
    }
    
    // Modal Elements
    const modals = {
        add: document.getElementById('addModal'),
        edit: document.getElementById('editModal'),
        delete: document.getElementById('deleteModal'),
        logout: document.getElementById('logoutModal'),
        stats: document.getElementById('statsModal'),
        contact: document.getElementById('contactModal')
    };
    
    // Modal Open/Close Functions
    function openModal(modal) {
        console.log('Opening modal:', modal.id);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                const modalContent = modal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.classList.add('show');
                }
            }, 10);
        }
    }
    
    function closeModalFunc(modal) {
        console.log('Closing modal:', modal.id);
        if (modal) {
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.remove('show');
            }
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }
    
    // Event Listeners untuk membuka modal
    const addActivityBtn = document.getElementById('addActivity');
    if (addActivityBtn) {
        addActivityBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Add activity clicked');
            openModal(modals.add);
            if (fabOptions) fabOptions.classList.remove('show');
        });
    }
    
    const viewStatsBtn = document.getElementById('viewStats');
    if (viewStatsBtn) {
        viewStatsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('View stats clicked');
            updateStats();
            openModal(modals.stats);
            if (fabOptions) fabOptions.classList.remove('show');
        });
    }
    
    const addFirstActivityBtn = document.getElementById('addFirstActivity');
    if (addFirstActivityBtn) {
        addFirstActivityBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Add first activity clicked');
            openModal(modals.add);
        });
    }
    
    const openLogoutModalBtn = document.getElementById('openLogoutModal');
    if (openLogoutModalBtn) {
        openLogoutModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Open logout modal clicked');
            openModal(modals.logout);
        });
    }
    
    const openContactModalBtn = document.getElementById('openContactModal');
    if (openContactModalBtn) {
        openContactModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Open contact modal clicked');
            openModal(modals.contact);
        });
    }
    
    const contactBtn = document.getElementById('contactBtn');
    if (contactBtn) {
        contactBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Contact button clicked');
            openModal(modals.contact);
        });
    }
    
    // Event Listeners untuk menutup modal
    const closeModalBtn = document.getElementById('closeModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalFunc(modals.add);
        });
    }
    
    const closeEditModalBtn = document.getElementById('closeEditModal');
    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalFunc(modals.edit);
        });
    }
    
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    if (closeDeleteModalBtn) {
        closeDeleteModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalFunc(modals.delete);
        });
    }
    
    const closeLogoutModalBtn = document.getElementById('closeLogoutModal');
    if (closeLogoutModalBtn) {
        closeLogoutModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalFunc(modals.logout);
        });
    }
    
    const closeStatsModalBtn = document.getElementById('closeStatsModal');
    if (closeStatsModalBtn) {
        closeStatsModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalFunc(modals.stats);
        });
    }
    
    const closeContactModalBtn = document.getElementById('closeContactModal');
    if (closeContactModalBtn) {
        closeContactModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalFunc(modals.contact);
        });
    }
    
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalFunc(modals.delete);
        });
    }
    
    const cancelLogoutBtn = document.getElementById('cancelLogout');
    if (cancelLogoutBtn) {
        cancelLogoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModalFunc(modals.logout);
        });
    }
    
    // Close modal when clicking outside
    Object.values(modals).forEach(modal => {
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModalFunc(modal);
                }
            });
        }
    });
    
    // Edit functionality
    const editButtons = document.querySelectorAll('.edit-btn');
    console.log('Found edit buttons:', editButtons.length);
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const kegiatan = this.getAttribute('data-kegiatan');
            const tanggal = this.getAttribute('data-tanggal');
            
            console.log('Edit clicked:', {id, kegiatan, tanggal});
            
            document.getElementById('editId').value = id;
            document.getElementById('editKegiatan').value = kegiatan;
            document.getElementById('editTanggal').value = tanggal;
            
            openModal(modals.edit);
        });
    });
    
    // Delete functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    console.log('Found delete buttons:', deleteButtons.length);
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const kegiatan = this.getAttribute('data-kegiatan');
            
            console.log('Delete clicked:', {id, kegiatan});
            
            document.getElementById('deleteKegiatanName').textContent = kegiatan;
            document.getElementById('confirmDelete').href = `?hapus=${id}`;
            
            openModal(modals.delete);
        });
    });
    
    // Filter activities
    const filterBtns = document.querySelectorAll('.filter-btn');
    console.log('Found filter buttons:', filterBtns.length);
    const activityCards = document.querySelectorAll('.activity-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            console.log('Filter clicked:', filter);
            
            const today = new Date();
            
            activityCards.forEach(card => {
                const cardDate = new Date(card.getAttribute('data-date'));
                const showCard = filterActivity(cardDate, filter, today);
                card.style.display = showCard ? 'flex' : 'none';
            });
        });
    });
    
    function filterActivity(cardDate, filter, today) {
        if (filter === 'all') return true;
        
        if (filter === 'today') {
            return cardDate.toDateString() === today.toDateString();
        }
        
        if (filter === 'week') {
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            startOfWeek.setHours(0, 0, 0, 0);
            return cardDate >= startOfWeek;
        }
        
        if (filter === 'month') {
            return cardDate.getMonth() === today.getMonth() && 
                   cardDate.getFullYear() === today.getFullYear();
        }
        
        return true;
    }
    
    // Update stats
    function updateStats() {
        const today = new Date();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay());
        startOfWeek.setHours(0, 0, 0, 0);
        
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        
        const totalActivities = document.querySelectorAll('.activity-card').length;
        
        let todayActivities = 0;
        let weekActivities = 0;
        let monthActivities = 0;
        
        document.querySelectorAll('.activity-card').forEach(card => {
            const cardDate = new Date(card.getAttribute('data-date'));
            cardDate.setHours(0, 0, 0, 0);
            
            if (cardDate.getTime() === today.getTime()) {
                todayActivities++;
            }
            
            if (cardDate >= startOfWeek) {
                weekActivities++;
            }
            
            if (cardDate >= startOfMonth) {
                monthActivities++;
            }
        });
        
        // Update modal stats
        const totalActivitiesEl = document.getElementById('totalActivities');
        const todayActivitiesEl = document.getElementById('todayActivities');
        const weekActivitiesEl = document.getElementById('weekActivities');
        const monthActivitiesEl = document.getElementById('monthActivities');
        
        if (totalActivitiesEl) totalActivitiesEl.textContent = totalActivities;
        if (todayActivitiesEl) todayActivitiesEl.textContent = todayActivities;
        if (weekActivitiesEl) weekActivitiesEl.textContent = weekActivities;
        if (monthActivitiesEl) monthActivitiesEl.textContent = monthActivities;
        
        // Update quick stats
        const quickTotal = document.getElementById('quickTotal');
        const quickCompleted = document.getElementById('quickCompleted');
        const quickPending = document.getElementById('quickPending');
        
        if (quickTotal) quickTotal.textContent = totalActivities;
        if (quickCompleted) quickCompleted.textContent = todayActivities;
        if (quickPending) quickPending.textContent = totalActivities - todayActivities;
    }
    
    // Initialize stats
    updateStats();
    
    // Auto-hide notifications
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 500);
        }, 4000);
    });
    
    // Background animation
    const shapes = document.querySelectorAll('.shape');
    shapes.forEach((shape, index) => {
        const randomX = Math.random() * 100;
        const randomY = Math.random() * 100;
        const randomDelay = Math.random() * 5;
        
        shape.style.left = `${randomX}%`;
        shape.style.top = `${randomY}%`;
        shape.style.animationDelay = `${randomDelay}s`;
    });
    
    // Close FAB options when clicking outside
    document.addEventListener('click', function(e) {
        if (fabOptions && fabOptions.classList.contains('show') && 
            !fabToggle.contains(e.target) && 
            !fabOptions.contains(e.target)) {
            fabOptions.classList.remove('show');
        }
    });
    
    console.log('All event listeners initialized successfully');
});
</script>

<style>
/* ====== VARIABLES ====== */
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --primary-light: #818cf8;
    --secondary: #10b981;
    --secondary-dark: #059669;
    --accent: #f59e0b;
    --accent-dark: #d97706;
    --danger: #ef4444;
    --danger-dark: #dc2626;
    --dark: #0f172a;
    --darker: #020617;
    --light: #f8fafc;
    --gray: #64748b;
    --gray-light: #cbd5e1;
    --card-bg: rgba(255, 255, 255, 0.1);
    --card-border: rgba(255, 255, 255, 0.15);
    --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    --glow: 0 0 20px rgba(99, 102, 241, 0.3);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --whatsapp: #25D366;
    --instagram: #E4405F;
    --tiktok: #000000;
}

/* ====== RESET & BASE ====== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, var(--darker), var(--dark));
    color: var(--light);
    min-height: 100vh;
    line-height: 1.6;
    overflow-x: hidden;
}

.main-bg {
    background-attachment: fixed;
}

/* ====== BACKGROUND ANIMATION ====== */
.background-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -2;
    overflow: hidden;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: linear-gradient(45deg, var(--primary), transparent);
    opacity: 0.1;
    animation: float 25s infinite linear;
    filter: blur(40px);
}

.shape-1 {
    width: 400px;
    height: 400px;
    top: 10%;
    left: 5%;
    background: linear-gradient(45deg, var(--primary), transparent);
}

.shape-2 {
    width: 300px;
    height: 300px;
    top: 60%;
    right: 5%;
    background: linear-gradient(45deg, var(--secondary), transparent);
}

.shape-3 {
    width: 250px;
    height: 250px;
    bottom: 10%;
    left: 15%;
    background: linear-gradient(45deg, var(--accent), transparent);
}

.shape-4 {
    width: 350px;
    height: 350px;
    top: 20%;
    right: 15%;
    background: linear-gradient(45deg, var(--danger), transparent);
}

.shape-5 {
    width: 200px;
    height: 200px;
    top: 80%;
    left: 80%;
    background: linear-gradient(45deg, var(--primary-light), transparent);
}

.shape-6 {
    width: 280px;
    height: 280px;
    top: 40%;
    left: 70%;
    background: linear-gradient(45deg, var(--secondary-dark), transparent);
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg) scale(1);
    }
    25% {
        transform: translateY(-30px) rotate(90deg) scale(1.1);
    }
    50% {
        transform: translateY(0) rotate(180deg) scale(1);
    }
    75% {
        transform: translateY(30px) rotate(270deg) scale(0.9);
    }
}

/* ====== PARTICLE ANIMATION ====== */
.particles-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    pointer-events: none;
}

.particle {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: particle-float 20s infinite linear;
}

@keyframes particle-float {
    0%, 100% {
        transform: translateY(0) translateX(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100vh) translateX(100px);
        opacity: 0;
    }
}

/* ====== TOP NAVIGATION ====== */
.top-nav {
    position: sticky;
    top: 0;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--card-border);
    z-index: 1000;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-brand .logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--light);
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
    box-shadow: var(--glow);
}

.logo-text {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 900;
}

.nav-center .date-display {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-light);
    font-size: 0.9rem;
    background: rgba(255, 255, 255, 0.05);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--card-border);
}

.nav-user {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-welcome {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--gray-light);
    background: rgba(255, 255, 255, 0.05);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--card-border);
}

.user-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
}

.user-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
}

.nav-btn {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--card-border);
    color: var(--gray-light);
    font-size: 1rem;
    cursor: pointer;
    border-radius: 10px;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--light);
    transform: translateY(-2px);
    box-shadow: var(--glow);
}

.user-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--darker);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 0.5rem;
    min-width: 220px;
    box-shadow: var(--shadow);
    backdrop-filter: blur(20px);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: var(--transition);
    z-index: 1001;
}

.user-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--gray-light);
    text-decoration: none;
    border-radius: 10px;
    transition: var(--transition);
    font-weight: 500;
}

.menu-item:hover {
    background: rgba(255, 255, 255, 0.05);
    color: var(--light);
    transform: translateX(5px);
}

.menu-divider {
    height: 1px;
    background: var(--card-border);
    margin: 0.5rem 0;
}

.logout-item {
    color: var(--danger);
}

.logout-item:hover {
    background: rgba(239, 68, 68, 0.1);
}

/* ====== MAIN CONTAINER ====== */
.main-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

/* ====== WELCOME SECTION ====== */
.welcome-section {
    margin-bottom: 3rem;
}

.welcome-content {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 3rem;
    align-items: center;
    padding: 3rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 24px;
    border: 1px solid var(--card-border);
    backdrop-filter: blur(20px);
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
}

.welcome-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
}

.welcome-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.greeting {
    color: var(--gray-light);
    font-size: 1.8rem;
    font-weight: 600;
}

.username {
    background: linear-gradient(135deg, var(--primary), var(--secondary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 900;
}

.welcome-subtitle {
    color: var(--gray-light);
    font-size: 1.2rem;
    margin-bottom: 2rem;
    font-weight: 400;
}

.welcome-stats {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.stat-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.08);
    padding: 0.75rem 1.25rem;
    border-radius: 50px;
    border: 1px solid var(--card-border);
    color: var(--gray-light);
    font-size: 0.9rem;
    font-weight: 500;
    transition: var(--transition);
}

.stat-badge:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-2px);
}

.stat-badge i {
    color: var(--secondary);
}

.welcome-illustration {
    display: flex;
    align-items: center;
    justify-content: center;
}

.illustration-container {
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: white;
    box-shadow: var(--glow);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 30px rgba(99, 102, 241, 0.6);
    }
}

/* ====== QUICK STATS ====== */
.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-card.quick {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 20px;
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: var(--transition);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.stat-card.quick:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow);
    border-color: var(--primary);
}

.stat-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), transparent);
    opacity: 0;
    transition: var(--transition);
}

.stat-card.quick:hover .stat-glow {
    opacity: 1;
}

.stat-card.quick .stat-icon {
    width: 70px;
    height: 70px;
    border-radius: 16px;
    background: rgba(99, 102, 241, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--primary);
    transition: var(--transition);
}

.stat-card.quick:hover .stat-icon {
    background: var(--primary);
    color: white;
    transform: scale(1.1);
}

.stat-content h3 {
    font-size: 0.95rem;
    color: var(--gray-light);
    margin-bottom: 0.75rem;
    font-weight: 500;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--light);
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ====== CONTENT GRID ====== */
.content-grid {
    display: grid;
    gap: 2rem;
}

/* ====== ACTIVITIES SECTION ====== */
.activities-section {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 24px;
    padding: 2.5rem;
    backdrop-filter: blur(20px);
    box-shadow: var(--shadow);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2.5rem;
    gap: 2rem;
}

.section-title h2 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.section-title p {
    color: var(--gray-light);
    font-size: 1rem;
}

.section-actions {
    flex-shrink: 0;
}

.filter-options {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--card-border);
    color: var(--gray-light);
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.9rem;
    font-weight: 500;
}

.filter-btn.active,
.filter-btn:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--glow);
}

/* ====== NOTIFICATION ====== */
.notification {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
    animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
}

.notification.success i {
    color: var(--secondary);
    font-size: 1.2rem;
}

.notification span {
    color: var(--light);
    font-weight: 600;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* ====== ACTIVITIES CONTAINER ====== */
.activities-container {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.activity-card {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--card-border);
    border-radius: 20px;
    padding: 2rem;
    transition: var(--transition);
    animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.activity-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: var(--shadow);
}

.activity-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), transparent);
    opacity: 0;
    transition: var(--transition);
}

.activity-card:hover .activity-glow {
    opacity: 1;
}

.activity-card.today {
    border-left: 6px solid var(--secondary);
}

.activity-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.activity-number {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: white;
    font-size: 1rem;
    box-shadow: var(--glow);
}

.activity-status {
    width: 10px;
    height: 10px;
    background: var(--secondary);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--light);
    line-height: 1.4;
}

.activity-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
}

.activity-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-light);
    font-size: 0.9rem;
    font-weight: 500;
}

.activity-date i {
    color: var(--primary);
}

.activity-actions {
    display: flex;
    gap: 0.75rem;
}

.action-btn {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    font-size: 1rem;
}

.edit-btn {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.edit-btn:hover {
    background: #3b82f6;
    color: white;
    transform: scale(1.1) translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

.delete-btn {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.delete-btn:hover {
    background: var(--danger);
    color: white;
    transform: scale(1.1) translateY(-2px);
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
}

/* ====== EMPTY STATE ====== */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    color: var(--gray-light);
}

.empty-icon {
    font-size: 5rem;
    color: var(--gray);
    margin-bottom: 2rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: var(--light);
    font-weight: 600;
}

.empty-state p {
    margin-bottom: 2.5rem;
    font-size: 1.1rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* ====== FLOATING ACTION BUTTON ====== */
.fab-container {
    position: fixed;
    bottom: 2.5rem;
    right: 2.5rem;
    z-index: 1000;
}

.main-fab {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1001;
}

.main-fab:hover {
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 15px 40px rgba(99, 102, 241, 0.7);
}

.fab-options {
    position: absolute;
    bottom: 85px;
    right: 0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    opacity: 0;
    visibility: hidden;
    transform: translateY(30px);
    transition: var(--transition);
}

.fab-options.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.fab-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--darker);
    color: var(--light);
    border: 1px solid var(--card-border);
    border-radius: 50px;
    padding: 1rem 1.5rem;
    white-space: nowrap;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: var(--shadow);
    font-size: 0.95rem;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

.fab-option:hover {
    background: var(--primary);
    transform: translateX(-10px);
    box-shadow: var(--glow);
}

.fab-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

/* ====== MODAL STYLES ====== */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-content {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 24px;
    width: 100%;
    max-width: 500px;
    border: 1px solid var(--card-border);
    box-shadow: var(--shadow);
    transform: scale(0.9) translateY(20px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 90vh;
    overflow-y: auto;
    backdrop-filter: blur(20px);
}

.modal-content.show {
    transform: scale(1) translateY(0);
    opacity: 1;
}

.modal-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 2rem 2rem 1.5rem;
    border-bottom: 1px solid var(--card-border);
    position: relative;
}

.modal-header::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
}

.modal-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: var(--glow);
}

.modal-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--light);
    flex: 1;
}

.close-modal {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--card-border);
    color: var(--gray-light);
    font-size: 1.5rem;
    cursor: pointer;
    transition: var(--transition);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}

.close-modal:hover {
    color: var(--light);
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(90deg);
}

.modal .form-add {
    padding: 2rem;
}

/* ====== FORM STYLES ====== */
.form-add {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: var(--gray);
    z-index: 1;
    font-size: 1.1rem;
}

.input {
    width: 100%;
    padding: 1.25rem 1.25rem 1.25rem 3.5rem;
    border-radius: 16px;
    border: 1px solid var(--card-border);
    background: rgba(255, 255, 255, 0.08);
    color: var(--light);
    font-size: 1rem;
    transition: var(--transition);
    font-family: 'Poppins', sans-serif;
}

.input:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.12);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    transform: translateY(-2px);
}

.input::placeholder {
    color: var(--gray);
}

/* ====== BUTTONS ====== */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 2rem;
    border-radius: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    border: none;
    font-size: 1rem;
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.5);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: var(--light);
    border: 1px solid var(--card-border);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
}

.btn-danger {
    background: linear-gradient(135deg, var(--danger), var(--danger-dark));
    color: white;
}

.btn-danger:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(239, 68, 68, 0.5);
}

.btn-warning {
    background: linear-gradient(135deg, var(--accent), var(--accent-dark));
    color: white;
}

.btn-warning:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(245, 158, 11, 0.5);
}

.btn-glow {
    box-shadow: var(--glow);
}

/* ====== STATS MODAL ====== */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--card-bg);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid var(--card-border);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary);
}

.stat-icon {
    width: 70px;
    height: 70px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    box-shadow: var(--glow);
}

.stat-icon.total { background: linear-gradient(135deg, var(--primary), var(--primary-light)); }
.stat-icon.today { background: linear-gradient(135deg, var(--secondary), var(--secondary-dark)); }
.stat-icon.week { background: linear-gradient(135deg, var(--accent), var(--accent-dark)); }
.stat-icon.month { background: linear-gradient(135deg, var(--danger), var(--danger-dark)); }

.stat-info h3 {
    font-size: 0.9rem;
    color: var(--gray-light);
    margin-bottom: 0.75rem;
    font-weight: 500;
}

.stat-info p {
    font-size: 2rem;
    font-weight: 800;
    color: var(--light);
}

/* ====== CONTACT MODAL ====== */
.contact-info {
    text-align: center;
}

.contact-info h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--light);
    font-weight: 600;
}

.contact-info > p {
    color: var(--gray-light);
    margin-bottom: 2rem;
}

.social-links {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2.5rem;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-radius: 16px;
    text-decoration: none;
    color: var(--light);
    transition: var(--transition);
    border: 1px solid var(--card-border);
    background: rgba(255, 255, 255, 0.05);
}

.social-link:hover {
    transform: translateX(10px);
    border-color: transparent;
}

.social-link.whatsapp:hover {
    background: var(--whatsapp);
    box-shadow: 0 10px 25px rgba(37, 211, 102, 0.4);
}

.social-link.instagram:hover {
    background: var(--instagram);
    box-shadow: 0 10px 25px rgba(228, 64, 95, 0.4);
}

.social-link.tiktok:hover {
    background: var(--tiktok);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
}

.social-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.contact-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    border: 1px solid var(--card-border);
    color: var(--gray-light);
    transition: var(--transition);
}

.contact-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateX(5px);
}

.contact-item i {
    color: var(--primary);
    font-size: 1.1rem;
}

/* ====== MODAL BODY & FOOTER ====== */
.modal-body {
    padding: 2rem;
}

.modal-body h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--light);
    font-weight: 600;
}

.modal-body p {
    color: var(--gray-light);
    line-height: 1.6;
    font-size: 1rem;
}

.delete-icon,
.logout-icon {
    font-size: 5rem;
    color: var(--danger);
    margin-bottom: 2rem;
    opacity: 0.8;
}

.logout-icon {
    color: var(--accent);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1.5rem 2rem;
    border-top: 1px solid var(--card-border);
}

/* ====== FOOTER ====== */
.main-footer {
    background: rgba(255, 255, 255, 0.05);
    border-top: 1px solid var(--card-border);
    margin-top: 4rem;
    backdrop-filter: blur(20px);
}

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 3rem 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 3rem;
}

.footer-section h3 {
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    color: var(--light);
    font-weight: 600;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    color: var(--light);
}

.footer-logo i {
    color: var(--primary);
}

.footer-section p {
    color: var(--gray-light);
    line-height: 1.6;
}

.social-media {
    display: flex;
    gap: 1rem;
}

.social-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: var(--gray-light);
    transition: var(--transition);
    text-decoration: none;
}

.social-icon:hover {
    transform: translateY(-5px);
    color: white;
}

.social-icon.whatsapp:hover {
    background: var(--whatsapp);
    box-shadow: 0 10px 20px rgba(37, 211, 102, 0.4);
}

.social-icon.instagram:hover {
    background: var(--instagram);
    box-shadow: 0 10px 20px rgba(228, 64, 95, 0.4);
}

.social-icon.tiktok:hover {
    background: var(--tiktok);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
}

.contact-info p {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    color: var(--gray-light);
}

.contact-info i {
    color: var(--primary);
    width: 20px;
}

.footer-bottom {
    border-top: 1px solid var(--card-border);
    padding: 1.5rem 2rem;
    text-align: center;
    color: var(--gray-light);
}

/* ====== ANIMATIONS ====== */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ====== RESPONSIVE DESIGN ====== */
@media (max-width: 1024px) {
    .main-container {
        padding: 1.5rem;
    }
    
    .welcome-title {
        font-size: 2.5rem;
    }
    
    .quick-stats {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}

@media (max-width: 768px) {
    .main-container {
        padding: 1rem;
    }
    
    .nav-container {
        padding: 1rem;
    }
    
    .nav-center {
        display: none;
    }
    
    .welcome-content {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 2rem;
        gap: 2rem;
    }
    
    .welcome-title {
        font-size: 2rem;
    }
    
    .illustration-container {
        width: 120px;
        height: 120px;
        font-size: 3rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1.5rem;
    }
    
    .filter-options {
        justify-content: center;
    }
    
    .activity-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;
    }
    
    .activity-actions {
        align-self: flex-end;
    }
    
    .modal-content {
        margin: 1rem;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .fab-container {
        bottom: 1.5rem;
        right: 1.5rem;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
    }
    
    .social-media {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .welcome-title {
        font-size: 1.8rem;
    }
    
    .greeting {
        font-size: 1.4rem;
    }
    
    .welcome-stats {
        justify-content: center;
    }
    
    .quick-stats {
        grid-template-columns: 1fr;
    }
    
    .activities-section {
        padding: 1.5rem;
    }
    
    .activity-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 1.5rem;
    }
    
    .activity-badge {
        flex-direction: row;
        width: 100%;
        justify-content: space-between;
    }
    
    .stat-value {
        font-size: 2rem;
    }
    
    .main-fab {
        width: 60px;
        height: 60px;
        font-size: 1.3rem;
    }
    
    .fab-option {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
}
</style>
</body>
</html>