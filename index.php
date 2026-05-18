<?php
// FILE: website/index.php
session_start();

$dataFile = 'data.json';

// Initialize data structure if not exists
$data = [];
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true) ?? [];
}
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

// Handle Anonymous Message Only (Public can post)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $msg = trim($_POST['anon_message'] ?? '');
    if (!empty($msg)) {
        $data['messages'][] = [
            'id' => time(),
            'text' => htmlspecialchars($msg),
            'date' => date('d-m-Y H:i')
        ];
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        $_SESSION['msg_success'] = "Pesan berhasil dikirim secara anonim!";
    }
    header("Location: index.php#pesan");
    exit;
}

$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
$membersSemua = array_merge($data['members']['cowo'], $data['members']['cewe']);
usort($membersSemua, function($a, $b) {
    return (int)($a['nim'] ?? 0) <=> (int)($b['nim'] ?? 0);
});

// Pre-calculate rowspans for the schedule
$scheduleMap = $data['schedule'];
$times_keys = array_keys($scheduleMap);
$rowspans = [];
$skip = [];

foreach ($days as $day) {
    for ($i = 0; $i < count($times_keys); $i++) {
        $t = $times_keys[$i];
        if (isset($skip[$t][$day]) && $skip[$t][$day]) continue;
        
        $subject = trim($scheduleMap[$t][$day] ?? '');
        $span = 1;

        if (!empty($subject)) {
            for ($j = $i + 1; $j < count($times_keys); $j++) {
                $next_t = $times_keys[$j];
                $next_subject = trim($scheduleMap[$next_t][$day] ?? '');
                if ($next_subject === $subject) {
                    $span++;
                    $skip[$next_t][$day] = true;
                } else {
                    break;
                }
            }
        }
        $rowspans[$t][$day] = $span;
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devtronix - Official Website</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    colors: {
                        darkbg: '#121212',    
                        darkcard: '#1e1e1e',  
                        darkborder: '#000000', 
                        brutal: {
                            yellow: '#FFE600',
                            green: '#00FF66',
                            pink: '#FF3B8B',
                            cyan: '#00E5FF',
                            purple: '#A855F7',
                            cream: '#FAF6EE',
                            darkcream: '#FFFDF5',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-padding-top: 100px; }
        ::-webkit-scrollbar { display: none; }
        html { -ms-overflow-style: none; scrollbar-width: none; }

        /* Neubrutalism Styles */
        .neo-border {
            border: 3px solid #000000;
        }
        .dark .neo-border {
            border: 3px solid #FFFFFF;
        }
        
        .neo-btn {
            transition: all 0.15s cubic-bezier(0, 0, 0.2, 1);
        }
        .neo-btn:hover {
            transform: translate(-3px, -3px);
        }
        .neo-btn:active {
            transform: translate(3px, 3px);
        }

        .member-grid { display: none; }
        .member-grid.active { 
            display: grid; 
            animation: gridPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes gridPop {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Scroll Reveal Styles */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .scroll-reveal.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="text-black dark:text-white bg-[#FAF6EE] dark:bg-[#121212] antialiased min-h-screen selection:bg-brutal-yellow selection:text-black relative font-sans pb-20 md:pb-0 transition-colors duration-300">

    <!-- Background (Clean Cream) -->
    <div class="fixed inset-0 z-[-2] overflow-hidden bg-[#FAF6EE] dark:bg-[#121212] transition-colors duration-300">
        <!-- Faint texture -->
        <div class="absolute inset-0 opacity-[0.015]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>
    </div>

    <!-- NAVBAR (Desktop) -->
    <nav class="fixed top-4 left-6 right-6 z-50 py-3 px-6 bg-[#FAF6EE] dark:bg-[#1E1E1E] border-[3px] border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] rounded-xl transition-all duration-300 max-w-5xl mx-auto">
        <div class="flex justify-between items-center">
            <!-- Left: Logo -->
            <div class="flex items-center gap-3 cursor-pointer hover:scale-105 active:scale-95 transition-transform" onclick="window.scrollTo(0,0)">
                <img src="uploads/kelas_logo.jpg" alt="Logo" class="w-9 h-9 object-contain rounded-lg border-2 border-black dark:border-white bg-white">
                <span class="font-extrabold text-lg tracking-tight text-black dark:text-white uppercase">DEVTRONIX</span>
            </div>
            
            <!-- Center: Links -->
            <div class="hidden md:flex items-center justify-center gap-4 text-sm font-bold text-black dark:text-white">
                <a href="#home" class="hover:bg-brutal-yellow hover:text-black border border-transparent hover:border-black dark:hover:border-white px-3 py-1.5 rounded-lg transition-all">Home</a>
                <a href="#member" class="hover:bg-brutal-green hover:text-black border border-transparent hover:border-black dark:hover:border-white px-3 py-1.5 rounded-lg transition-all">Member</a>
                <a href="#gallery" class="hover:bg-brutal-pink hover:text-black border border-transparent hover:border-black dark:hover:border-white px-3 py-1.5 rounded-lg transition-all">Gallery</a>
                <a href="#schedule" class="hover:bg-brutal-cyan hover:text-black border border-transparent hover:border-black dark:hover:border-white px-3 py-1.5 rounded-lg transition-all">Schedule</a>
                <a href="#permainan" class="hover:bg-brutal-purple hover:text-black border border-transparent hover:border-black dark:hover:border-white px-3 py-1.5 rounded-lg transition-all">Game</a>
                <a href="#pesan" class="hover:bg-brutal-yellow hover:text-black border border-transparent hover:border-black dark:hover:border-white px-3 py-1.5 rounded-lg transition-all">Pesan</a>
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Navigation -->
    <div class="md:hidden fixed bottom-4 left-4 right-4 bg-[#FAF6EE] dark:bg-[#1E1E1E] border-[3px] border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] z-50 px-4 py-2 flex justify-between items-center rounded-xl">
        <a href="#member" class="flex flex-col items-center p-1.5 text-black dark:text-white hover:bg-brutal-green border border-transparent hover:border-black dark:hover:border-white rounded-lg transition-all">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="text-[9px] font-extrabold tracking-wide">Member</span>
        </a>
        <a href="#gallery" class="flex flex-col items-center p-1.5 text-black dark:text-white hover:bg-brutal-pink border border-transparent hover:border-black dark:hover:border-white rounded-lg transition-all">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="text-[9px] font-extrabold tracking-wide">Gallery</span>
        </a>
        <a href="#schedule" class="flex flex-col items-center p-1.5 text-black dark:text-white hover:bg-brutal-cyan border border-transparent hover:border-black dark:hover:border-white rounded-lg transition-all">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="text-[9px] font-extrabold tracking-wide">Schedule</span>
        </a>
        <a href="#permainan" class="flex flex-col items-center p-1.5 text-black dark:text-white hover:bg-brutal-purple border border-transparent hover:border-black dark:hover:border-white rounded-lg transition-all">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[9px] font-extrabold tracking-wide">Game</span>
        </a>
        <a href="#pesan" class="flex flex-col items-center p-1.5 text-black dark:text-white hover:bg-[#FF8A00] border border-transparent hover:border-black dark:hover:border-white rounded-lg transition-all">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            <span class="text-[9px] font-extrabold tracking-wide">Pesan</span>
        </a>
    </div>

    <!-- Background Wrapper for Hero (Restored) -->
    <div class="absolute top-0 left-0 right-0 h-[100vh] z-[-1] pointer-events-none opacity-20 dark:opacity-10 bg-cover bg-center transition-all duration-300" style="background-image: url('uploads/foto_kelas.jpg'); mask-image: linear-gradient(to bottom, black 60%, transparent 100%); -webkit-mask-image: linear-gradient(to bottom, black 60%, transparent 100%);"></div>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">

        <!-- 1. HOME SECTION -->
        <section id="home" class="min-h-[95dvh] relative z-10 flex flex-col items-center justify-center text-center pt-32 pb-20">
            
            <!-- Tag -->
            <div class="inline-flex items-center gap-2 bg-brutal-yellow text-black border-[3px] border-black px-4 py-2 rounded-lg mb-8 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] dark:border-white font-extrabold text-xs uppercase tracking-wider">
                <span class="w-3.5 h-3.5 rounded-full bg-brutal-green border-2 border-black animate-pulse"></span>
                Website Resmi Devtronix
            </div>

            <!-- Main Title -->
            <h1 class="text-5xl md:text-[6rem] font-black tracking-tight flex flex-col gap-2 md:gap-4 mb-6 leading-none">
                <span class="text-black dark:text-white">Selamat Datang</span>
                <span class="relative inline-block bg-brutal-yellow text-black border-[4px] border-black px-6 py-2 rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] uppercase select-none transform -rotate-1 self-center scale-95 md:scale-100">
                    di Devtronix
                </span>
            </h1>

            <!-- Description -->
            <p class="text-black dark:text-white max-w-2xl text-lg md:text-xl mb-12 font-bold leading-relaxed px-4 mt-6">
                Wadah untuk berkreasi, berbagi memori, dan melihat susunan jadwal secara terstruktur. Selamat mengeksplorasi perjalanan kami yang luar biasa.
            </p>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-6 z-20 w-full justify-center">
                <a href="#member" class="neo-btn px-10 py-4 rounded-xl bg-brutal-green text-black border-[3px] border-black dark:border-white font-black text-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] hover:bg-[#00E057] w-11/12 sm:w-auto text-center">
                    Kenali Member
                </a>
                <a href="#schedule" class="neo-btn px-10 py-4 rounded-xl bg-[#FAF6EE] dark:bg-[#1E1E1E] text-black dark:text-white border-[3px] border-black dark:border-white font-black text-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] hover:bg-brutal-cyan hover:text-black w-11/12 sm:w-auto text-center">
                    Lihat Jadwal
                </a>
            </div>
        </section>

        <!-- 2. MEMBER SECTION -->
        <section id="member" class="pt-8 scroll-reveal">
            <h2 class="text-4xl font-black text-center text-black dark:text-white mb-10 uppercase tracking-wider">
                Warga Devtronix
            </h2>

            <div class="flex justify-center mb-12">
                <div class="bg-[#FAF6EE] dark:bg-[#1E1E1E] p-2 border-[3px] border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] flex flex-wrap gap-3 items-center rounded-xl">
                    <button onclick="showMembers('14')" id="btn-14" class="neo-btn member-tab px-6 py-2.5 rounded-lg text-sm font-extrabold text-black border-2 border-black dark:border-white bg-[#FAF6EE] dark:bg-[#1E1E1E] dark:text-white transition-all flex items-center justify-center gap-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14v7m-3-3h6"></path></svg>
                        <?= count($data['members']['cowo'] ?? []) ?> Cowo
                    </button>
                    <button onclick="showMembers('25')" id="btn-25" class="neo-btn member-tab px-6 py-2.5 rounded-lg text-sm font-extrabold text-black border-2 border-black bg-brutal-yellow transition-all flex items-center justify-center gap-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <?= count($membersSemua ?? []) ?> Semua
                    </button>
                    <button onclick="showMembers('11')" id="btn-11" class="neo-btn member-tab px-6 py-2.5 rounded-lg text-sm font-extrabold text-black border-2 border-black dark:border-white bg-[#FAF6EE] dark:bg-[#1E1E1E] dark:text-white transition-all flex items-center justify-center gap-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4a4 4 0 100 8 4 4 0 000-8zM2 20h20"></path></svg>
                        <?= count($data['members']['cewe'] ?? []) ?> Cewe
                    </button>
                </div>
            </div>

            <!-- Grid 14 (Cowo) -->
            <div id="grid-14" class="member-grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <?php if (empty($data['members']['cowo'])): ?>
                    <div class="col-span-full text-center text-black dark:text-white py-12 italic border-[3px] border-black dark:border-white border-dashed rounded-xl bg-white dark:bg-darkcard">Belum ada data anggota.</div>
                <?php endif; ?>
                <?php foreach ($data['members']['cowo'] as $i => $m): 
                    $jsonAttr = htmlspecialchars(json_encode([
                        'image' => $m['image'] ?? '',
                        'name' => $m['name'] ?? 'Anggota',
                        'nim' => $m['nim'] ?? '',
                        'asal' => $m['asal'] ?? '-',
                        'hobi' => $m['hobi'] ?? '-',
                        'linkedin' => $m['linkedin'] ?? '',
                        'instagram' => $m['instagram'] ?? '',
                        'whatsapp' => $m['whatsapp'] ?? '',
                        'motto' => $m['motto'] ?? '',
                        'julukan' => $m['julukan'] ?? 'Anggota'
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                    <div onclick="openMemberModal(<?= $jsonAttr ?>)" class="neo-btn bg-white dark:bg-darkcard border-[3px] border-black dark:border-white rounded-xl aspect-[3/4] flex flex-col items-center justify-center relative overflow-hidden group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] cursor-pointer">
                        <?php if(!empty($m['image'])): ?>
                            <img src="<?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" class="absolute inset-0 w-full h-full object-cover z-0 transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-10"></div>
                        <?php else: ?>
                            <div class="absolute inset-0 bg-brutal-cyan dark:bg-[#1E1E1E] flex flex-col items-center justify-center z-0">
                                <svg class="w-14 h-14 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-3 left-3 bg-brutal-cyan text-black text-xs font-mono font-black px-2.5 py-1 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] z-20">
                            NIM <?= htmlspecialchars(str_pad($m['nim'] ?? '', 2, '0', STR_PAD_LEFT)) ?>
                        </div>
                        
                        <div class="relative z-20 mt-auto pb-4 w-full text-center px-2">
                            <span class="text-sm text-white font-black truncate block w-full drop-shadow-[0_2px_2px_rgba(0,0,0,1)]"><?= htmlspecialchars($m['name']) ?></span>
                            <span class="text-xs text-brutal-cyan font-extrabold mt-1 block drop-shadow-[0_1px_1px_rgba(0,0,0,1)] uppercase tracking-wide"><?= htmlspecialchars($m['julukan'] ?? 'Anggota') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Grid 25 (Semua) -->
            <div id="grid-25" class="member-grid active grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <?php if (empty($membersSemua)): ?>
                    <div class="col-span-full text-center text-black dark:text-white py-12 italic border-[3px] border-black dark:border-white border-dashed rounded-xl bg-white dark:bg-darkcard">Belum ada data anggota.</div>
                <?php endif; ?>
                <?php foreach ($membersSemua as $i => $m): 
                    $jsonAttr = htmlspecialchars(json_encode([
                        'image' => $m['image'] ?? '',
                        'name' => $m['name'] ?? 'Anggota',
                        'nim' => $m['nim'] ?? '',
                        'asal' => $m['asal'] ?? '-',
                        'hobi' => $m['hobi'] ?? '-',
                        'linkedin' => $m['linkedin'] ?? '',
                        'instagram' => $m['instagram'] ?? '',
                        'whatsapp' => $m['whatsapp'] ?? '',
                        'motto' => $m['motto'] ?? '',
                        'julukan' => $m['julukan'] ?? 'Anggota'
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                    <div onclick="openMemberModal(<?= $jsonAttr ?>)" class="neo-btn bg-white dark:bg-darkcard border-[3px] border-black dark:border-white rounded-xl aspect-[3/4] flex flex-col items-center justify-center relative overflow-hidden group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] cursor-pointer">
                        <?php if(!empty($m['image'])): ?>
                            <img src="<?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" class="absolute inset-0 w-full h-full object-cover z-0 transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-10"></div>
                        <?php else: ?>
                            <div class="absolute inset-0 bg-brutal-yellow dark:bg-[#1E1E1E] flex flex-col items-center justify-center z-0">
                                <svg class="w-14 h-14 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-3 left-3 bg-brutal-yellow text-black text-xs font-mono font-black px-2.5 py-1 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] z-20">
                            NIM <?= htmlspecialchars(str_pad($m['nim'] ?? '', 2, '0', STR_PAD_LEFT)) ?>
                        </div>
                        
                        <div class="relative z-20 mt-auto pb-4 w-full text-center px-2">
                            <span class="text-sm text-white font-black truncate block w-full drop-shadow-[0_2px_2px_rgba(0,0,0,1)]"><?= htmlspecialchars($m['name']) ?></span>
                            <span class="text-xs text-brutal-yellow font-extrabold mt-1 block drop-shadow-[0_1px_1px_rgba(0,0,0,1)] uppercase tracking-wide"><?= htmlspecialchars($m['julukan'] ?? 'Anggota') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Grid 11 (Cewe) -->
            <div id="grid-11" class="member-grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <?php if (empty($data['members']['cewe'])): ?>
                    <div class="col-span-full text-center text-black dark:text-white py-12 italic border-[3px] border-black dark:border-white border-dashed rounded-xl bg-white dark:bg-darkcard">Belum ada data anggota.</div>
                <?php endif; ?>
                <?php foreach ($data['members']['cewe'] as $i => $m): 
                    $jsonAttr = htmlspecialchars(json_encode([
                        'image' => $m['image'] ?? '',
                        'name' => $m['name'] ?? 'Anggota',
                        'nim' => $m['nim'] ?? '',
                        'asal' => $m['asal'] ?? '-',
                        'hobi' => $m['hobi'] ?? '-',
                        'linkedin' => $m['linkedin'] ?? '',
                        'instagram' => $m['instagram'] ?? '',
                        'whatsapp' => $m['whatsapp'] ?? '',
                        'motto' => $m['motto'] ?? '',
                        'julukan' => $m['julukan'] ?? 'Anggota'
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                    <div onclick="openMemberModal(<?= $jsonAttr ?>)" class="neo-btn bg-white dark:bg-darkcard border-[3px] border-black dark:border-white rounded-xl aspect-[3/4] flex flex-col items-center justify-center relative overflow-hidden group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] cursor-pointer">
                        <?php if(!empty($m['image'])): ?>
                            <img src="
                            <?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" class="absolute inset-0 w-full h-full object-cover z-0 transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-10"></div>
                        <?php else: ?>
                            <div class="absolute inset-0 bg-brutal-pink dark:bg-[#1E1E1E] flex flex-col items-center justify-center z-0">
                                <svg class="w-14 h-14 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-3 left-3 bg-brutal-pink text-black text-xs font-mono font-black px-2.5 py-1 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] z-20">
                            NIM <?= htmlspecialchars(str_pad($m['nim'] ?? '', 2, '0', STR_PAD_LEFT)) ?>
                        </div>
                        
                        <div class="relative z-20 mt-auto pb-4 w-full text-center px-2">
                            <span class="text-sm text-white font-black truncate block w-full drop-shadow-[0_2px_2px_rgba(0,0,0,1)]"><?= htmlspecialchars($m['name']) ?></span>
                            <span class="text-xs text-brutal-pink font-extrabold mt-1 block drop-shadow-[0_1px_1px_rgba(0,0,0,1)] uppercase tracking-wide"><?= htmlspecialchars($m['julukan'] ?? 'Anggota') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 3. GALLERY SECTION -->
        <section id="gallery" class="pt-8 bg-[#FAF6EE] dark:bg-[#121212] -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-16 border-y-[3px] border-black dark:border-white scroll-reveal">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-4xl font-black text-center text-black dark:text-white mb-12 uppercase tracking-wider">
                    Galeri Memori
                </h2>

                <?php if (empty($data['gallery'])): ?>
                    <div class="text-center py-12 border-[3px] border-dashed border-black dark:border-white rounded-xl text-black dark:text-white bg-white dark:bg-darkcard shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="font-extrabold text-lg">Belum ada foto album.</p>
                        <span class="text-xs font-mono mt-1 inline-block">Admin dapat menambahkan foto via CMS Dashboard.</span>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                        <?php foreach (array_reverse($data['gallery']) as $img): 
                            $photos = $img['photos'] ?? [];
                            $cover = $photos[0] ?? 'placeholder.png';
                            $count = count($photos);
                            $jsonAttr = htmlspecialchars(json_encode([
                                'photos' => $photos,
                                'name' => $img['name'] ?? 'Untitled',
                                'date' => $img['date'] ?? '-',
                                'loc' => $img['location'] ?? '-',
                                'desc' => $img['description'] ?? '-'
                            ]), ENT_QUOTES, 'UTF-8');
                        ?>
                            <div onclick="openGalleryModal(<?= $jsonAttr ?>)" class="neo-btn bg-white dark:bg-[#1E1E1E] border-[3px] border-black dark:border-white p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] hover:scale-[1.02] transition-all cursor-pointer flex flex-col group rounded-xl">
                                <div class="relative aspect-[4/3] w-full overflow-hidden border-[3px] border-black dark:border-white bg-[#FAF6EE] dark:bg-[#121212] rounded-lg">
                                    <img src="<?= $cover ?>" alt="<?= htmlspecialchars($img['name'] ?? '') ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    <div class="absolute top-2 left-2 bg-brutal-yellow text-black text-xs font-black px-2.5 py-1 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center gap-1.5 z-20">
                                        📸 <?= $count ?> Foto
                                    </div>
                                </div>
                                <div class="pt-4 pb-1 text-left">
                                    <p class="text-black dark:text-white font-black text-lg uppercase tracking-tight truncate w-full mb-1 group-hover:text-brutal-pink transition-colors"><?= htmlspecialchars($img['name'] ?? 'Untitled') ?></p>
                                    <div class="flex justify-between items-center text-xs font-mono text-slate-700 dark:text-slate-300 mt-2">
                                        <span>📍 <?= htmlspecialchars($img['location'] ?? '-') ?></span>
                                        <span>📅 <?= htmlspecialchars($img['date'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- 4. SCHEDULE SECTION -->
        <section id="schedule" class="pt-8 scroll-reveal">
            <h2 class="text-4xl font-black text-center text-black dark:text-white mb-10 uppercase tracking-wider">
                Jadwal Kuliah
            </h2>

            <div class="overflow-x-auto bg-white dark:bg-darkcard rounded-xl border-[3px] border-black dark:border-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                <table class="w-full text-sm text-center border-collapse">
                    <thead class="text-xs text-black dark:text-white uppercase bg-[#FAF6EE] dark:bg-[#1E1E1E] border-b-[3px] border-black dark:border-white">
                        <tr>
                            <th class="px-4 py-4 font-black border-r-[3px] border-black dark:border-white w-24">Jam</th>
                            <?php foreach($days as $day): ?>
                                <th class="px-4 py-4 font-black border-r-[3px] border-black dark:border-white last:border-0"><?= $day ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y-[3px] divide-black dark:divide-white">
                        <?php foreach ($times_keys as $time): 
                            $rowDays = $scheduleMap[$time];
                        ?>
                        <tr class="hover:bg-brutal-yellow/10 transition-colors">
                            <td class="font-mono text-xs text-black dark:text-white font-extrabold border-r-[3px] border-black dark:border-white px-4 py-5 whitespace-nowrap bg-[#FAF6EE] dark:bg-[#1E1E1E]"><?= $time ?></td>
                            <?php foreach($days as $day): 
                                if (isset($skip[$time][$day]) && $skip[$time][$day]) continue;
                                $subject = trim($rowDays[$day] ?? '');
                                $span = $rowspans[$time][$day] ?? 1;
                                
                                // Neubrutalist colorful pastels
                                $colorClasses = ['bg-brutal-yellow', 'bg-brutal-green', 'bg-brutal-pink', 'bg-brutal-cyan', 'bg-brutal-purple'];
                                $colorClass = $colorClasses[strlen($subject) % count($colorClasses)];
                            ?>
                                <td class="border-r-[3px] border-black dark:border-white last:border-r-0 p-2 relative h-[70px]" rowspan="<?= $span ?>">
                                    <?php if (!empty($subject)): ?>
                                        <div class="border-[2px] border-black rounded-lg p-3 flex flex-col items-center justify-center <?= $colorClass ?> text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] absolute top-2 bottom-2 left-2 right-2 overflow-hidden group">
                                            <span class="text-black font-extrabold font-mono text-[11px] text-center w-full break-words whitespace-normal leading-relaxed uppercase"><?= htmlspecialchars($subject) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400 dark:text-slate-500 font-mono text-[10px]">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 5. PERMAINAN SECTION -->
        <section id="permainan" class="pt-16 pb-8 bg-[#FAF6EE] dark:bg-[#121212] -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 border-y-[3px] border-black dark:border-white scroll-reveal">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-4xl font-black text-center text-black dark:text-white mb-12 uppercase tracking-wider">
                    Permainan Kelas
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-8">
                    <!-- Game Card: Werewolf -->
                    <div class="neo-btn bg-white dark:bg-[#1E1E1E] rounded-xl border-[3px] border-black dark:border-white overflow-hidden shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] flex flex-col h-full group">
                        <div class="h-36 bg-brutal-purple flex items-center justify-center relative overflow-hidden border-b-[3px] border-black dark:border-white">
                            <img src="uploads/werewolf_logo.png?v=3" alt="Werewolf Logo" class="h-24 w-24 object-contain rounded-full border-[3px] border-black bg-white p-1 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-xl font-black text-black dark:text-white mb-2 uppercase">Werewolf</h3>
                            <p class="text-slate-700 dark:text-slate-300 text-sm mb-6 flex-1 font-medium">Malam yang mencekam. Lengkap dengan 24 role interaktif offline!</p>
                            <a href="warewolf.php" target="_blank" class="neo-btn inline-flex items-center justify-center w-full bg-brutal-purple text-black border-2 border-black font-black px-4 py-2.5 rounded-lg text-sm transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                Mainkan
                            </a>
                        </div>
                    </div>

                    <!-- Game Card: Truth or Dare -->
                    <div class="neo-btn bg-white dark:bg-[#1E1E1E] rounded-xl border-[3px] border-black dark:border-white overflow-hidden shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] flex flex-col h-full group">
                        <div class="h-36 bg-brutal-pink flex items-center justify-center relative overflow-hidden border-b-[3px] border-black dark:border-white">
                            <img src="uploads/truth_or_dare_logo.png?v=3" alt="Truth or Dare Logo" class="h-24 w-24 object-contain rounded-full border-[3px] border-black bg-white p-1 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-xl font-black text-black dark:text-white mb-2 uppercase">Truth or Dare</h3>
                            <p class="text-slate-700 dark:text-slate-300 text-sm mb-6 flex-1 font-medium">Jujur atau berani? Game paling seru untuk kumpul kelas!</p>
                            <a href="truth-or-dare.php" class="neo-btn inline-flex items-center justify-center w-full bg-brutal-pink text-black border-2 border-black font-black px-4 py-2.5 rounded-lg text-sm transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                Mainkan
                            </a>
                        </div>
                    </div>

                    <!-- Game Card: Tebak Kata -->
                    <div class="neo-btn bg-white dark:bg-[#1E1E1E] rounded-xl border-[3px] border-black dark:border-white overflow-hidden shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] flex flex-col h-full group">
                        <div class="h-36 bg-brutal-cyan flex items-center justify-center relative overflow-hidden border-b-[3px] border-black dark:border-white">
                            <img src="uploads/tebak_kata_logo.png?v=3" alt="Tebak Kata Logo" class="h-24 w-24 object-contain rounded-full border-[3px] border-black bg-white p-1 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-xl font-black text-black dark:text-white mb-2 uppercase">Tebak Kata</h3>
                            <p class="text-slate-700 dark:text-slate-300 text-sm mb-6 flex-1 font-medium">Uji wawasanmu tentang komponen elektronika!</p>
                            <a href="tebak-kata.php" class="neo-btn inline-flex items-center justify-center w-full bg-brutal-cyan text-black border-2 border-black font-black px-4 py-2.5 rounded-lg text-sm transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                Mainkan
                            </a>
                        </div>
                    </div>

                    <!-- Game Card: Tic Tac Toe -->
                    <div class="neo-btn bg-white dark:bg-[#1E1E1E] rounded-xl border-[3px] border-black dark:border-white overflow-hidden shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] flex flex-col h-full group">
                        <div class="h-36 bg-brutal-yellow flex items-center justify-center relative overflow-hidden border-b-[3px] border-black dark:border-white">
                            <img src="uploads/tictactoe_logo.png?v=3" alt="Tic Tac Toe Logo" class="h-24 w-24 object-contain rounded-full border-[3px] border-black bg-white p-1 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-xl font-black text-black dark:text-white mb-2 uppercase">Tic Tac Toe</h3>
                            <p class="text-slate-700 dark:text-slate-300 text-sm mb-6 flex-1 font-medium">Klasik 2 pemain. Siapa yang paling cerdik?</p>
                            <a href="tictactoe.php" class="neo-btn inline-flex items-center justify-center w-full bg-brutal-yellow text-black border-2 border-black font-black px-4 py-2.5 rounded-lg text-sm transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                Mainkan
                            </a>
                        </div>
                    </div>

                    <!-- Game Card: Undercover -->
                    <div class="neo-btn bg-white dark:bg-[#1E1E1E] rounded-xl border-[3px] border-black dark:border-white overflow-hidden shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] flex flex-col h-full group">
                        <div class="h-36 bg-brutal-green flex items-center justify-center relative overflow-hidden border-b-[3px] border-black dark:border-white">
                            <img src="uploads/undercover_logo.png?v=3" alt="Undercover Logo" class="h-24 w-24 object-contain rounded-full border-[3px] border-black bg-white p-1 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-xl font-black text-black dark:text-white mb-2 uppercase">Undercover</h3>
                            <p class="text-slate-700 dark:text-slate-300 text-sm mb-6 flex-1 font-medium">Temukan Penyusup di antaramu! Terintegrasi Gemini AI.</p>
                            <a href="undercover.php" class="neo-btn inline-flex items-center justify-center w-full bg-brutal-green text-black border-2 border-black font-black px-4 py-2.5 rounded-lg text-sm transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                Mainkan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Trivia Link -->
                <div class="mt-12 text-center">
                    <a href="trivia.php" class="neo-btn inline-flex items-center gap-3 bg-white dark:bg-[#1E1E1E] border-[3px] border-black dark:border-white px-6 py-3 rounded-xl font-black text-black dark:text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] hover:bg-brutal-cyan hover:text-black">
                        🧠 Uji Pengetahuanmu di Trivia Kelas &rarr;
                    </a>
                </div>
            </div>
        </section>

        <!-- 6. KOTAK PESAN SECTION -->
        <section id="pesan" class="pt-12 pb-24 scroll-reveal">
            <h2 class="text-4xl font-black text-center text-black dark:text-white mb-10 uppercase tracking-wider">
                Bisikan Anonim
            </h2>

            <div class="max-w-2xl mx-auto space-y-5 mb-8 max-h-[400px] overflow-y-auto pr-3" id="msg-container">
                <?php if (empty($data['messages'])): ?>
                    <p class="text-center text-black dark:text-white font-extrabold text-sm py-8 border-[3px] border-dashed border-black dark:border-white rounded-xl bg-white dark:bg-[#1E1E1E] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">Belum ada pesan anonim. Jadilah yang pertama berbisik!</p>
                <?php else: ?>
                    <?php foreach ($data['messages'] as $msg): ?>
                        <div class="bg-white dark:bg-[#1E1E1E] border-[3px] border-black dark:border-white rounded-xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] flex gap-3 group relative">
                            <div class="w-10 h-10 rounded-lg bg-brutal-cyan border-2 border-black flex items-center justify-center flex-shrink-0 font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                👤
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h4 class="font-extrabold text-sm text-black dark:text-white">Seseorang</h4>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono font-bold"><?= htmlspecialchars($msg['date']) ?></span>
                                </div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($msg['text']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="max-w-2xl mx-auto">
                <?php if (isset($_SESSION['msg_success'])): ?>
                    <div class="bg-brutal-green border-[3px] border-black text-black font-extrabold px-4 py-3 rounded-lg mb-4 text-sm text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        🎉 <?= $_SESSION['msg_success'] ?>
                        <?php unset($_SESSION['msg_success']); ?>
                    </div>
                <?php endif; ?>
                <form action="index.php" method="POST" class="relative">
                    <input type="hidden" name="action" value="send_message">
                    <input type="text" name="anon_message" required class="w-full bg-white dark:bg-[#1E1E1E] border-[3px] border-black dark:border-white rounded-xl pl-6 pr-36 py-4 text-sm text-black dark:text-white font-bold focus:outline-none placeholder-slate-700 dark:placeholder-slate-400 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]" placeholder="Ketik bisikan rahasiamu di sini...">
                    <button type="submit" class="neo-btn absolute right-3 top-2 bottom-2 bg-brutal-pink text-black border-2 border-black font-black px-6 rounded-lg transition-all text-xs flex items-center gap-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim 
                    </button>
                </form>
            </div>
        </section>

    </main>

    <footer class="border-t-[3px] border-black dark:border-white bg-[#FAF6EE] dark:bg-[#1E1E1E] py-10 text-center mt-12 transition-colors">
        <div class="w-12 h-12 bg-brutal-yellow text-black border-2 border-black rounded-lg flex items-center justify-center font-black text-xl mx-auto mb-4 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] select-none">D</div>
        <p class="text-black dark:text-white font-extrabold text-sm">&copy; 2026 Devtronix Website.</p>
        <a href="login.php" class="inline-block bg-brutal-cyan text-black border-2 border-black px-3.5 py-1.5 font-bold font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px] transition-all mt-4">⭧ Admin Dashboard</a>
    </footer>

    <!-- Gallery Fullscreen Modal -->
    <div id="galleryModal" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-md hidden opacity-0 pointer-events-none transition-opacity duration-300 flex items-center justify-center p-4" onclick="closeGalleryModal()">
        <!-- Close Button -->
        <button class="absolute top-4 right-4 text-black bg-brutal-pink border-2 border-black p-2.5 rounded-lg transition-all z-50 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Modal Content Container -->
        <div id="modalContent" class="bg-white dark:bg-[#1E1E1E] border-[4px] border-black dark:border-white rounded-2xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] max-w-5xl w-full max-h-[90vh] flex flex-col md:flex-row scale-95 opacity-0 transition-all duration-300 relative" onclick="event.stopPropagation()">
            
            <!-- Image Area -->
            <div class="w-full md:w-2/3 bg-[#FAF6EE] dark:bg-[#121212] flex items-center justify-center relative min-h-[300px] border-b-[3px] md:border-b-0 md:border-r-[3px] border-black dark:border-white group">
                <img id="modalImg" src="" class="max-w-full max-h-[50vh] md:max-h-[90vh] object-contain transition-opacity duration-300 p-4">
                
                <!-- Navigation Buttons -->
                <button id="btnPrevPhoto" onclick="prevPhoto(event)" class="absolute left-4 top-1/2 -translate-y-1/2 bg-brutal-cyan text-black border-2 border-black p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity disabled:opacity-0 disabled:cursor-not-allowed z-50 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="btnNextPhoto" onclick="nextPhoto(event)" class="absolute right-4 top-1/2 -translate-y-1/2 bg-brutal-cyan text-black border-2 border-black p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity disabled:opacity-0 disabled:cursor-not-allowed z-50 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </button>
                
                <!-- Photo Counter -->
                <div id="photoCounter" class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-brutal-yellow text-black text-xs px-3.5 py-1.5 rounded-lg border-2 border-black font-black font-mono z-50 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    1 / 1
                </div>
            </div>

            <!-- Metadata Panel -->
            <div class="w-full md:w-1/3 bg-[#FAF6EE] dark:bg-[#1E1E1E] p-6 md:p-8 flex flex-col gap-6 overflow-y-auto max-h-[45vh] md:max-h-[90vh]">
                <div>
                    <h3 id="modalTitle" class="text-2xl font-black text-black dark:text-white mb-2 leading-tight uppercase">Momen</h3>
                    <div class="w-16 h-1.5 bg-brutal-pink border border-black rounded-full"></div>
                </div>

                <div class="space-y-4">
                    <!-- Tanggal -->
                    <div class="flex items-start gap-3 bg-white dark:bg-[#121212] border-2 border-black dark:border-white p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                        <div class="bg-brutal-green p-2 border-2 border-black rounded text-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                            📅
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-0.5">Tanggal</p>
                            <p id="modalDate" class="text-sm font-black text-black dark:text-white font-mono"></p>
                        </div>
                    </div>

                    <!-- Tempat -->
                    <div class="flex items-start gap-3 bg-white dark:bg-[#121212] border-2 border-black dark:border-white p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                        <div class="bg-brutal-cyan p-2 border-2 border-black rounded text-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                            📍
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-0.5">Tempat</p>
                            <p id="modalLoc" class="text-sm font-black text-black dark:text-white capitalize"></p>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="flex items-start gap-3 bg-white dark:bg-[#121212] border-2 border-black dark:border-white p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                        <div class="bg-brutal-purple p-2 border-2 border-black rounded text-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                            📝
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-0.5">Keterangan</p>
                            <p id="modalDesc" class="text-sm font-bold text-black dark:text-white leading-relaxed"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-6 border-t-[3px] border-black dark:border-white">
                    <button onclick="closeGalleryModal()" class="neo-btn w-full bg-brutal-cyan text-black border-2 border-black py-2.5 rounded-lg text-sm font-black transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">Tutup Peninjau</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Identity Fullscreen Modal -->
    <div id="memberModal" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-md hidden opacity-0 pointer-events-none transition-opacity duration-300 flex items-center justify-center p-4" onclick="closeMemberModal()">
        <!-- Close Button -->
        <button class="absolute top-4 right-4 text-black bg-brutal-pink border-2 border-black p-2.5 rounded-lg transition-all z-[110] shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Modal Content -->
        <div id="memberModalContent" class="bg-[#FAF6EE] dark:bg-[#1E1E1E] border-[4px] border-black dark:border-white rounded-2xl overflow-hidden shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] max-w-4xl w-full flex flex-col md:flex-row scale-95 opacity-0 transition-all duration-300 relative" onclick="event.stopPropagation()">
            
            <!-- Picture Area (Left) -->
            <div class="w-full md:w-[45%] bg-[#FAF6EE] dark:bg-[#121212] relative flex items-center justify-center min-h-[300px] md:min-h-[500px] border-b-[3px] md:border-b-0 md:border-r-[3px] border-black dark:border-white">
                <img id="mModalImg" src="" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-transparent md:hidden"></div>
                <div id="mModalImgFallback" class="hidden flex-col items-center justify-center z-10 text-black dark:text-white">
                    <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="text-xs bg-brutal-pink text-black border-2 border-black px-4 py-1.5 rounded-lg font-black font-mono shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider">Tanpa Foto</span>
                </div>
            </div>

            <!-- Profile Details Panel (Right) -->
            <div class="w-full md:w-[55%] bg-[#FAF6EE] dark:bg-[#1E1E1E] p-6 md:p-10 flex flex-col overflow-y-auto max-h-[60vh] md:max-h-[90vh]">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 id="mModalName" class="text-3xl font-black text-black dark:text-white leading-tight uppercase tracking-tight">Nama</h3>
                        <p id="mModalJulukan" class="text-brutal-pink font-extrabold text-sm uppercase mt-1 tracking-wide">Anggota</p>
                    </div>
                    <div class="bg-brutal-yellow text-black border-2 border-black px-3.5 py-1 rounded-lg font-black text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] h-fit font-mono" id="mModalNim">NIM</div>
                </div>
                
                <div class="w-16 h-1.5 bg-brutal-cyan border border-black rounded-full mb-8"></div>

                <div class="space-y-6 flex-1">
                    <!-- Asal & Hobi Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-brutal-cyan text-black border-2 border-black rounded-lg p-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <p class="text-[10px] uppercase font-black text-slate-700 tracking-wider mb-1">Asal Daerah</p>
                            <p id="mModalAsal" class="font-extrabold text-sm"></p>
                        </div>
                        <div class="bg-brutal-green text-black border-2 border-black rounded-lg p-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <p class="text-[10px] uppercase font-black text-slate-700 tracking-wider mb-1">Hobi</p>
                            <p id="mModalHobi" class="font-extrabold text-sm"></p>
                        </div>
                    </div>

                    <!-- Motto -->
                    <div class="bg-white dark:bg-[#1E1E1E] border-[3px] border-black dark:border-white rounded-xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] relative overflow-hidden group">
                        <svg class="absolute -top-2 -left-2 w-12 h-12 text-slate-300 dark:text-slate-700 opacity-40" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"></path></svg>
                        <p id="mModalMotto" class="text-sm font-extrabold text-black dark:text-white italic relative z-10 pl-6 leading-relaxed"></p>
                    </div>

                    <!-- Social Media Links -->
                    <div class="pt-4 border-t-[3px] border-black dark:border-white">
                        <p class="text-[10px] uppercase font-black text-slate-500 dark:text-slate-400 tracking-wider mb-4">Terhubung</p>
                        <div class="flex gap-4">
                            <!-- LinkedIn -->
                            <a id="btnLinkedin" href="#" target="_blank" class="hidden flex-1 flex-col items-center justify-center gap-1.5 bg-[#0077b5] text-white border-2 border-black p-3 rounded-lg text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-1px] transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                LinkedIn
                            </a>
                            <!-- Instagram -->
                            <a id="btnInstagram" href="#" target="_blank" class="hidden flex-1 flex-col items-center justify-center gap-1.5 bg-brutal-pink text-black border-2 border-black p-3 rounded-lg text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-1px] transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                Instagram
                            </a>
                            <!-- WhatsApp -->
                            <a id="btnWhatsapp" href="#" target="_blank" class="hidden flex-1 flex-col items-center justify-center gap-1.5 bg-brutal-green text-black border-2 border-black p-3 rounded-lg text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-1px] transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.527 1.059 3.597l-.875 3.197 3.273-.86c1.026.604 2.187.92 3.393.92h.001c3.182 0 5.768-2.587 5.768-5.767 0-3.181-2.586-5.768-5.768-5.768v-.001zm3.178 8.243c-.174.492-.98.922-1.393.961-.318.03-1.024.168-3.328-1.503-2.195-1.597-3.667-3.871-3.771-4.008-.103-.137-.905-1.206-.905-2.296s.562-1.623.76-1.831c.197-.208.428-.261.571-.261s.286-.004.41-.004c.125-.001.296-.048.463.356.174.423.594 1.45.648 1.558.053.11.088.239.019.377-.07.139-.105.227-.208.351-.105.122-.224.275-.316.368-.103.104-.213.218-.096.421.117.202.52 1.837 1.045 2.298.679.595 1.303.778 1.488.871.185.094.293.078.403-.047.11-.125.474-.551.599-.74.125-.19.25-.157.424-.092.174.065 1.102.52 1.291.614.188.094.316.14.362.22.047.078.047.458-.127.951zM11.95 2C6.455 2 2.001 6.454 2.001 11.95c0 1.859.488 3.659 1.416 5.25L2 22l4.908-1.282A9.917 9.917 0 0011.95 21.95C17.444 21.95 21.9 17.495 21.9 12c0-5.495-4.455-9.95-9.95-9.95z"/></svg>
                                Chat WA
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showMembers(id) {
            document.querySelectorAll('.member-grid').forEach(grid => grid.classList.remove('active'));
            document.querySelectorAll('.member-tab').forEach(btn => {
                btn.className = "neo-btn member-tab px-6 py-2.5 rounded-lg text-sm font-extrabold text-black border-2 border-black dark:border-white bg-[#FAF6EE] dark:bg-[#1E1E1E] dark:text-white transition-all flex items-center justify-center gap-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]";
            });
            
            document.getElementById('grid-' + id).classList.add('active');
            let activeBg = 'bg-brutal-yellow';
            if (id === '14') activeBg = 'bg-brutal-cyan';
            if (id === '11') activeBg = 'bg-brutal-pink';
            
            document.getElementById('btn-' + id).className = `neo-btn member-tab px-6 py-2.5 rounded-lg text-sm font-extrabold text-black border-2 border-black ${activeBg} transition-all flex items-center justify-center gap-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]`;
        }
        
        const msgContainer = document.getElementById('msg-container');
        if(msgContainer) {
            msgContainer.scrollTop = msgContainer.scrollHeight;
        }

        // Gallery Modal Logic
        const modal = document.getElementById('galleryModal');
        const modalContent = document.getElementById('modalContent');
        let currentPhotos = [];
        let currentPhotoIndex = 0;
        
        function updateModalPhoto() {
            if (currentPhotos.length === 0) return;
            const imgEl = document.getElementById('modalImg');
            imgEl.style.opacity = '0';
            setTimeout(() => {
                imgEl.src = currentPhotos[currentPhotoIndex];
                imgEl.style.opacity = '1';
            }, 150);
            
            document.getElementById('photoCounter').textContent = `${currentPhotoIndex + 1} / ${currentPhotos.length}`;
            
            document.getElementById('btnPrevPhoto').disabled = currentPhotoIndex === 0;
            document.getElementById('btnNextPhoto').disabled = currentPhotoIndex === currentPhotos.length - 1;
            
            if (currentPhotos.length <= 1) {
                document.getElementById('photoCounter').style.display = 'none';
                document.getElementById('btnPrevPhoto').style.display = 'none';
                document.getElementById('btnNextPhoto').style.display = 'none';
            } else {
                document.getElementById('photoCounter').style.display = 'block';
                document.getElementById('btnPrevPhoto').style.display = 'block';
                document.getElementById('btnNextPhoto').style.display = 'block';
            }
        }

        function nextPhoto(e) {
            e.stopPropagation();
            if (currentPhotoIndex < currentPhotos.length - 1) {
                currentPhotoIndex++;
                updateModalPhoto();
            }
        }

        function prevPhoto(e) {
            e.stopPropagation();
            if (currentPhotoIndex > 0) {
                currentPhotoIndex--;
                updateModalPhoto();
            }
        }

        function openGalleryModal(data) {
            currentPhotos = data.photos || [];
            currentPhotoIndex = 0;
            
            updateModalPhoto();
            
            document.getElementById('modalTitle').textContent = data.name;
            document.getElementById('modalDate').textContent = data.date;
            document.getElementById('modalLoc').textContent = data.loc;
            document.getElementById('modalDesc').textContent = data.desc;
            
            modal.classList.remove('hidden');
            // Small delay to allow display:block to apply before animating opacity
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modalContent.classList.remove('scale-95', 'opacity-0');
            }, 10);
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeGalleryModal() {
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300); // Wait for transition
            document.body.style.overflow = '';
        }

        // Member Profile Logic
        const memberModal = document.getElementById('memberModal');
        const memberModalContent = document.getElementById('memberModalContent');
        
        function openMemberModal(data) {
            // Picture
            const imgEl = document.getElementById('mModalImg');
            const fallbackEl = document.getElementById('mModalImgFallback');
            if (data.image) {
                imgEl.src = data.image;
                imgEl.classList.remove('hidden');
                fallbackEl.classList.add('hidden', 'flex');
            } else {
                imgEl.classList.add('hidden');
                fallbackEl.classList.remove('hidden');
                fallbackEl.classList.add('flex');
            }

            // Text Fields
            document.getElementById('mModalName').textContent = data.name;
            document.getElementById('mModalJulukan').textContent = data.julukan || 'Anggota';
            document.getElementById('mModalNim').textContent = data.nim ? data.nim : '...';
            document.getElementById('mModalAsal').textContent = data.asal;
            document.getElementById('mModalHobi').textContent = data.hobi;
            document.getElementById('mModalMotto').textContent = data.motto ? `"${data.motto}"` : "-";

            // Social Links (Conditional Display)
            const lnk = document.getElementById('btnLinkedin');
            const ig = document.getElementById('btnInstagram');
            const wa = document.getElementById('btnWhatsapp');

            lnk.classList.toggle('hidden', !data.linkedin);
            if(data.linkedin) lnk.href = data.linkedin;

            ig.classList.toggle('hidden', !data.instagram);
            if(data.instagram) ig.href = `https://instagram.com/${data.instagram}`;

            wa.classList.toggle('hidden', !data.whatsapp);
            if(data.whatsapp) {
                // Ensure WHATSAPP number format
                let phone = data.whatsapp.replace(/\D/g, ''); 
                if(phone.startsWith('0')) phone = '62' + phone.slice(1);
                wa.href = `https://wa.me/${phone}`;
            }

            // Animate In
            memberModal.classList.remove('hidden');
            setTimeout(() => {
                memberModal.classList.remove('opacity-0', 'pointer-events-none');
                memberModalContent.classList.remove('scale-95', 'opacity-0');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeMemberModal() {
            memberModal.classList.add('opacity-0', 'pointer-events-none');
            memberModalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                memberModal.classList.add('hidden');
            }, 300);
            document.body.style.overflow = '';
        }

    </script>
    
    <!-- Scroll Reveal Animation Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                    } else {
                        entry.target.classList.remove('is-revealed');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.scroll-reveal').forEach((el) => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
