<?php
// FILE: website/admin.php
session_start();

// Security Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$dataFile = 'data.json';
$uploadDir = 'uploads/';

// Initialize or load data safely
$data = [];
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true) ?? [];
}

// Structure enforcement
if (!isset($data['gallery'])) $data['gallery'] = [];
if (!isset($data['messages'])) $data['messages'] = [];
if (!isset($data['members'])) $data['members'] = ['cowo' => [], 'cewe' => []];

// Data Migration for Gallery Albums
foreach ($data['gallery'] as &$item) {
    if (!isset($item['photos'])) {
        $item['photos'] = [];
        if (!empty($item['path'])) {
            $item['photos'][] = $item['path'];
        }
        unset($item['path']);
    }
}
unset($item);

$default_schedule = [
    '7:30 - 8:20'   => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>''],
    '8:20 - 9:10'   => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>''],
    '9:10 - 10:00'  => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>''],
    '10:20 - 11:10' => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>''],
    '11:10 - 12:00' => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>''],
    '13:00 - 13:50' => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>''],
    '13:50 - 14:40' => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>''],
    '14:40 - 15:30' => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>''],
    '16:00 - 16:50' => ['Senin'=>'', 'Selasa'=>'', 'Rabu'=>'', 'Kamis'=>'', 'Jumat'=>'']
];
if (!isset($data['schedule']) || empty($data['schedule'])) {
    $data['schedule'] = $default_schedule;
}

// Helper function to convert uploaded image to webp
function saveAsWebp($sourcePath, $destinationPath, $isBase64 = false) {
    if ($isBase64) {
        $image_data = base64_decode($sourcePath);
        $img = @imagecreatefromstring($image_data);
    } else {
        $info = @getimagesize($sourcePath);
        if (!$info) return false;
        $mime = $info['mime'];
        
        if ($mime == 'image/jpeg') {
            $img = @imagecreatefromjpeg($sourcePath);
        } elseif ($mime == 'image/png') {
            $img = @imagecreatefrompng($sourcePath);
        } elseif ($mime == 'image/webp') {
            $img = @imagecreatefromwebp($sourcePath);
        } elseif ($mime == 'image/gif') {
            $img = @imagecreatefromgif($sourcePath);
        } else {
            return false;
        }
    }

    if (!$img) return false;

    imagepalettetotruecolor($img);
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $destinationPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $destinationPath);

    $success = imagewebp($img, $destinationPath, 85);
    imagedestroy($img);
    return $success ? $destinationPath : false;
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // 1. GALLERY UPLOAD
    if ($action === 'upload_gallery' && isset($_FILES['gallery_images'])) {
        $uploadedPhotos = [];
        $totalFiles = count($_FILES['gallery_images']['name']);
        
        for ($i = 0; $i < $totalFiles; $i++) {
            if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['gallery_images']['tmp_name'][$i];
                $uniqueName = time() . '_' . $i . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['gallery_images']['name'][$i]));
                $targetFilePath = $uploadDir . $uniqueName;
                
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                if (in_array($fileType, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) {
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $webpPath = saveAsWebp($tmpName, $targetFilePath);
                    if ($webpPath) {
                        $uploadedPhotos[] = $webpPath;
                    }
                }
            }
        }
        
        if (!empty($uploadedPhotos)) {
            $data['gallery'][] = [
                'id' => time(),
                'photos' => $uploadedPhotos,
                'name' => htmlspecialchars($_POST['image_title'] ?? 'Untitled'),
                'date' => htmlspecialchars($_POST['image_date'] ?? date('Y-m-d')),
                'location' => htmlspecialchars($_POST['image_location'] ?? '-'),
                'description' => htmlspecialchars($_POST['image_desc'] ?? '')
            ];
            $_SESSION['admin_msg'] = count($uploadedPhotos) . " gambar berhasil ditambahkan sebagai album ke galeri.";
        } else {
            $_SESSION['admin_err'] = "Tidak ada gambar valid yang berhasil diupload.";
        }
    }

    // 2. DELETE GALLERY
    if ($action === 'delete_gallery' && isset($_POST['id'])) {
        foreach ($data['gallery'] as $k => $v) {
            if ($v['id'] == $_POST['id']) {
                if (isset($v['photos'])) {
                    foreach ($v['photos'] as $photoPath) {
                        if (file_exists($photoPath)) unlink($photoPath);
                    }
                }
                unset($data['gallery'][$k]);
                $_SESSION['admin_msg'] = "Album galeri dihapus.";
                break;
            }
        }
        $data['gallery'] = array_values($data['gallery']);
    }

    // 3. ADD MEMBER
    if ($action === 'add_member') {
        $gender = $_POST['gender'] ?? 'cowo';
        $name = htmlspecialchars($_POST['member_name'] ?? 'Anggota Baru');
        
        $imagePath = '';
        if (!empty($_POST['member_image_cropped'])) {
            $base64_string = $_POST['member_image_cropped'];
            $data_array = explode(',', $base64_string);
            if (count($data_array) == 2) {
                $uniqueName = 'member_' . time() . '_' . uniqid() . '.webp';
                $targetFilePath = $uploadDir . $uniqueName;
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $webpPath = saveAsWebp($data_array[1], $targetFilePath, true);
                if ($webpPath) {
                    $imagePath = $webpPath;
                }
            }
        } elseif (isset($_FILES['member_image']) && $_FILES['member_image']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['member_image']['tmp_name'];
            $uniqueName = 'member_' . time() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['member_image']['name']));
            $targetFilePath = $uploadDir . $uniqueName;
            
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            if (in_array($fileType, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) {
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $webpPath = saveAsWebp($tmpName, $targetFilePath);
                if ($webpPath) {
                    $imagePath = $webpPath;
                }
            }
        }

        $julukan = htmlspecialchars($_POST['member_julukan'] ?? 'Anggota');
        $nim = htmlspecialchars($_POST['member_nim'] ?? '');
        $asal = htmlspecialchars($_POST['member_asal'] ?? '');
        $hobi = htmlspecialchars($_POST['member_hobi'] ?? '');
        $linkedin = htmlspecialchars($_POST['member_linkedin'] ?? '');
        $instagram = htmlspecialchars($_POST['member_instagram'] ?? '');
        $whatsapp = htmlspecialchars($_POST['member_whatsapp'] ?? '');
        $motto = htmlspecialchars($_POST['member_motto'] ?? '');

        if (in_array($gender, ['cowo', 'cewe'])) {
            $data['members'][$gender][] = [
                'id' => time(),
                'name' => $name,
                'julukan' => $julukan,
                'nim' => $nim,
                'asal' => $asal,
                'hobi' => $hobi,
                'linkedin' => $linkedin,
                'instagram' => $instagram,
                'whatsapp' => $whatsapp,
                'motto' => $motto,
                'image' => $imagePath
            ];
            
            // Sort the array by NIM ascending after adding
            usort($data['members'][$gender], function($a, $b) {
                return (int)($a['nim'] ?? 0) <=> (int)($b['nim'] ?? 0);
            });
            
            $_SESSION['admin_msg'] = "Anggota $gender berhasil ditambahkan.";
        }
    }

    // 4. DELETE MEMBER
    if ($action === 'delete_member' && isset($_POST['id']) && isset($_POST['gender'])) {
        $g = $_POST['gender'];
        if (isset($data['members'][$g])) {
            foreach ($data['members'][$g] as $k => $v) {
                if ($v['id'] == $_POST['id']) {
                    if (!empty($v['image']) && file_exists($v['image'])) {
                        unlink($v['image']); // Clean up disk space
                    }
                    unset($data['members'][$g][$k]);
                    $_SESSION['admin_msg'] = "Anggota dihapus beserta fotonya.";
                    break;
                }
            }
            $data['members'][$g] = array_values($data['members'][$g]);
        }
    }

    // 5. EDIT GALLERY
    if ($action === 'edit_gallery' && isset($_POST['id'])) {
        foreach ($data['gallery'] as $k => &$v) {
            if ($v['id'] == $_POST['id']) {
                $v['name'] = htmlspecialchars($_POST['image_title'] ?? $v['name']);
                $v['date'] = htmlspecialchars($_POST['image_date'] ?? $v['date']);
                $v['location'] = htmlspecialchars($_POST['image_location'] ?? $v['location']);
                $v['description'] = htmlspecialchars($_POST['image_desc'] ?? $v['description']);
                
                if (isset($_FILES['gallery_images'])) {
                    $totalFiles = count($_FILES['gallery_images']['name']);
                    for ($i = 0; $i < $totalFiles; $i++) {
                        if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                            $tmpName = $_FILES['gallery_images']['tmp_name'][$i];
                            $uniqueName = time() . '_' . $i . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['gallery_images']['name'][$i]));
                            $targetFilePath = $uploadDir . $uniqueName;
                            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                            if (in_array($fileType, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) {
                                $webpPath = saveAsWebp($tmpName, $targetFilePath);
                                if ($webpPath) {
                                    $v['photos'][] = $webpPath;
                                }
                            }
                        }
                    }
                }
                
                if (isset($_POST['delete_photos']) && is_array($_POST['delete_photos'])) {
                    foreach ($_POST['delete_photos'] as $delPath) {
                        $idx = array_search($delPath, $v['photos']);
                        if ($idx !== false) {
                            if (file_exists($delPath)) unlink($delPath);
                            unset($v['photos'][$idx]);
                        }
                    }
                    $v['photos'] = array_values($v['photos']);
                }
                
                $_SESSION['admin_msg'] = "Data album diperbarui.";
                break;
            }
        }
        unset($v);
    }

    // 6. EDIT MEMBER
    if ($action === 'edit_member' && isset($_POST['id']) && isset($_POST['old_gender'])) {
        $old_gender = $_POST['old_gender'];
        $new_gender = $_POST['gender'] ?? $old_gender;
        $target_member = null;
        $target_index = -1;
        if (isset($data['members'][$old_gender])) {
            foreach ($data['members'][$old_gender] as $k => $v) {
                if ($v['id'] == $_POST['id']) {
                    $target_member = $v;
                    $target_index = $k;
                    break;
                }
            }
        }
        if ($target_member) {
            $target_member['name'] = htmlspecialchars($_POST['member_name'] ?? $target_member['name']);
            $target_member['julukan'] = htmlspecialchars($_POST['member_julukan'] ?? ($target_member['julukan'] ?? 'Anggota'));
            $target_member['nim'] = htmlspecialchars($_POST['member_nim'] ?? $target_member['nim']);
            $target_member['asal'] = htmlspecialchars($_POST['member_asal'] ?? $target_member['asal']);
            $target_member['hobi'] = htmlspecialchars($_POST['member_hobi'] ?? $target_member['hobi']);
            $target_member['linkedin'] = htmlspecialchars($_POST['member_linkedin'] ?? $target_member['linkedin']);
            $target_member['instagram'] = htmlspecialchars($_POST['member_instagram'] ?? $target_member['instagram']);
            $target_member['whatsapp'] = htmlspecialchars($_POST['member_whatsapp'] ?? $target_member['whatsapp']);
            $target_member['motto'] = htmlspecialchars($_POST['member_motto'] ?? $target_member['motto']);
            
            if (!empty($_POST['member_image_cropped_edit'])) {
                $base64_string = $_POST['member_image_cropped_edit'];
                $data_array = explode(',', $base64_string);
                if (count($data_array) == 2) {
                    $uniqueName = 'member_' . time() . '_' . uniqid() . '.webp';
                    $targetFilePath = $uploadDir . $uniqueName;
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $webpPath = saveAsWebp($data_array[1], $targetFilePath, true);
                    if ($webpPath) {
                        if (!empty($target_member['image']) && file_exists($target_member['image'])) unlink($target_member['image']);
                        $target_member['image'] = $webpPath;
                    }
                }
            } elseif (isset($_FILES['member_image']) && $_FILES['member_image']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['member_image']['tmp_name'];
                $uniqueName = 'member_' . time() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['member_image']['name']));
                $targetFilePath = $uploadDir . $uniqueName;
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                if (in_array($fileType, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) {
                    $webpPath = saveAsWebp($tmpName, $targetFilePath);
                    if ($webpPath) {
                        if (!empty($target_member['image']) && file_exists($target_member['image'])) unlink($target_member['image']);
                        $target_member['image'] = $webpPath;
                    }
                }
            }
            
            if ($old_gender !== $new_gender) {
                unset($data['members'][$old_gender][$target_index]);
                $data['members'][$old_gender] = array_values($data['members'][$old_gender]);
                $data['members'][$new_gender][] = $target_member;
                usort($data['members'][$new_gender], function($a, $b) { return (int)($a['nim'] ?? 0) <=> (int)($b['nim'] ?? 0); });
            } else {
                $data['members'][$old_gender][$target_index] = $target_member;
                usort($data['members'][$old_gender], function($a, $b) { return (int)($a['nim'] ?? 0) <=> (int)($b['nim'] ?? 0); });
            }
            $_SESSION['admin_msg'] = "Profil member diperbarui.";
        }
    }

    // 7. UPDATE SCHEDULE
    if ($action === 'update_schedule' && isset($_POST['schedule'])) {
        foreach ($_POST['schedule'] as $time => $days) {
            foreach ($days as $day => $val) {
                $data['schedule'][$time][$day] = htmlspecialchars($val);
            }
        }
        $_SESSION['admin_msg'] = "Jadwal berhasil diperbarui.";
    }

    // 8. DELETE MESSAGE
    if ($action === 'delete_message' && isset($_POST['id'])) {
        foreach ($data['messages'] as $k => $v) {
            if ($v['id'] == $_POST['id']) {
                unset($data['messages'][$k]);
                $_SESSION['admin_msg'] = "Pesan dihapus.";
                break;
            }
        }
        $data['messages'] = array_values($data['messages']);
    }

    // Save changes
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    
    // Redirect to clear POST
    header("Location: admin.php");
    exit;
}

$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devtronix Admin Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="uploads/kelas_logo_circle.svg?v=3">
    <!-- Space Grotesk Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Space Grotesk', 'sans-serif'] 
                    },
                    colors: {
                        'brutal-yellow': '#FFF000',
                        'brutal-green': '#00FF66',
                        'brutal-pink': '#FF007A',
                        'brutal-cyan': '#00F0FF',
                        'brutal-purple': '#9E00FF',
                        'brutal-cream': '#FAF6EE',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #FAF6EE;
            background-image: 
                radial-gradient(#000000 1.5px, transparent 1.5px), 
                radial-gradient(#000000 1.5px, #FAF6EE 1.5px);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
        }
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #FAF6EE; border-left: 2px solid #000; }
        ::-webkit-scrollbar-thumb { background: #000; border-radius: 0; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.2s cubic-bezier(0,0,0,1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        .neo-btn {
            transition: all 0.15s cubic-bezier(0, 0, 0, 1);
        }
        .neo-btn:hover {
            transform: translate(-3px, -3px);
        }
        .neo-btn:active {
            transform: translate(1px, 1px);
        }
    </style>
</head>
<body class="text-black antialiased min-h-screen selection:bg-brutal-yellow selection:text-black">
    
    <!-- Navbar -->
    <nav class="bg-[#FAF6EE] border-b-[3px] border-black sticky top-0 z-50 py-3 shadow-[0_2px_0_0_rgba(0,0,0,1)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white border-2 border-black rounded-lg overflow-hidden shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center p-0.5">
                        <img src="uploads/kelas_logo.jpg" alt="Logo Kelas" class="w-full h-full object-cover">
                    </div>
                    <span class="font-black text-2xl text-black uppercase tracking-tight">DEVTRONIX <span class="bg-brutal-yellow border-2 border-black px-2 py-0.5 rounded text-xs lowercase">cms</span></span>
                </div>
                <div class="flex gap-4">
                    <a href="index.php" target="_blank" class="neo-btn text-xs font-black text-black bg-brutal-cyan px-4 py-2.5 rounded-lg border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-1.5">
                        🌎 LIHAT WEB ⭧
                    </a>
                    <a href="logout.php" class="neo-btn text-xs font-black text-black bg-brutal-pink px-4 py-2.5 rounded-lg border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-1.5">
                        🚪 LOGOUT
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
        
        <!-- Sidebar Navigation (Physical Control Panel) -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white border-[3px] border-black rounded-xl p-5 sticky top-28 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="text-xs font-black text-black uppercase tracking-widest mb-4 px-2 pb-2 border-b-2 border-black border-dashed">🎛️ PANEL KONTROL</h3>
                <nav class="space-y-3">
                    <button onclick="switchAdminTab('gallery')" id="btn-gallery" class="neo-btn admin-tab w-full text-left px-4 py-3 rounded-lg text-sm font-black text-black border-2 border-black bg-brutal-yellow shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2">
                        📸 ALBUM GALERI
                    </button>
                    <button onclick="switchAdminTab('members')" id="btn-members" class="neo-btn admin-tab w-full text-left px-4 py-3 rounded-lg text-sm font-extrabold text-black border-2 border-black bg-brutal-cream shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2">
                        👥 ANGGOTA KELAS
                    </button>
                    <button onclick="switchAdminTab('schedule')" id="btn-schedule" class="neo-btn admin-tab w-full text-left px-4 py-3 rounded-lg text-sm font-extrabold text-black border-2 border-black bg-brutal-cream shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2">
                        📅 JADWAL KULIAH
                    </button>
                    <button onclick="switchAdminTab('messages')" id="btn-messages" class="neo-btn admin-tab w-full text-left px-4 py-3 rounded-lg text-sm font-extrabold text-black border-2 border-black bg-brutal-cream shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2">
                        💬 BISIKAN ANONIM
                    </button>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0">
            <?php if(isset($_SESSION['admin_msg'])): ?>
                <div class="bg-brutal-green border-[3px] border-black text-black font-black px-5 py-4 rounded-xl mb-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-sm flex justify-between items-center">
                    <span>💡 <?= $_SESSION['admin_msg']; unset($_SESSION['admin_msg']); ?></span>
                    <button onclick="this.parentElement.remove()" class="font-black text-lg">&times;</button>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['admin_err'])): ?>
                <div class="bg-brutal-pink border-[3px] border-black text-black font-black px-5 py-4 rounded-xl mb-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-sm flex justify-between items-center">
                    <span>💥 <?= $_SESSION['admin_err']; unset($_SESSION['admin_err']); ?></span>
                    <button onclick="this.parentElement.remove()" class="font-black text-lg">&times;</button>
                </div>
            <?php endif; ?>

            <!-- 1. GALLERY TAB -->
            <div id="tab-gallery" class="tab-content active space-y-8">
                <div class="bg-white border-[3px] border-black rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <h2 class="text-xl font-black text-black mb-6 uppercase tracking-wider border-b-3 border-black pb-3 flex items-center gap-2">
                        <span>📸 Upload Album Momen</span>
                    </h2>
                    
                    <form action="admin.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="action" value="upload_gallery">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Judul Momen</label>
                                <input type="text" name="image_title" required placeholder="Ex: Makrab Devtronix" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Tanggal</label>
                                <input type="date" name="image_date" required class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Lokasi</label>
                                <input type="text" name="image_location" required placeholder="Ex: Villa Kaliurang" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-black text-black uppercase tracking-wider">Keterangan Singkat</label>
                            <textarea name="image_desc" rows="2" placeholder="Ceritakan keseruan momen ini..." class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] resize-none"></textarea>
                        </div>

                        <div class="flex flex-col md:flex-row gap-6 items-start md:items-end border-t-2 border-black border-dashed pt-6">
                            <div class="flex-1 w-full space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Pilih File Foto (Bisa Banyak Sekaligus)</label>
                                <input type="file" name="gallery_images[]" multiple required accept="image/*" class="w-full bg-[#FAF6EE] border-2 border-black rounded-lg p-2 text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                            <button type="submit" class="neo-btn w-full md:w-auto bg-brutal-green text-black border-2 border-black px-8 py-3.5 rounded-lg text-sm font-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-[#00E057] uppercase tracking-wider cursor-pointer whitespace-nowrap">
                                📤 Upload Album
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white border-[3px] border-black rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <h2 class="text-xl font-black text-black mb-6 uppercase tracking-wider border-b-3 border-black pb-3">📸 Daftar Album Terbit</h2>
                    <?php if(empty($data['gallery'])): ?>
                        <p class="text-sm text-slate-500 italic py-6 text-center border-2 border-black border-dashed rounded-lg bg-[#FAF6EE]">Belum ada album foto yang diupload.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                            <?php foreach(array_reverse($data['gallery']) as $img): 
                                $cover = $img['photos'][0] ?? '';
                                $count = count($img['photos'] ?? []);
                            ?>
                                <div class="relative group rounded-xl border-2 border-black overflow-hidden bg-[#FAF6EE] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                                    <div class="aspect-video relative border-b-2 border-black overflow-hidden bg-black flex items-center justify-center">
                                        <img src="<?= $cover ?>" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-all duration-300">
                                        <div class="absolute bottom-2 left-2 bg-brutal-yellow text-black text-[10px] font-black px-2.5 py-1 rounded-lg border-2 border-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] flex items-center gap-1 font-mono">
                                            🖼️ <?= $count ?> FOTO
                                        </div>
                                    </div>
                                    
                                    <div class="p-3">
                                        <div class="font-black text-sm text-black truncate uppercase tracking-tight"><?= $img['name'] ?></div>
                                        <div class="text-[10px] text-slate-700 font-extrabold mt-1 font-mono uppercase">📅 <?= $img['date'] ?></div>
                                    </div>
                                    
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1.5 z-20">
                                        <button type="button" class="bg-brutal-cyan border-2 border-black text-black p-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] font-bold text-xs" title="Edit" onclick='openEditGalleryModal(<?= htmlspecialchars(json_encode($img), ENT_QUOTES, "UTF-8") ?>)'>✏️</button>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="delete_gallery">
                                            <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                            <button class="bg-brutal-pink border-2 border-black text-black p-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] font-bold text-xs" title="Hapus" onclick="return confirm('Hapus album ini?')">🗑️</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. MEMBERS TAB -->
            <div id="tab-members" class="tab-content space-y-8">
                <div class="bg-white border-[3px] border-black rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <h2 class="text-xl font-black text-black mb-6 uppercase tracking-wider border-b-3 border-black pb-3">👤 Tambah Anggota Warga</h2>
                    
                    <form action="admin.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="action" value="add_member">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Gender</label>
                                <select name="gender" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <option value="cowo">Laki-laki (Cowo)</option>
                                    <option value="cewe">Perempuan (Cewe)</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">NIM Akhiran</label>
                                <input type="number" name="member_nim" required placeholder="Ex: 089" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Julukan / Gelar</label>
                                <input type="text" name="member_julukan" required placeholder="Ex: Rust Wizard" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" name="member_name" required placeholder="Ex: Bagus Satria" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Asal Daerah</label>
                                <input type="text" name="member_asal" placeholder="Ex: Yogyakarta" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Hobi</label>
                                <input type="text" name="member_hobi" placeholder="Ex: Coding, Gaming" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">LinkedIn URL</label>
                                <input type="url" name="member_linkedin" placeholder="https://linkedin.com/in/..." class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Instagram Username</label>
                                <input type="text" name="member_instagram" placeholder="Ex: bagus_satria (Tanpa @)" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Nomor WA</label>
                                <input type="number" name="member_whatsapp" placeholder="Ex: 628123456789" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-black text-black uppercase tracking-wider">Motto Hidup</label>
                            <textarea name="member_motto" rows="2" placeholder="Tulis quote andalanmu..." class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] resize-none"></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-6 border-t-2 border-black border-dashed pt-6">
                            <div class="flex-1 w-full space-y-2">
                                <label class="block text-xs font-black text-black uppercase tracking-wider">Foto Profil (Pas Foto Rasio 3:4 akan dipotong)</label>
                                <input type="hidden" name="member_image_cropped" id="member_image_cropped">
                                <input type="file" id="member_image_input" accept="image/*" class="w-full bg-[#FAF6EE] border-2 border-black rounded-lg p-2 text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                
                                <div id="preview_container_add" class="mt-4 hidden p-3 border-2 border-black rounded-xl bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] w-fit flex flex-col items-center">
                                    <p class="text-xs font-black text-brutal-pink mb-2">💥 FOTO DI-CROP :</p>
                                    <img id="preview_add" class="w-24 h-32 object-cover rounded-lg border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                </div>
                            </div>
                            
                            <button type="submit" class="neo-btn w-full sm:w-auto bg-brutal-green text-black border-2 border-black px-8 py-3.5 rounded-lg text-sm font-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-[#00E057] uppercase tracking-wider cursor-pointer whitespace-nowrap h-fit">
                                + Tambah Warga
                            </button>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Cowo -->
                    <div class="bg-white border-[3px] border-black rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                        <h3 class="font-black text-lg text-black mb-4 uppercase tracking-tight flex items-center gap-2">
                            <span class="w-4 h-4 bg-brutal-cyan border-2 border-black rounded-full"></span>
                            <span>COWO (<?= count($data['members']['cowo']) ?> ANGGOTA)</span>
                        </h3>
                        <div class="space-y-3">
                            <?php foreach($data['members']['cowo'] as $m): ?>
                                <div class="flex justify-between items-center bg-[#FAF6EE] border-2 border-black p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <div class="font-black text-sm text-black">
                                        <?= $m['name'] ?> <span class="bg-brutal-cyan border border-black text-[9px] px-1.5 py-0.5 rounded font-mono font-black">NIM <?= htmlspecialchars($m['nim'] ?? '...') ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="neo-btn bg-brutal-yellow text-black border-2 border-black text-xs font-black px-3 py-1.5 rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]" onclick='openEditMemberModal("cowo", <?= htmlspecialchars(json_encode($m), ENT_QUOTES, "UTF-8") ?>)'>EDIT</button>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="delete_member">
                                            <input type="hidden" name="gender" value="cowo">
                                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                            <button class="neo-btn bg-brutal-pink text-black border-2 border-black text-xs font-black px-3 py-1.5 rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]" onclick="return confirm('Hapus member cowo ini?')">HAPUS</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Cewe -->
                    <div class="bg-white border-[3px] border-black rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                        <h3 class="font-black text-lg text-black mb-4 uppercase tracking-tight flex items-center gap-2">
                            <span class="w-4 h-4 bg-brutal-pink border-2 border-black rounded-full"></span>
                            <span>CEWE (<?= count($data['members']['cewe']) ?> ANGGOTA)</span>
                        </h3>
                        <div class="space-y-3">
                            <?php foreach($data['members']['cewe'] as $m): ?>
                                <div class="flex justify-between items-center bg-[#FAF6EE] border-2 border-black p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <div class="font-black text-sm text-black">
                                        <?= $m['name'] ?> <span class="bg-brutal-pink text-black border border-black text-[9px] px-1.5 py-0.5 rounded font-mono font-black">NIM <?= htmlspecialchars($m['nim'] ?? '...') ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="neo-btn bg-brutal-yellow text-black border-2 border-black text-xs font-black px-3 py-1.5 rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]" onclick='openEditMemberModal("cewe", <?= htmlspecialchars(json_encode($m), ENT_QUOTES, "UTF-8") ?>)'>EDIT</button>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="delete_member">
                                            <input type="hidden" name="gender" value="cewe">
                                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                            <button class="neo-btn bg-brutal-pink text-black border-2 border-black text-xs font-black px-3 py-1.5 rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]" onclick="return confirm('Hapus member cewe ini?')">HAPUS</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. SCHEDULE TAB -->
            <div id="tab-schedule" class="tab-content space-y-8">
                <div class="bg-white border-[3px] border-black rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <h2 class="text-xl font-black text-black mb-6 uppercase tracking-wider border-b-3 border-black pb-3">📅 Editor Jadwal Kuliah</h2>
                    
                    <form action="admin.php" method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="update_schedule">
                        <div class="overflow-x-auto border-3 border-black rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead class="text-xs text-black bg-brutal-yellow border-b-3 border-black uppercase font-black tracking-wider">
                                    <tr>
                                        <th class="p-3 border-r-2 border-black">Waktu</th>
                                        <?php foreach($days as $d): ?>
                                            <th class="p-3 border-r-2 border-black"><?= $d ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['schedule'] as $time => $rowDays): ?>
                                        <tr class="border-b-2 border-black bg-white hover:bg-brutal-cream transition-colors">
                                            <td class="p-3 border-r-2 border-black text-black font-black font-mono text-xs whitespace-nowrap bg-[#FAF6EE] w-36">
                                                ⏱️ <?= $time ?>
                                            </td>
                                            <?php foreach($days as $day): ?>
                                                <td class="p-1 border-r-2 border-black">
                                                    <input type="text" name="schedule[<?= $time ?>][<?= $day ?>]" value="<?= $rowDays[$day] ?? '' ?>" class="w-full bg-transparent text-black text-xs font-bold px-3 py-2.5 focus:outline-none focus:bg-brutal-yellow transition-all rounded" placeholder="-">
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <button type="submit" class="neo-btn bg-brutal-green text-black border-2 border-black px-8 py-3.5 rounded-lg text-sm font-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-[#00E057] uppercase tracking-wider cursor-pointer">
                            💾 Simpan Jadwal Utama
                        </button>
                    </form>
                </div>
            </div>

            <!-- 4. MESSAGES TAB -->
            <div id="tab-messages" class="tab-content space-y-8">
                <div class="bg-white border-[3px] border-black rounded-xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-center border-b-3 border-black pb-3 mb-6">
                        <h2 class="text-xl font-black text-black uppercase tracking-wider">💬 Kotak Bisikan Anonim</h2>
                        <span class="bg-brutal-purple text-white border-2 border-black text-xs font-black px-3.5 py-1 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider"><?= count($data['messages']) ?> Total Bisikan</span>
                    </div>

                    <?php if(empty($data['messages'])): ?>
                        <p class="text-sm text-slate-500 italic py-8 text-center border-2 border-black border-dashed rounded-lg bg-[#FAF6EE]">Belum ada bisikan anonim masuk ke sistem.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach(array_reverse($data['messages']) as $msg): ?>
                                <div class="bg-[#FAF6EE] border-2 border-black p-4 rounded-xl flex justify-between gap-6 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2.5">
                                            <span class="bg-brutal-pink text-black border border-black text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-wider shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">ANONYMOUS</span>
                                            <span class="text-[10px] text-slate-600 font-mono font-bold">⏱️ <?= $msg['date'] ?></span>
                                        </div>
                                        <p class="text-sm font-bold text-black leading-relaxed mt-2"><?= $msg['text'] ?></p>
                                    </div>
                                    <form method="POST" class="flex-shrink-0 self-center">
                                        <input type="hidden" name="action" value="delete_message">
                                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                        <button class="neo-btn bg-brutal-pink border-2 border-black text-black p-2.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] transition-colors" title="Hapus Permanen" onclick="return confirm('Hapus pesan ini?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>

    <!-- Edit Gallery Modal -->
    <div id="editGalleryModal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white border-[4px] border-black rounded-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeModal('editGalleryModal')" class="absolute top-4 right-4 text-black bg-brutal-pink border-2 border-black p-1.5 rounded-lg font-black text-lg transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">&times;</button>
            <h2 class="text-xl font-black text-black mb-6 uppercase tracking-wider border-b-3 border-black pb-3">✏️ Edit Album Galeri</h2>
            
            <form action="admin.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="edit_gallery">
                <input type="hidden" name="id" id="eg_id">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Judul Momen</label>
                        <input type="text" name="image_title" id="eg_title" required class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Tanggal</label>
                        <input type="date" name="image_date" id="eg_date" required class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Lokasi</label>
                        <input type="text" name="image_location" id="eg_location" required class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-black uppercase tracking-wider">Keterangan Singkat</label>
                    <textarea name="image_desc" id="eg_desc" rows="2" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all resize-none"></textarea>
                </div>

                <div class="space-y-2 border-t-2 border-black border-dashed pt-4">
                    <label class="block text-xs font-black text-black uppercase tracking-wider">Centang untuk Hapus Foto dari Album</label>
                    <div id="eg_photo_list" class="flex gap-4 overflow-x-auto py-3 bg-[#FAF6EE] p-3 rounded-lg border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] min-h-[6rem]">
                        <!-- JS injected -->
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row gap-6 items-start md:items-end pt-4">
                    <div class="flex-1 w-full space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Tambah Foto Baru (Opsional)</label>
                        <input type="file" name="gallery_images[]" multiple accept="image/*" class="w-full bg-[#FAF6EE] border-2 border-black rounded-lg p-2 text-xs font-black">
                    </div>
                    
                    <button type="submit" class="neo-btn w-full md:w-auto bg-brutal-green text-black border-2 border-black px-8 py-3.5 rounded-lg text-sm font-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-[#00E057] uppercase tracking-wider cursor-pointer whitespace-nowrap">
                        💾 Simpan Album
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div id="editMemberModal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white border-[4px] border-black rounded-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeModal('editMemberModal')" class="absolute top-4 right-4 text-black bg-brutal-pink border-2 border-black p-1.5 rounded-lg font-black text-lg transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">&times;</button>
            <h2 class="text-xl font-black text-black mb-6 uppercase tracking-wider border-b-3 border-black pb-3">✏️ Edit Profil Member</h2>
            
            <form action="admin.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="edit_member">
                <input type="hidden" name="id" id="em_id">
                <input type="hidden" name="old_gender" id="em_old_gender">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Gender</label>
                        <select name="gender" id="em_gender" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                            <option value="cowo">Laki-laki (Cowo)</option>
                            <option value="cewe">Perempuan (Cewe)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">NIM Akhiran</label>
                        <input type="number" name="member_nim" id="em_nim" required class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Julukan / Gelar</label>
                        <input type="text" name="member_julukan" id="em_julukan" required class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="member_name" id="em_name" required class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Asal Daerah</label>
                        <input type="text" name="member_asal" id="em_asal" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Hobi</label>
                        <input type="text" name="member_hobi" id="em_hobi" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">LinkedIn URL</label>
                        <input type="url" name="member_linkedin" id="em_linkedin" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Instagram Username</label>
                        <input type="text" name="member_instagram" id="em_instagram" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">WhatsApp</label>
                        <input type="number" name="member_whatsapp" id="em_whatsapp" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-black uppercase tracking-wider">Motto Hidup</label>
                    <textarea name="member_motto" id="em_motto" rows="2" class="w-full bg-white border-2 border-black rounded-lg px-3.5 py-2.5 text-sm text-black font-extrabold focus:outline-none focus:bg-brutal-yellow transition-all resize-none"></textarea>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6 border-t-2 border-black border-dashed pt-6">
                    <div class="flex-1 w-full space-y-2">
                        <label class="block text-xs font-black text-black uppercase tracking-wider">Ganti Foto Profil (Pas Foto Rasio 3:4 akan dipotong)</label>
                        <input type="hidden" name="member_image_cropped_edit" id="member_image_cropped_edit">
                        <input type="file" id="member_image_input_edit" accept="image/*" class="w-full bg-[#FAF6EE] border-2 border-black rounded-lg p-2 text-xs font-black">
                        
                        <div id="preview_container_edit" class="mt-4 hidden p-3 border-2 border-black rounded-xl bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] w-fit flex flex-col items-center">
                            <p class="text-xs font-black text-brutal-pink mb-2">💥 FOTO BARU :</p>
                            <img id="preview_edit" class="w-24 h-32 object-cover rounded-lg border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        </div>
                    </div>
                    
                    <button type="submit" class="neo-btn w-full sm:w-auto bg-brutal-green text-black border-2 border-black px-8 py-3.5 rounded-lg text-sm font-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-[#00E057] uppercase tracking-wider cursor-pointer whitespace-nowrap h-fit">
                        💾 Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cropper Modal -->
    <div id="cropperModal" class="hidden fixed inset-0 z-[150] bg-black/80 flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-white p-6 rounded-2xl border-[4px] border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
            <h3 class="text-black font-black text-xl mb-4 uppercase tracking-tight">✂️ Potong Pas Foto (3:4)</h3>
            <div class="max-h-[60vh] w-full bg-[#FAF6EE] border-2 border-black flex justify-center overflow-hidden rounded-xl mb-4 p-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                <img id="cropperImage" class="max-w-full max-h-full">
            </div>
            <div class="flex justify-end gap-4 mt-4 border-t-2 border-black border-dashed pt-4">
                <button type="button" id="btnCancelCrop" class="neo-btn px-5 py-2.5 bg-brutal-pink text-black border-2 border-black rounded-lg text-sm font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider cursor-pointer">Batal</button>
                <button type="button" id="btnApplyCrop" class="neo-btn px-5 py-2.5 bg-brutal-green text-black border-2 border-black rounded-lg text-sm font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider cursor-pointer">Terapkan Perubahan</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openEditGalleryModal(img) {
            document.getElementById('editGalleryModal').classList.remove('hidden');
            document.getElementById('eg_id').value = img.id;
            document.getElementById('eg_title').value = img.name || '';
            document.getElementById('eg_date').value = img.date || '';
            document.getElementById('eg_location').value = img.location || '';
            document.getElementById('eg_desc').value = img.description || '';
            
            const photoList = document.getElementById('eg_photo_list');
            photoList.innerHTML = '';
            if (img.photos && img.photos.length > 0) {
                img.photos.forEach(photoPath => {
                    photoList.innerHTML += `
                        <div class="relative w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 border-2 border-black rounded-lg overflow-hidden group shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] bg-black">
                            <img src="${photoPath}" class="w-full h-full object-cover opacity-95">
                            <div class="absolute top-0 right-0 bg-brutal-pink border-b-2 border-l-2 border-black p-1 flex items-center justify-center cursor-pointer">
                                <input type="checkbox" name="delete_photos[]" value="${photoPath}" class="w-4 h-4 cursor-pointer" title="Hapus foto ini dari album">
                            </div>
                        </div>
                    `;
                });
            } else {
                photoList.innerHTML = '<span class="text-xs text-slate-500 italic">Tidak ada foto di album ini.</span>';
            }
        }

        function openEditMemberModal(gender, m) {
            document.getElementById('editMemberModal').classList.remove('hidden');
            document.getElementById('em_id').value = m.id;
            document.getElementById('em_old_gender').value = gender;
            document.getElementById('em_gender').value = gender;
            document.getElementById('em_nim').value = m.nim || '';
            document.getElementById('em_julukan').value = m.julukan || 'Anggota';
            document.getElementById('em_name').value = m.name || '';
            document.getElementById('em_asal').value = m.asal || '';
            document.getElementById('em_hobi').value = m.hobi || '';
            document.getElementById('em_linkedin').value = m.linkedin || '';
            document.getElementById('em_instagram').value = m.instagram || '';
            document.getElementById('em_whatsapp').value = m.whatsapp || '';
            document.getElementById('em_motto').value = m.motto || '';
            document.getElementById('member_image_cropped_edit').value = '';
            document.getElementById('member_image_input_edit').value = '';
            document.getElementById('preview_container_edit').classList.add('hidden');
        }

        function switchAdminTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.admin-tab').forEach(el => {
                el.className = "neo-btn admin-tab w-full text-left px-4 py-3 rounded-lg text-sm font-extrabold text-black border-2 border-black bg-brutal-cream shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2";
            });
            // Show selected
            document.getElementById('tab-' + tabName).classList.add('active');
            document.getElementById('btn-' + tabName).className = "neo-btn admin-tab w-full text-left px-4 py-3 rounded-lg text-sm font-black text-black border-2 border-black bg-brutal-yellow shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2";
        }

        // --- CROPPER SCRIPT ---
        let cropper = null;
        let currentInputTarget = null; // 'add' or 'edit'

        const memberInputAdd = document.getElementById('member_image_input');
        const memberInputEdit = document.getElementById('member_image_input_edit');
        const cropperModal = document.getElementById('cropperModal');
        const cropperImage = document.getElementById('cropperImage');
        const btnCancelCrop = document.getElementById('btnCancelCrop');
        const btnApplyCrop = document.getElementById('btnApplyCrop');

        function handleFileSelect(e, target) {
            const file = e.target.files[0];
            if (file) {
                currentInputTarget = target;
                const reader = new FileReader();
                reader.onload = function(event) {
                    cropperImage.src = event.target.result;
                    cropperModal.classList.remove('hidden');
                    
                    if (cropper) { cropper.destroy(); }
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 3 / 4, // Pas foto ratio
                        viewMode: 2,
                        autoCropArea: 1,
                        background: false
                    });
                };
                reader.readAsDataURL(file);
            }
        }

        memberInputAdd.addEventListener('change', (e) => handleFileSelect(e, 'add'));
        memberInputEdit.addEventListener('change', (e) => handleFileSelect(e, 'edit'));

        btnCancelCrop.addEventListener('click', () => {
            cropperModal.classList.add('hidden');
            if(cropper) cropper.destroy();
            cropper = null;
            if(currentInputTarget === 'add') memberInputAdd.value = '';
            if(currentInputTarget === 'edit') memberInputEdit.value = '';
        });

        btnApplyCrop.addEventListener('click', () => {
            if (!cropper) return;
            const canvas = cropper.getCroppedCanvas({
                width: 600,
                height: 800
            });
            const base64Image = canvas.toDataURL('image/webp', 0.85);
            
            if (currentInputTarget === 'add') {
                document.getElementById('member_image_cropped').value = base64Image;
                const preview = document.getElementById('preview_add');
                preview.src = base64Image;
                document.getElementById('preview_container_add').classList.remove('hidden');
            } else if (currentInputTarget === 'edit') {
                document.getElementById('member_image_cropped_edit').value = base64Image;
                const preview = document.getElementById('preview_edit');
                preview.src = base64Image;
                document.getElementById('preview_container_edit').classList.remove('hidden');
            }
            
            cropperModal.classList.add('hidden');
            cropper.destroy();
            cropper = null;
        });

    </script>
</body>
</html>
