<?php
/**
 * DEVTRONIX ARCADE PORTAL - UNDERCOVER PARTY GAME
 * File: undercover.php
 * Description: Sleek, responsive, single-device neubrutalist party game with local roster integration and Gemini AI custom generation.
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undercover - Devtronix Arcade</title>
    <link rel="icon" type="image/jpeg" href="uploads/kelas_logo.jpg?v=3">
    <!-- Space Grotesk Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700;900&display=swap" rel="stylesheet">
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
        .neo-btn {
            transition: all 0.15s cubic-bezier(0, 0, 0, 1);
        }
        .neo-btn:hover {
            transform: translate(-3px, -3px);
        }
        .neo-btn:active {
            transform: translate(1px, 1px);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #FAF6EE;
            border-left: 2px solid #000;
        }
        ::-webkit-scrollbar-thumb {
            background: #000;
            border-radius: 4px;
        }
    </style>
</head>
<body class="text-black antialiased min-h-screen flex flex-col p-4 md:p-8 selection:bg-brutal-yellow">

    <!-- STICKY BRUTALIST NAVBAR -->
    <nav class="bg-white border-[3px] border-black p-4 flex justify-between items-center sticky top-4 z-50 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] max-w-6xl w-full mx-auto mb-10">
        <div class="flex items-center gap-3 font-black text-xl text-black uppercase tracking-tight">
            <img src="uploads/undercover_logo.png?v=3" alt="Logo" class="w-8 h-8 rounded-full border-2 border-black shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] bg-white p-0.5"> 
            UNDERCOVER DEVTRONIX
        </div>
        <div class="flex gap-2">
            <button onclick="openConfigModal()" class="neo-btn bg-brutal-yellow text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] cursor-pointer">
                PENGATURAN AI
            </button>
            <a href="index.php" class="neo-btn bg-brutal-cyan text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                ⭠ KEMBALI
            </a>
        </div>
    </nav>

    <!-- MAIN GAME AREA -->
    <main class="max-w-4xl w-full mx-auto flex-1 flex flex-col items-center justify-center">

        <!-- ============================================================
             SCREEN 1: SETUP
             ============================================================ -->
        <div id="screen-setup" class="w-full fade-up">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Left Panel: Roster & Names -->
                <div class="md:col-span-2 bg-white border-[3px] border-black rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                        <h2 class="font-black text-2xl uppercase tracking-tight flex items-center gap-2">
                            👥 Pemain Aktif <span id="setup-count-badge" class="bg-brutal-pink text-white text-xs px-2.5 py-1 rounded-full border-2 border-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] font-mono">0</span>
                        </h2>
                        <button type="button" onclick="resetRosterToClass()" class="neo-btn bg-brutal-yellow text-black border-2 border-black font-black px-3 py-1.5 rounded-lg text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] cursor-pointer">
                            Reset Roster Kelas (23)
                        </button>
                    </div>
                    
                    <!-- Player Grid -->
                    <div id="setup-player-list" class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-[350px] overflow-y-auto p-1 mb-6 border-2 border-black border-dashed rounded-xl bg-[#FAF6EE]">
                        <!-- Dynamic list -->
                    </div>

                    <!-- Add Player Form -->
                    <form id="add-player-form" onsubmit="addPlayer(event)" class="flex gap-3">
                        <input type="text" id="new-player-name" placeholder="Ketik nama baru..." maxlength="15" autocomplete="off" class="flex-1 bg-white border-[3px] border-black px-4 py-2.5 rounded-xl font-bold placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brutal-purple shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <button type="submit" class="neo-btn bg-brutal-green text-black border-[3px] border-black font-black px-6 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer">
                            ➕ TAMBAH
                        </button>
                    </form>
                </div>

                <!-- Right Panel: Configurations & Mode -->
                <div class="flex flex-col gap-6">
                    
                    <!-- Game Config Card -->
                    <div class="bg-white border-[3px] border-black rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                        <h2 class="font-black text-xl uppercase tracking-tight mb-4">🎮 Aturan Peran</h2>
                        
                        <!-- Undercover Count -->
                        <div class="mb-4">
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">🕵️ Undercover</label>
                            <div class="flex items-center gap-2">
                                <button onclick="adjustConfig('undercovers', -1)" class="neo-btn h-10 w-10 bg-white border-2 border-black font-black text-lg rounded-lg shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] cursor-pointer">-</button>
                                <span id="val-undercovers" class="flex-1 text-center font-black font-mono text-xl border-2 border-black bg-brutal-cream py-1 rounded-lg">1</span>
                                <button onclick="adjustConfig('undercovers', 1)" class="neo-btn h-10 w-10 bg-white border-2 border-black font-black text-lg rounded-lg shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] cursor-pointer">+</button>
                            </div>
                        </div>

                        <!-- Mr. White Count -->
                        <div class="mb-2">
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">🔲 Mr. White</label>
                            <div class="flex items-center gap-2">
                                <button onclick="adjustConfig('whites', -1)" class="neo-btn h-10 w-10 bg-white border-2 border-black font-black text-lg rounded-lg shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] cursor-pointer">-</button>
                                <span id="val-whites" class="flex-1 text-center font-black font-mono text-xl border-2 border-black bg-brutal-cream py-1 rounded-lg">0</span>
                                <button onclick="adjustConfig('whites', 1)" class="neo-btn h-10 w-10 bg-white border-2 border-black font-black text-lg rounded-lg shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] cursor-pointer">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Word Settings Card -->
                    <div class="bg-white border-[3px] border-black rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex-1 flex flex-col">
                        <h2 class="font-black text-xl uppercase tracking-tight mb-4">🏷️ Pilihan Kata</h2>
                        
                        <!-- Tabs -->
                        <div class="grid grid-cols-3 gap-1.5 mb-4 bg-slate-100 p-1 border-2 border-black rounded-xl">
                            <button onclick="setWordMode('offline')" id="btn-mode-offline" class="py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-transparent select-none cursor-pointer">Offline</button>
                            <button onclick="setWordMode('custom')" id="btn-mode-custom" class="py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-transparent select-none cursor-pointer">Custom</button>
                            <button onclick="setWordMode('ai')" id="btn-mode-ai" class="py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-transparent select-none cursor-pointer">AI</button>
                        </div>

                        <!-- Tab Content: Offline -->
                        <div id="pane-mode-offline" class="flex-1 flex flex-col justify-between">
                            <p class="text-xs text-slate-700 font-bold mb-3">Pilih tema kata acak dari 160+ pasangan kata bawaan yang langsung siap dimainkan.</p>
                            <select id="select-category" class="w-full bg-white border-2 border-black p-2 rounded-lg font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:outline-none mb-4">
                                <option value="Semua">🌟 Semua Tema (Acak)</option>
                                <option value="Makanan & Minuman">🍔 Makanan & Minuman</option>
                                <option value="Hewan">🐶 Hewan</option>
                                <option value="Sehari-hari">🛋️ Kehidupan Sehari-hari</option>
                                <option value="Teknologi">💻 Teknologi & Gadget</option>
                                <option value="Sekolah & Kerja">📚 Sekolah & Kantor</option>
                                <option value="Transportasi">🚗 Transportasi</option>
                                <option value="Hiburan">🎸 Hobi & Hiburan</option>
                                <option value="Konsep & Sains">🔬 Konsep & Sains</option>
                            </select>
                        </div>

                        <!-- Tab Content: Custom -->
                        <div id="pane-mode-custom" class="hidden flex-1 flex flex-col gap-2">
                            <input type="text" id="cust-category" placeholder="Kategori (contoh: Buah)" class="w-full bg-white border-2 border-black px-3 py-1.5 rounded-lg text-xs font-bold focus:outline-none">
                            <input type="text" id="cust-citizen" placeholder="Kata Warga (Citizen)" class="w-full bg-white border-2 border-black px-3 py-1.5 rounded-lg text-xs font-bold focus:outline-none">
                            <input type="text" id="cust-undercover" placeholder="Kata Penyusup (Undercover)" class="w-full bg-white border-2 border-black px-3 py-1.5 rounded-lg text-xs font-bold focus:outline-none">
                        </div>

                        <!-- Tab Content: AI -->
                        <div id="pane-mode-ai" class="hidden flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] text-slate-700 font-bold mb-2">Gemini AI akan membuatkan sepasang kata bahasa Indonesia secara instan berdasarkan tema!</p>
                                <input type="text" id="ai-category-input" placeholder="Tema Bebas (Contoh: Anime Jadul)" class="w-full bg-white border-2 border-black px-3 py-2 rounded-lg text-xs font-black focus:outline-none shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] mb-3">
                            </div>
                            <div id="ai-setup-warning" class="hidden p-2 bg-brutal-pink/10 border-2 border-brutal-pink rounded-lg text-[10px] font-black text-brutal-pink mb-3 text-center">
                                ⚠️ Gemini API Key belum dipasang. Klik tombol "⚙️ PENGATURAN AI" di navbar!
                            </div>
                        </div>

                        <!-- Play Button -->
                        <button onclick="startGame()" class="w-full neo-btn bg-brutal-purple text-white border-[3px] border-black font-black py-3 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider text-sm mt-4 cursor-pointer">
                            🚀 MULAI GAME
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ============================================================
             SCREEN 2: REVEAL PHASE
             ============================================================ -->
        <div id="screen-reveal" class="hidden max-w-md w-full fade-up">
            <div class="bg-white border-[3px] border-black rounded-2xl p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-3 bg-brutal-purple border-b-2 border-black"></div>
                
                <span id="rev-badge" class="inline-block bg-brutal-yellow border-2 border-black px-4 py-1.5 rounded-full font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider mb-6 mt-2">
                    Pemain 1 dari 5
                </span>
                
                <h2 id="rev-player-name" class="font-black text-4xl text-black uppercase mb-8 tracking-tight">Nova</h2>
                
                <!-- Secret Card Display Box -->
                <div id="rev-card-box" class="mb-8">
                    <!-- Dynamic pass and view content -->
                </div>

                <div id="rev-action-container">
                    <!-- Dynamic button action -->
                </div>
            </div>
        </div>

        <!-- ============================================================
             SCREEN 3: PLAYING BOARD
             ============================================================ -->
        <div id="screen-play" class="hidden w-full fade-up">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Left Panel: Players Board -->
                <div class="md:col-span-2 bg-white border-[3px] border-black rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="font-black text-2xl uppercase tracking-tight">👥 Papan Permainan</h2>
                        <div class="bg-brutal-yellow border-2 border-black px-4 py-1.5 rounded-lg font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wide">
                            Kategori: <span id="play-category-name" class="text-brutal-pink">Makanan</span>
                        </div>
                    </div>

                    <!-- Instructions and Timer -->
                    <div class="bg-brutal-cream border-2 border-black p-4 rounded-xl mb-6 shadow-[2.5px_2.5px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs font-bold text-slate-800 leading-relaxed text-center sm:text-left">
                            🎙️ <span class="font-black text-black">Mulai Giliran Deskripsi!</span> Setiap pemain secara bergiliran menjelaskan kata mereka dalam satu kata kunci/kalimat singkat tanpa membongkar kata tersebut!
                        </div>
                        
                        <!-- Neubrutalist Stopwatch Timer -->
                        <div class="flex items-center gap-3 bg-white border-2 border-black px-4 py-2 rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] min-w-[140px] justify-center">
                            <span class="text-lg">⏱️</span>
                            <span id="timer-display" class="font-mono font-black text-xl text-black">30s</span>
                            <button id="timer-control-btn" onclick="toggleTimer()" class="neo-btn bg-brutal-green text-black border border-black p-1 text-[10px] font-black rounded cursor-pointer">START</button>
                        </div>
                    </div>

                    <!-- Active Players Grid -->
                    <div id="play-player-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <!-- Dynamic Player Cards -->
                    </div>
                </div>

                <!-- Right Panel: Discussion, Logs & Reset -->
                <div class="flex flex-col gap-6">
                    <!-- Target Roster Rules -->
                    <div class="bg-white border-[3px] border-black rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                        <h3 class="font-black text-lg uppercase tracking-tight mb-3">🎲 Aturan Voting</h3>
                        <p class="text-xs text-slate-800 font-bold leading-relaxed mb-4">
                            Setelah semua pemain selesai mendeskripsikan kata, diskusikan bersama siapa yang gerak-geriknya paling mencurigakan sebagai **Undercover** atau **Mr. White**.
                        </p>
                        <div class="p-3 bg-brutal-pink/10 border-2 border-brutal-pink rounded-xl text-xs font-black text-brutal-pink leading-relaxed">
                            💡 Ketuk tombol <span class="bg-brutal-pink text-white px-2 py-0.5 rounded border border-black shadow-[0.5px_0.5px_0px_0px_rgba(0,0,0,1)]">ELIMINASI</span> pada kartu pemain untuk mendepak orang tersebut dari game!
                        </div>
                    </div>

                    <!-- Game Event Log -->
                    <div class="bg-white border-[3px] border-black rounded-2xl p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex-1 flex flex-col">
                        <h3 class="font-black text-lg uppercase tracking-tight mb-3">📜 Catatan Permainan</h3>
                        <div id="play-event-logs" class="flex-1 max-h-[220px] overflow-y-auto border-2 border-black border-dashed rounded-xl bg-brutal-cream p-3 font-mono text-[10px] leading-relaxed flex flex-col gap-2">
                            <div>💬 Permainan dimulai! Lakukan deskripsi berputar.</div>
                        </div>
                        <button onclick="abortGame()" class="w-full neo-btn bg-brutal-pink text-white border-2 border-black font-black py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider text-xs mt-4 cursor-pointer">
                            🛑 AKHIRI & BATALKAN GAME
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             SCREEN 4: VICTORY SCREEN
             ============================================================ -->
        <div id="screen-victory" class="hidden max-w-xl w-full fade-up">
            <div class="bg-white border-[3px] border-black rounded-2xl p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-center relative overflow-hidden">
                <!-- Top banner colored -->
                <div id="victory-header-stripe" class="absolute top-0 left-0 w-full h-4 bg-brutal-green border-b-2 border-black"></div>
                
                <div id="victory-emoji" class="text-7xl mb-4 mt-4 filter drop-shadow-[2.5px_2.5px_0px_#000]">🏆</div>
                <h1 id="victory-title" class="font-black text-4xl text-black uppercase mb-2 tracking-tight">Warga Menang!</h1>
                <p id="victory-desc" class="text-sm font-bold text-slate-800 mb-6">Seluruh penyusup dan Mr. White berhasil dibongkar!</p>
                
                <!-- Words Reveal Neubrutalist Card -->
                <div class="bg-brutal-cream border-3 border-black p-5 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] mb-8 text-left flex flex-col gap-3">
                    <div class="flex items-center justify-between border-b-2 border-black border-dashed pb-2">
                        <span class="text-xs font-black uppercase text-slate-700">Kata Warga (Citizen)</span>
                        <span id="vic-word-citizen" class="bg-brutal-green border border-black font-black px-3 py-1 rounded text-xs shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">Kucing</span>
                    </div>
                    <div class="flex items-center justify-between border-b-2 border-black border-dashed pb-2">
                        <span class="text-xs font-black uppercase text-slate-700">Kata Penyusup (Undercover)</span>
                        <span id="vic-word-undercover" class="bg-brutal-pink text-white border border-black font-black px-3 py-1 rounded text-xs shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">Anjing</span>
                    </div>
                    <div id="vic-row-whites" class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase text-slate-700">Mr. White</span>
                        <span class="bg-brutal-purple text-white border border-black font-black px-3 py-1 rounded text-xs shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">Tidak Tahu Kata</span>
                    </div>
                </div>

                <!-- Roles Summary List -->
                <div class="mb-8 text-left">
                    <h3 class="font-black text-sm uppercase tracking-wider text-black mb-3">📋 Rincian Peran Semua Pemain:</h3>
                    <div id="victory-roster-list" class="grid grid-cols-2 gap-3 max-h-[200px] overflow-y-auto border-2 border-black p-3 bg-[#FAF6EE] rounded-xl">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Reset Button -->
                <button onclick="resetGame()" class="neo-btn inline-flex items-center gap-2 bg-brutal-green text-black border-[3px] border-black font-black px-8 py-3.5 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider text-sm cursor-pointer">
                    🔄 MAIN LAGI &rarr;
                </button>
            </div>
        </div>

    </main>

    <!-- CONFIGURATION MODAL (API KEY SETUP) -->
    <div id="config-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-6 animate-fade">
        <div class="bg-white border-[4px] border-black p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] max-w-md w-full relative">
            <h2 class="text-2xl font-black mb-2 uppercase tracking-tight flex items-center gap-2">Pengaturan AI Gemini</h2>
            <p class="text-xs text-slate-700 font-bold mb-6">Masukkan Google Gemini API Key Anda. API Key disimpan aman secara lokal pada browser Anda dan tidak pernah dikirim ke server pihak ketiga mana pun.</p>
            
            <div class="mb-6">
                <label class="block text-xs font-black uppercase tracking-wider text-black mb-2">Google Gemini API Key</label>
                <input type="password" id="api-key-input" placeholder="AIzaSy..." class="w-full bg-white border-3 border-black px-4 py-3 rounded-xl font-bold placeholder-slate-400 focus:outline-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
            </div>

            <div class="flex gap-4">
                <button onclick="closeConfigModal()" class="flex-1 neo-btn bg-white border-2 border-black font-black py-2.5 rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] text-xs uppercase cursor-pointer">
                    Batal
                </button>
                <button onclick="saveConfig()" class="flex-1 neo-btn bg-brutal-green text-black border-2 border-black font-black py-2.5 rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] text-xs uppercase cursor-pointer">
                    💾 Simpan Key
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================
         GAME ENGINE (JAVASCRIPT)
         ============================================================ -->
    <script>
        // Massive Indonesian word pairs database (160+ unique balanced pairs)
        const OFFLINE_WORD_PAIRS = [
            // HEWAN (20 pairs)
            { category: "Hewan", citizen: "kucing", undercover: "anjing" },
            { category: "Hewan", citizen: "singa", undercover: "harimau" },
            { category: "Hewan", citizen: "bebek", undercover: "angsa" },
            { category: "Hewan", citizen: "kelinci", undercover: "hamster" },
            { category: "Hewan", citizen: "kodok", undercover: "katak" },
            { category: "Hewan", citizen: "lumba-lumba", undercover: "paus" },
            { category: "Hewan", citizen: "lebah", undercover: "tawon" },
            { category: "Hewan", citizen: "kuda", undercover: "keledai" },
            { category: "Hewan", citizen: "buaya", undercover: "aligator" },
            { category: "Hewan", citizen: "kambing", undercover: "domba" },
            { category: "Hewan", citizen: "semut", undercover: "rayap" },
            { category: "Hewan", citizen: "kura-kura", undercover: "penyu" },
            { category: "Hewan", citizen: "ulat", undercover: "cacing" },
            { category: "Hewan", citizen: "elang", undercover: "burung hantu" },
            { category: "Hewan", citizen: "nyamuk", undercover: "lalat" },
            { category: "Hewan", citizen: "sapi", undercover: "kerbau" },
            { category: "Hewan", citizen: "ayam", undercover: "bebek" },
            { category: "Hewan", citizen: "kera", undercover: "simpanse" },
            { category: "Hewan", citizen: "tupai", undercover: "koala" },
            { category: "Hewan", citizen: "gurita", undercover: "cumi-cumi" },
            
            // MAKANAN & MINUMAN (30 pairs)
            { category: "Makanan & Minuman", citizen: "es krim", undercover: "gelato" },
            { category: "Makanan & Minuman", citizen: "bakso", undercover: "mie ayam" },
            { category: "Makanan & Minuman", citizen: "coto makassar", undercover: "sop konro" },
            { category: "Makanan & Minuman", citizen: "martabak manis", undercover: "terang bulan" },
            { category: "Makanan & Minuman", citizen: "gado-gado", undercover: "lotek" },
            { category: "Makanan & Minuman", citizen: "kopi", undercover: "teh" },
            { category: "Makanan & Minuman", citizen: "rendang", undercover: "dendeng" },
            { category: "Makanan & Minuman", citizen: "nasi goreng", undercover: "bubur ayam" },
            { category: "Makanan & Minuman", citizen: "tempe", undercover: "tahu" },
            { category: "Makanan & Minuman", citizen: "sate ayam", undercover: "sate kambing" },
            { category: "Makanan & Minuman", citizen: "pempek", undercover: "otak-otak" },
            { category: "Makanan & Minuman", citizen: "burger", undercover: "sandwich" },
            { category: "Makanan & Minuman", citizen: "pizza", undercover: "calzone" },
            { category: "Makanan & Minuman", citizen: "donat", undercover: "roti sobek" },
            { category: "Makanan & Minuman", citizen: "suku", undercover: "yoghurt" },
            { category: "Makanan & Minuman", citizen: "jus jeruk", undercover: "lemonade" },
            { category: "Makanan & Minuman", citizen: "pepsi", undercover: "coca-cola" },
            { category: "Makanan & Minuman", citizen: "nasi uduk", undercover: "nasi kuning" },
            { category: "Makanan & Minuman", citizen: "kfc", undercover: "mcdonalds" },
            { category: "Makanan & Minuman", citizen: "indomie", undercover: "sarimi" },
            { category: "Makanan & Minuman", citizen: "keju", undercover: "mentega" },
            { category: "Makanan & Minuman", citizen: "siomay", undercover: "batagor" },
            { category: "Makanan & Minuman", citizen: "martabak telur", undercover: "lumpia" },
            { category: "Makanan & Minuman", citizen: "klepon", undercover: "onde-onde" },
            { category: "Makanan & Minuman", citizen: "madu", undercover: "sirup" },
            { category: "Makanan & Minuman", citizen: "susu cokelat", undercover: "milo" },
            { category: "Makanan & Minuman", citizen: "keripik singkong", undercover: "keripik kentang" },
            { category: "Makanan & Minuman", citizen: "sambal terasi", undercover: "sambal tomat" },
            { category: "Makanan & Minuman", citizen: "semangka", undercover: "melon" },
            { category: "Makanan & Minuman", citizen: "apel", undercover: "pir" },
            
            // TEKNOLOGI (25 pairs)
            { category: "Teknologi", citizen: "laptop", undercover: "komputer" },
            { category: "Teknologi", citizen: "android", undercover: "iphone" },
            { category: "Teknologi", citizen: "instagram", undercover: "tiktok" },
            { category: "Teknologi", citizen: "whatsapp", undercover: "telegram" },
            { category: "Teknologi", citizen: "chrome", undercover: "firefox" },
            { category: "Teknologi", citizen: "keyboard", undercover: "mouse" },
            { category: "Teknologi", citizen: "monitor", undercover: "tv" },
            { category: "Teknologi", citizen: "flashdisk", undercover: "harddisk" },
            { category: "Teknologi", citizen: "wi-fi", undercover: "kuota data" },
            { category: "Teknologi", citizen: "headphone", undercover: "tws" },
            { category: "Teknologi", citizen: "netflix", undercover: "disney+" },
            { category: "Teknologi", citizen: "google", undercover: "yahoo" },
            { category: "Teknologi", citizen: "excel", undercover: "google sheets" },
            { category: "Teknologi", citizen: "spotify", undercover: "youtube music" },
            { category: "Teknologi", citizen: "playstation", undercover: "xbox" },
            { category: "Teknologi", citizen: "ipad", undercover: "tablet" },
            { category: "Teknologi", citizen: "zoom", undercover: "google meet" },
            { category: "Teknologi", citizen: "facebook", undercover: "twitter" },
            { category: "Teknologi", citizen: "cctv", undercover: "kamera" },
            { category: "Teknologi", citizen: "macbook", undercover: "thinkpad" },
            { category: "Teknologi", citizen: "gopay", undercover: "ovo" },
            { category: "Teknologi", citizen: "tokopedia", undercover: "shopee" },
            { category: "Teknologi", citizen: "gojek", undercover: "grab" },
            { category: "Teknologi", citizen: "line", undercover: "whatsapp" },
            { category: "Teknologi", citizen: "powerbank", undercover: "charger" },
            
            // KEHIDUPAN SEHARI-HARI (30 pairs)
            { category: "Sehari-hari", citizen: "bantal", undercover: "guling" },
            { category: "Sehari-hari", citizen: "sepatu", undercover: "sandal" },
            { category: "Sehari-hari", citizen: "helm", undercover: "topi" },
            { category: "Sehari-hari", citizen: "kasur", undercover: "tikar" },
            { category: "Sehari-hari", citizen: "kipas angin", undercover: "ac" },
            { category: "Sehari-hari", citizen: "dompet", undercover: "tas" },
            { category: "Sehari-hari", citizen: "cermin", undercover: "kaca" },
            { category: "Sehari-hari", citizen: "piring", undercover: "mangkuk" },
            { category: "Sehari-hari", citizen: "garpu", undercover: "sendok" },
            { category: "Sehari-hari", citizen: "sapu", undercover: "pel" },
            { category: "Sehari-hari", citizen: "lampu", undercover: "senter" },
            { category: "Sehari-hari", citizen: "sabun cair", undercover: "sabun batang" },
            { category: "Sehari-hari", citizen: "payung", undercover: "jas hujan" },
            { category: "Sehari-hari", citizen: "jam tangan", undercover: "jam dinding" },
            { category: "Sehari-hari", citizen: "gunting", undercover: "pisau" },
            { category: "Sehari-hari", citizen: "celana", undercover: "rok" },
            { category: "Sehari-hari", citizen: "kemeja", undercover: "kaos" },
            { category: "Sehari-hari", citizen: "jaket", undercover: "hoodie" },
            { category: "Sehari-hari", citizen: "handuk", undercover: "keset" },
            { category: "Sehari-hari", citizen: "pintu", undercover: "jendela" },
            { category: "Sehari-hari", citizen: "pagar", undercover: "tembok" },
            { category: "Sehari-hari", citizen: "kunci", undercover: "gembok" },
            { category: "Sehari-hari", citizen: "tangga", undercover: "lift" },
            { category: "Sehari-hari", citizen: "popok", undercover: "pembalut" },
            { category: "Sehari-hari", citizen: "rak buku", undercover: "lemari" },
            { category: "Sehari-hari", citizen: "tikar", undercover: "karpet" },
            { category: "Sehari-hari", citizen: "jemuran", undercover: "gantungan baju" },
            { category: "Sehari-hari", citizen: "kompor gas", undercover: "oven" },
            { category: "Sehari-hari", citizen: "setrika", undercover: "hair dryer" },
            { category: "Sehari-hari", citizen: "sisir", undercover: "sikat" },
            
            // SEKOLAH & KERJA (20 pairs)
            { category: "Sekolah & Kerja", citizen: "pulpen", undercover: "pensil" },
            { category: "Sekolah & Kerja", citizen: "buku tulis", undercover: "binder" },
            { category: "Sekolah & Kerja", citizen: "penghapus", undercover: "tip-ex" },
            { category: "Sekolah & Kerja", citizen: "penggaris", undercover: "busur" },
            { category: "Sekolah & Kerja", citizen: "ransel", undercover: "tas selempang" },
            { category: "Sekolah & Kerja", citizen: "guru", undercover: "dosen" },
            { category: "Sekolah & Kerja", citizen: "pr", undercover: "ujian" },
            { category: "Sekolah & Kerja", citizen: "rapat", undercover: "seminar" },
            { category: "Sekolah & Kerja", citizen: "gaji", undercover: "bonus" },
            { category: "Sekolah & Kerja", citizen: "seragam", undercover: "batik" },
            { category: "Sekolah & Kerja", citizen: "spidol", undercover: "kapur" },
            { category: "Sekolah & Kerja", citizen: "raport", undercover: "ijazah" },
            { category: "Sekolah & Kerja", citizen: "kantor", undercover: "pabrik" },
            { category: "Sekolah & Kerja", citizen: "magang", undercover: "kontrak" },
            { category: "Sekolah & Kerja", citizen: "meja belajar", undercover: "meja kantor" },
            { category: "Sekolah & Kerja", citizen: "upacara", undercover: "apel pagi" },
            { category: "Sekolah & Kerja", citizen: "kelas", undercover: "lab komputer" },
            { category: "Sekolah & Kerja", citizen: "papan tulis", undercover: "proyektor" },
            { category: "Sekolah & Kerja", citizen: "kartu siswa", undercover: "ktp" },
            { category: "Sekolah & Kerja", citizen: "skripsi", undercover: "tugas akhir" },
            
            // TRANSPORTASI (15 pairs)
            { category: "Transportasi", citizen: "kereta api", undercover: "krl" },
            { category: "Transportasi", citizen: "mobil", undercover: "taksi" },
            { category: "Transportasi", citizen: "sepeda motor", undercover: "sepeda" },
            { category: "Transportasi", citizen: "kapal laut", undercover: "kapal pesiar" },
            { category: "Transportasi", citizen: "pesawat", undercover: "helikopter" },
            { category: "Transportasi", citizen: "bus", undercover: "angkot" },
            { category: "Transportasi", citizen: "stasiun", undercover: "terminal" },
            { category: "Transportasi", citizen: "bandara", undercover: "pelabuhan" },
            { category: "Transportasi", citizen: "lampu merah", undercover: "polisi tidur" },
            { category: "Transportasi", citizen: "supir", undercover: "pilot" },
            { category: "Transportasi", citizen: "bensin", undercover: "solar" },
            { category: "Transportasi", citizen: "kartu tol", undercover: "e-money" },
            { category: "Transportasi", citizen: "helm", undercover: "sabuk pengaman" },
            { category: "Transportasi", citizen: "aspal", undercover: "rel" },
            { category: "Transportasi", citizen: "ban", undercover: "kemudi" },
            
            // HOBI & HIBURAN (20 pairs)
            { category: "Hiburan", citizen: "bioskop", undercover: "netflix" },
            { category: "Hiburan", citizen: "novel", undercover: "komik" },
            { category: "Hiburan", citizen: "futsal", undercover: "sepak bola" },
            { category: "Hiburan", citizen: "berenang", undercover: "menyelam" },
            { category: "Hiburan", citizen: "gitar", undercover: "biola" },
            { category: "Hiburan", citizen: "catur", undercover: "ludo" },
            { category: "Hiburan", citizen: "playstation", undercover: "pc gaming" },
            { category: "Hiburan", citizen: "karaoke", undercover: "konser" },
            { category: "Hiburan", citizen: "pantai", undercover: "gunung" },
            { category: "Hiburan", citizen: "kebun binatang", undercover: "taman safari" },
            { category: "Hiburan", citizen: "fotografi", undercover: "videografi" },
            { category: "Hiburan", citizen: "museum", undercover: "galeri seni" },
            { category: "Hiburan", citizen: "catur", undercover: "monopoli" },
            { category: "Hiburan", citizen: "free fire", undercover: "pubg mobile" },
            { category: "Hiburan", citizen: "mendaki", undercover: "berkemah" },
            { category: "Hiburan", citizen: "memancing", undercover: "berburu" },
            { category: "Hiburan", citizen: "sketsa", undercover: "lukisan" },
            { category: "Hiburan", citizen: "danau", undercover: "kolam renang" },
            { category: "Hiburan", citizen: "bioskop", undercover: "teater" },
            { category: "Hiburan", citizen: "taman kota", undercover: "hutan raya" },
            
            // GEOGRAFI & ALAM (10 pairs)
            { category: "Geografi", citizen: "jakarta", undercover: "bandung" },
            { category: "Geografi", citizen: "surabaya", undercover: "semarang" },
            { category: "Geografi", citizen: "gunung api", undercover: "bukit" },
            { category: "Geografi", citizen: "sungai", undercover: "danau" },
            { category: "Geografi", citizen: "hutan", undercover: "taman" },
            { category: "Geografi", citizen: "desa", undercover: "kota" },
            { category: "Geografi", citizen: "pulau", undercover: "semenanjung" },
            { category: "Geografi", citizen: "pantai pasang", undercover: "ombak besar" },
            { category: "Geografi", citizen: "sawah", undercover: "kebun" },
            { category: "Geografi", citizen: "air terjun", undercover: "mata air" },
            
            // KONSEP & SAINS (10 pairs)
            { category: "Konsep & Sains", citizen: "hujan", undercover: "salju" },
            { category: "Konsep & Sains", citizen: "panas", undercover: "dingin" },
            { category: "Konsep & Sains", citizen: "pagi", undercover: "sore" },
            { category: "Konsep & Sains", citizen: "bintang", undercover: "bulan" },
            { category: "Konsep & Sains", citizen: "bumi", undercover: "mars" },
            { category: "Konsep & Sains", citizen: "dokter", undercover: "perawat" },
            { category: "Konsep & Sains", citizen: "polisi", undercover: "tentara" },
            { category: "Konsep & Sains", citizen: "resistor", undercover: "kapasitor" },
            { category: "Konsep & Sains", citizen: "led", undercover: "neon" },
            { category: "Konsep & Sains", citizen: "baterai", undercover: "aki" }
        ];

        // Global State
        const G = {
            stage: 'setup',
            playerNames: [],
            players: [],
            revealIdx: 0,
            revealShown: false,
            config: {
                undercovers: 1,
                whites: 0,
                wordMode: 'offline', // 'offline' | 'custom' | 'ai'
            },
            words: {
                citizen: '',
                undercover: '',
                category: ''
            },
            timer: {
                interval: null,
                time: 30,
                running: false
            }
        };

        // Fallback names
        const defaultRoster = [
            "alwan",
            "anjeli",
            "anni",
            "baso",
            "irfa dilla",
            "nova",
            "raidah",
            "fauziah",
            "hakim",
            "aril",
            "ancha",
            "rasyah",
            "reva",
            "andreas",
            "zizi",
            "aldy",
            "diva",
            "yunus",
            "tiwi",
            "tasa",
            "aca",
            "rafa",
            "alim"
        ];

        document.addEventListener('DOMContentLoaded', () => {
            // Load roster from Werewolf storage or fallback
            const saved = localStorage.getItem('devtronix_ww_names');
            if (saved) {
                try {
                    G.playerNames = JSON.parse(saved);
                } catch(e) {
                    G.playerNames = [...defaultRoster];
                }
            } else {
                G.playerNames = [...defaultRoster];
            }

            // Sync API Key warning
            updateAIWarning();
            
            // Sync Word Mode tab active style
            setWordMode('offline');

            // Render roster in setup
            renderSetupRoster();
            
            // Validate config variables
            validateRoleCounts();
        });

        /* ============================================================
           NAVIGATION & RENDERING
           ============================================================ */
        function showScreen(id) {
            document.getElementById('screen-setup').classList.add('hidden');
            document.getElementById('screen-reveal').classList.add('hidden');
            document.getElementById('screen-play').classList.add('hidden');
            document.getElementById('screen-victory').classList.add('hidden');
            
            document.getElementById(id).classList.remove('hidden');
        }

        function renderSetupRoster() {
            const container = document.getElementById('setup-player-list');
            document.getElementById('setup-count-badge').textContent = G.playerNames.length;
            
            container.innerHTML = '';
            G.playerNames.forEach((name, idx) => {
                const card = document.createElement('div');
                card.className = "flex justify-between items-center bg-white border-2 border-black p-2.5 rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] group hover:translate-y-[-2px] transition-transform duration-100";
                card.innerHTML = `
                    <span class="font-black text-xs uppercase tracking-tight truncate mr-2 select-none">${name}</span>
                    <button type="button" onclick="deletePlayer(${idx})" class="text-xs font-black text-brutal-pink hover:scale-125 transition-transform cursor-pointer">✕</button>
                `;
                container.appendChild(card);
            });
        }

        function addPlayer(e) {
            e.preventDefault();
            const input = document.getElementById('new-player-name');
            const name = input.value.trim().toLowerCase();
            
            if (!name) return;
            if (G.playerNames.includes(name)) {
                alert('Nama pemain ini sudah ada!');
                return;
            }

            G.playerNames.push(name);
            localStorage.setItem('devtronix_ww_names', JSON.stringify(G.playerNames));
            input.value = '';
            
            renderSetupRoster();
            validateRoleCounts();
        }

        function deletePlayer(idx) {
            G.playerNames.splice(idx, 1);
            localStorage.setItem('devtronix_ww_names', JSON.stringify(G.playerNames));
            renderSetupRoster();
            validateRoleCounts();
        }

        function resetRosterToClass() {
            if (confirm("Apakah Anda yakin ingin mereset roster pemain ke 23 nama teman kelas secara penuh? Perubahan nama saat ini akan hilang.")) {
                G.playerNames = [...defaultRoster];
                localStorage.setItem('devtronix_ww_names', JSON.stringify(G.playerNames));
                renderSetupRoster();
                validateRoleCounts();
            }
        }

        /* ============================================================
           CONFIGURATIONS & ATURAN PERAN
           ============================================================ */
        function adjustConfig(key, diff) {
            const count = G.playerNames.length;
            if (count < 3) {
                alert('Pemain harus berjumlah minimal 3 orang!');
                return;
            }

            let proposed = G.config[key] + diff;

            if (key === 'undercovers') {
                if (proposed < 1) proposed = 1;
                // Undercovers max: (N - 1) / 2
                const maxUndercovers = Math.floor((count - 1) / 2);
                if (proposed > maxUndercovers) proposed = maxUndercovers;
            } else if (key === 'whites') {
                if (proposed < 0) proposed = 0;
                // Mr. Whites max: (N - 1) / 3
                const maxWhites = Math.floor((count - 1) / 3);
                if (proposed > maxWhites) proposed = maxWhites;
            }

            G.config[key] = proposed;
            document.getElementById(`val-${key}`).textContent = proposed;
            
            validateRoleCounts();
        }

        function validateRoleCounts() {
            const count = G.playerNames.length;
            
            // Enforce safe limits dynamically based on active player list size
            const maxUndercovers = Math.max(1, Math.floor((count - 1) / 2));
            const maxWhites = Math.floor((count - 1) / 3);

            if (G.config.undercovers > maxUndercovers) {
                G.config.undercovers = maxUndercovers;
                document.getElementById('val-undercovers').textContent = maxUndercovers;
            }
            if (G.config.whites > maxWhites) {
                G.config.whites = maxWhites;
                document.getElementById('val-whites').textContent = maxWhites;
            }

            // Hitung Citizen
            const citizens = count - G.config.undercovers - G.config.whites;
            
            // Update UI status if needed
        }

        function setWordMode(mode) {
            G.config.wordMode = mode;
            
            const btnOffline = document.getElementById('btn-mode-offline');
            const btnCustom = document.getElementById('btn-mode-custom');
            const btnAI = document.getElementById('btn-mode-ai');
            
            btnOffline.className = "py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-transparent select-none cursor-pointer";
            btnCustom.className = "py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-transparent select-none cursor-pointer";
            btnAI.className = "py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-transparent select-none cursor-pointer";
            
            document.getElementById('pane-mode-offline').classList.add('hidden');
            document.getElementById('pane-mode-custom').classList.add('hidden');
            document.getElementById('pane-mode-ai').classList.add('hidden');

            if (mode === 'offline') {
                btnOffline.className = "py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-black bg-white select-none cursor-pointer shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]";
                document.getElementById('pane-mode-offline').classList.remove('hidden');
            } else if (mode === 'custom') {
                btnCustom.className = "py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-black bg-white select-none cursor-pointer shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]";
                document.getElementById('pane-mode-custom').classList.remove('hidden');
            } else if (mode === 'ai') {
                btnAI.className = "py-1.5 text-[10px] font-black uppercase rounded-lg border-2 border-black bg-white select-none cursor-pointer shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]";
                document.getElementById('pane-mode-ai').classList.remove('hidden');
            }
        }

        /* ============================================================
           MODAL CONFIGURATION (API KEY)
           ============================================================ */
        function openConfigModal() {
            const key = localStorage.getItem('devtronix_gemini_apikey') || '';
            document.getElementById('api-key-input').value = key;
            document.getElementById('config-modal').classList.remove('hidden');
        }

        function closeConfigModal() {
            document.getElementById('config-modal').classList.add('hidden');
        }

        function saveConfig() {
            const key = document.getElementById('api-key-input').value.trim();
            if (key) {
                localStorage.setItem('devtronix_gemini_apikey', key);
            } else {
                localStorage.removeItem('devtronix_gemini_apikey');
            }
            updateAIWarning();
            closeConfigModal();
        }

        function updateAIWarning() {
            const key = localStorage.getItem('devtronix_gemini_apikey');
            const warning = document.getElementById('ai-setup-warning');
            if (key) {
                warning.classList.add('hidden');
            } else {
                warning.classList.remove('hidden');
            }
        }

        /* ============================================================
           GAME FLOW CONTROL
           ============================================================ */
        async function startGame() {
            if (G.playerNames.length < 3) {
                alert('Pemain minimal berjumlah 3 orang untuk memulai Undercover!');
                return;
            }

            // Step 1: Pilihan kata (Word Generation)
            if (G.config.wordMode === 'offline') {
                const categoryFilter = document.getElementById('select-category').value;
                let filtered = [...OFFLINE_WORD_PAIRS];
                if (categoryFilter !== 'Semua') {
                    filtered = OFFLINE_WORD_PAIRS.filter(pair => pair.category === categoryFilter);
                }
                
                if (filtered.length === 0) {
                    alert('Gagal menyusun list kata dari filter ini!');
                    return;
                }

                // Ambil kata acak
                const pair = filtered[Math.floor(Math.random() * filtered.length)];
                G.words.citizen = pair.citizen;
                G.words.undercover = pair.undercover;
                G.words.category = pair.category;
            }
            else if (G.config.wordMode === 'custom') {
                const cat = document.getElementById('cust-category').value.trim();
                const cit = document.getElementById('cust-citizen').value.trim();
                const und = document.getElementById('cust-undercover').value.trim();
                
                if (!cit || !und) {
                    alert('Mohon isi kata untuk Warga (Citizen) dan Penyusup (Undercover)!');
                    return;
                }
                
                G.words.citizen = cit;
                G.words.undercover = und;
                G.words.category = cat ? cat : 'Custom';
            }
            else if (G.config.wordMode === 'ai') {
                const key = localStorage.getItem('devtronix_gemini_apikey');
                if (!key) {
                    alert('Gemini API Key belum dikonfigurasi! Klik tombol "PENGATURAN AI" di kanan atas.');
                    return;
                }
                
                const theme = document.getElementById('ai-category-input').value.trim() || 'Acak / Seru';
                
                // Tampilkan loading screen/alert sederhana
                const playBtn = document.querySelector('#pane-mode-ai + button') || document.querySelector('button[onclick="startGame()"]');
                const originalText = playBtn.innerHTML;
                playBtn.innerHTML = "🤖 AI SEDANG MENGGENERATE KATA...";
                playBtn.disabled = true;

                try {
                    const response = await fetch('ai-word-generator.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ apiKey: key, category: theme })
                    });
                    
                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.error || 'Gagal menghubungi server Gemini.');
                    }
                    
                    G.words.citizen = data.citizen;
                    G.words.undercover = data.undercover;
                    G.words.category = data.category;
                } catch(err) {
                    alert('AI Error: ' + err.message + '\n\nSistem dialihkan otomatis ke database kata offline (aman & cepat).');
                    
                    // Fallback otomatis ke offline yang andal!
                    const pair = OFFLINE_WORD_PAIRS[Math.floor(Math.random() * OFFLINE_WORD_PAIRS.length)];
                    G.words.citizen = pair.citizen;
                    G.words.undercover = pair.undercover;
                    G.words.category = pair.category + ' (Offline Fallback)';
                } finally {
                    playBtn.innerHTML = originalText;
                    playBtn.disabled = false;
                }
            }

            // Step 2: Role Allocation
            allocateRoles();
            
            // Step 3: Pindah ke Reveal Screen
            G.revealIdx = 0;
            G.revealShown = false;
            
            setupRevealScreen();
            showScreen('screen-reveal');
        }

        function allocateRoles() {
            const count = G.playerNames.length;
            const names = [...G.playerNames];
            
            // Acak urutan nama
            for (let i = names.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [names[i], names[j]] = [names[j], names[i]];
            }

            // Bikin daftar peran
            const roles = [];
            for (let i = 0; i < G.config.undercovers; i++) roles.push('undercover');
            for (let i = 0; i < G.config.whites; i++) roles.push('white');
            while (roles.length < count) roles.push('citizen');

            // Acak urutan peran
            for (let i = roles.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [roles[i], roles[j]] = [roles[j], roles[i]];
            }

            // Tentukan kata mana yang akan didapat Warga dan mana yang Undercover
            // Kadang kita balik agar Undercover tidak selalu memegang kata cadangan kedua
            const swapWords = Math.random() > 0.5;
            const wordCitizen = swapWords ? G.words.undercover : G.words.citizen;
            const wordUndercover = swapWords ? G.words.citizen : G.words.undercover;

            // Masukkan ke array players utama
            G.players = names.map((name, idx) => {
                const role = roles[idx];
                let word = '';
                if (role === 'citizen') {
                    word = wordCitizen;
                } else if (role === 'undercover') {
                    word = wordUndercover;
                } else {
                    word = '???';
                }

                return {
                    name: name,
                    role: role,
                    word: word,
                    active: true,
                    described: false
                };
            });
        }

        /* ============================================================
           REVEAL PHASE LOGIC (PASS & VIEW)
           ============================================================ */
        function setupRevealScreen() {
            const p = G.players[G.revealIdx];
            const total = G.players.length;
            
            document.getElementById('rev-badge').textContent = `Pemain ${G.revealIdx + 1} dari ${total}`;
            document.getElementById('rev-player-name').textContent = p.name;
            
            const cardBox = document.getElementById('rev-card-box');
            const actionBtn = document.getElementById('rev-action-container');

            if (!G.revealShown) {
                cardBox.innerHTML = `
                    <div onclick="revealCard()" class="cursor-pointer border-3 border-black border-dashed rounded-2xl p-10 bg-brutal-cream flex flex-col items-center justify-center min-h-[220px] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:scale-102 transition-all">
                        <span class="text-6xl mb-4 filter drop-shadow-[2px_2px_0px_#000]">🔒</span>
                        <span class="bg-brutal-yellow text-black border-2 border-black font-black uppercase text-xs px-4 py-2 rounded-lg shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]">
                            Ketuk untuk melihat Kata
                        </span>
                        <p class="text-[10px] text-slate-700 font-bold mt-4">Berikan perangkat ini ke pemain tersebut. Pastikan orang lain tidak melihat!</p>
                    </div>
                `;
                actionBtn.innerHTML = `
                    <button onclick="revealCard()" class="w-full neo-btn bg-brutal-yellow text-black border-2 border-black font-black py-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider text-xs cursor-pointer">
                        👁️ BUKA KATA SAYA
                    </button>
                `;
            } else {
                let displayHTML = '';
                if (p.role === 'white') {
                    displayHTML = `
                        <div class="border-3 border-black rounded-2xl p-8 bg-brutal-purple text-white flex flex-col items-center justify-center min-h-[220px] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                            <span class="text-5xl mb-3">🔳</span>
                            <h3 class="font-black text-2xl uppercase tracking-widest mb-2">Kamu Mr. White!</h3>
                            <div class="border-2 border-white border-dashed bg-black/25 px-5 py-2.5 rounded-xl font-mono text-3xl font-black tracking-widest">???</div>
                            <p class="text-[10px] text-slate-100 font-bold mt-4 text-center leading-relaxed">Kamu tidak memegang kata rahasia! Simak deskripsi pemain lain dengan jeli dan berpura-puralah tahu kata mereka agar tidak tereliminasi!</p>
                        </div>
                    `;
                } else {
                    displayHTML = `
                        <div class="border-3 border-black rounded-2xl p-8 bg-brutal-green text-black flex flex-col items-center justify-center min-h-[220px] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                            <span class="text-5xl mb-3">🤫</span>
                            <h3 class="font-black text-2xl uppercase tracking-widest mb-2">Kata Rahasiamu:</h3>
                            <div class="border-2 border-black border-dashed bg-white text-black px-6 py-2.5 rounded-xl font-mono text-2xl font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-1">
                                ${p.word}
                            </div>
                            <p class="text-[10px] font-bold mt-4 text-center leading-relaxed max-w-[280px] mx-auto">Ingat kata ini dengan baik! Jangan sampai orang lain melihatnya. Deskripsikan dengan cerdik agar tidak dituduh sebagai Penyusup!</p>
                        </div>
                    `;
                }

                cardBox.innerHTML = displayHTML;
                
                const isLast = G.revealIdx === G.players.length - 1;
                const nextAction = isLast ? 'startPlayingPhase()' : 'nextReveal()';
                const nextLabel = isLast ? '🏁 MULAI PAPAN GAME' : '➡️ SEMBUNYIKAN & LANJUT';
                
                actionBtn.innerHTML = `
                    <button onclick="${nextAction}" class="w-full neo-btn bg-brutal-green text-black border-2 border-black font-black py-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider text-xs cursor-pointer">
                        ${nextLabel}
                    </button>
                `;
            }
        }

        function revealCard() {
            G.revealShown = true;
            setupRevealScreen();
        }

        function nextReveal() {
            G.revealIdx++;
            G.revealShown = false;
            setupRevealScreen();
        }

        /* ============================================================
           PLAY PHASE BOARD
           ============================================================ */
        function startPlayingPhase() {
            showScreen('screen-play');
            
            // Set category
            document.getElementById('play-category-name').textContent = G.words.category;
            
            // Clear logs
            const logs = document.getElementById('play-event-logs');
            logs.innerHTML = '<div>💬 Roster dikocok! Silakan lakukan deskripsi kata berkeliling.</div>';

            renderPlayerGrid();
            resetTimer();
        }

        function renderPlayerGrid() {
            const grid = document.getElementById('play-player-grid');
            grid.innerHTML = '';

            G.players.forEach((p, idx) => {
                const card = document.createElement('div');
                
                if (!p.active) {
                    // Render eliminated player card
                    card.className = "bg-slate-200 border-2 border-black border-dashed rounded-2xl p-4 flex flex-col items-center justify-center min-h-[140px] opacity-60 text-slate-500 relative select-none";
                    
                    let roleTag = '';
                    if (p.role === 'citizen') {
                        roleTag = '<span class="bg-brutal-green text-black text-[9px] font-black px-2 py-0.5 rounded border border-black uppercase mt-1">Citizen</span>';
                    } else if (p.role === 'undercover') {
                        roleTag = '<span class="bg-brutal-pink text-white text-[9px] font-black px-2 py-0.5 rounded border border-black uppercase mt-1">Undercover</span>';
                    } else {
                        roleTag = '<span class="bg-brutal-purple text-white text-[9px] font-black px-2 py-0.5 rounded border border-black uppercase mt-1">Mr. White</span>';
                    }

                    card.innerHTML = `
                        <span class="text-3xl filter grayscale opacity-40 mb-1">💀</span>
                        <span class="font-black text-xs uppercase tracking-tight line-through">${p.name}</span>
                        ${roleTag}
                    `;
                } else {
                    // Render active player card
                    const describedColor = p.described ? 'bg-slate-50 border-2 border-slate-300 opacity-80' : 'bg-white border-[3px] border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]';
                    const speakBtn = p.described 
                        ? `<button onclick="toggleSpeak(${idx})" class="w-full bg-slate-200 border border-slate-400 font-bold py-1.5 text-[9px] rounded-lg cursor-pointer uppercase select-none text-slate-500 mt-2">Bicara Lagi</button>` 
                        : `<button onclick="toggleSpeak(${idx})" class="w-full neo-btn bg-brutal-yellow border border-black font-black py-1.5 text-[9px] rounded-lg cursor-pointer uppercase tracking-wider shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] mt-2">🎙️ Selesai Bicara</button>`;

                    card.className = `${describedColor} rounded-2xl p-4 flex flex-col justify-between min-h-[140px] group transition-all duration-100`;
                    
                    card.innerHTML = `
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-mono text-[9px] font-black text-slate-400">AKTIF</span>
                                ${p.described ? '<span class="text-xs">✅</span>' : '<span class="w-2.5 h-2.5 rounded-full bg-brutal-green border border-black animate-pulse"></span>'}
                            </div>
                            <span class="font-black text-sm uppercase tracking-tight text-black block truncate mb-2">${p.name}</span>
                        </div>
                        
                        <div>
                            ${speakBtn}
                            <button onclick="eliminatePlayer(${idx})" class="w-full neo-btn bg-brutal-pink text-white border border-black font-black py-1.5 text-[9px] rounded-lg cursor-pointer uppercase tracking-wider shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] mt-1.5 group-hover:bg-[#FF007A] transition-all">
                                💀 Eliminasi
                            </button>
                        </div>
                    `;
                }

                grid.appendChild(card);
            });
        }

        function toggleSpeak(idx) {
            G.players[idx].described = !G.players[idx].described;
            
            // Log the action
            const p = G.players[idx];
            const logs = document.getElementById('play-event-logs');
            const logDiv = document.createElement('div');
            
            if (p.described) {
                logDiv.innerHTML = `🎙️ <span class="font-black text-black uppercase">${p.name}</span> selesai berbicara.`;
            } else {
                logDiv.innerHTML = `🔄 <span class="font-black text-slate-600 uppercase">${p.name}</span> diatur ulang ke antrean bicara.`;
            }
            logs.appendChild(logDiv);
            logs.scrollTop = logs.scrollHeight;

            // Auto-stop and reset timer
            resetTimer();
            
            // If all active players described, flash a notice in log
            const unspoked = G.players.some(pl => pl.active && !pl.described);
            if (!unspoked) {
                const notice = document.createElement('div');
                notice.className = 'text-brutal-pink font-black uppercase mt-1';
                notice.innerHTML = `📢 SEMUA PEMAIN SUDAH DESKRIPSI! Mulai berdiskusi dan lakukan voting eliminasi.`;
                logs.appendChild(notice);
                logs.scrollTop = logs.scrollHeight;
            }

            renderPlayerGrid();
        }

        /* ============================================================
           ELIMINATIONS & WIN/LOSS DECISIONS
           ============================================================ */
        function eliminatePlayer(idx) {
            const p = G.players[idx];
            if (!confirm(`Apakah Anda yakin ingin mengeliminasi ${p.name.toUpperCase()}?`)) {
                return;
            }

            p.active = false;
            
            // Log role reveal
            let roleMsg = '';
            if (p.role === 'citizen') {
                roleMsg = `<span class="bg-brutal-green text-black border border-black px-1.5 py-0.5 rounded font-black text-[9px] uppercase shadow-[0.5px_0.5px_0px_0px_rgba(0,0,0,1)]">Citizen</span>`;
            } else if (p.role === 'undercover') {
                roleMsg = `<span class="bg-brutal-pink text-white border border-black px-1.5 py-0.5 rounded font-black text-[9px] uppercase shadow-[0.5px_0.5px_0px_0px_rgba(0,0,0,1)]">Undercover</span>`;
            } else {
                roleMsg = `<span class="bg-brutal-purple text-white border border-black px-1.5 py-0.5 rounded font-black text-[9px] uppercase shadow-[0.5px_0.5px_0px_0px_rgba(0,0,0,1)]">Mr. White</span>`;
            }

            const logs = document.getElementById('play-event-logs');
            const logDiv = document.createElement('div');
            logDiv.className = 'font-bold p-1 bg-slate-100 border border-black rounded';
            logDiv.innerHTML = `💀 <span class="font-black text-black uppercase">${p.name}</span> dieliminasi! Perannya: ${roleMsg}.`;
            logs.appendChild(logDiv);
            logs.scrollTop = logs.scrollHeight;

            // Reset description spoken state for all players to start next round
            G.players.forEach(pl => {
                pl.described = false;
            });

            // Check if victory condition met
            checkVictory();
            
            renderPlayerGrid();
            resetTimer();
        }

        function checkVictory() {
            // Count remaining active roles
            const actives = G.players.filter(pl => pl.active);
            const totalActive = actives.length;
            
            const citizens = actives.filter(pl => pl.role === 'citizen').length;
            const undercovers = actives.filter(pl => pl.role === 'undercover').length;
            const whites = actives.filter(pl => pl.role === 'white').length;

            const badGuysCount = undercovers + whites;

            if (badGuysCount === 0) {
                // Citizen wins!
                triggerVictory('citizen');
            } else if (totalActive <= 2) {
                // Undercover & Mr White wins if total active players drops to 2!
                triggerVictory('undercover');
            }
        }

        function triggerVictory(winner) {
            // Stop timers
            clearInterval(G.timer.interval);
            G.timer.running = false;
            
            showScreen('screen-victory');
            
            const title = document.getElementById('victory-title');
            const desc = document.getElementById('victory-desc');
            const emoji = document.getElementById('victory-emoji');
            const stripe = document.getElementById('victory-header-stripe');

            // Words reveal values
            document.getElementById('vic-word-citizen').textContent = G.words.citizen;
            document.getElementById('vic-word-undercover').textContent = G.words.undercover;

            const rowWhites = document.getElementById('vic-row-whites');
            const whitesList = G.players.filter(pl => pl.role === 'white').map(pl => pl.name.toUpperCase());
            if (whitesList.length > 0) {
                rowWhites.classList.remove('hidden');
                rowWhites.querySelector('span:last-child').textContent = whitesList.join(', ');
            } else {
                rowWhites.classList.add('hidden');
            }

            if (winner === 'citizen') {
                title.textContent = 'Warga (Citizen) Menang!';
                desc.textContent = 'Seluruh penyusup dan Mr. White berhasil dieliminasi dengan cerdik!';
                emoji.textContent = '🏆';
                stripe.className = 'absolute top-0 left-0 w-full h-4 bg-brutal-green border-b-2 border-black';
            } else {
                title.textContent = 'Penyusup Menang!';
                desc.textContent = 'Penyusup / Mr. White berhasil menyusup dan memenangi permainan kelas!';
                emoji.textContent = '🕵️‍♂️';
                stripe.className = 'absolute top-0 left-0 w-full h-4 bg-brutal-pink border-b-2 border-black';
            }

            // Fill full roster details for wrap-up
            const summaryList = document.getElementById('victory-roster-list');
            summaryList.innerHTML = '';
            
            G.players.forEach(p => {
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center text-xs font-bold border border-black p-2 bg-white rounded-lg shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]';
                
                let roleLabel = '';
                if (p.role === 'citizen') {
                    roleLabel = '<span class="bg-brutal-green border border-black px-1.5 py-0.5 rounded text-[9px] font-black text-black">CITIZEN</span>';
                } else if (p.role === 'undercover') {
                    roleLabel = '<span class="bg-brutal-pink border border-black px-1.5 py-0.5 rounded text-[9px] font-black text-white">UNDERCOVER</span>';
                } else {
                    roleLabel = '<span class="bg-brutal-purple border border-black px-1.5 py-0.5 rounded text-[9px] font-black text-white">MR. WHITE</span>';
                }

                item.innerHTML = `
                    <span class="uppercase">${p.name} ${!p.active ? '💀' : '💖'}</span>
                    ${roleLabel}
                `;
                summaryList.appendChild(item);
            });
        }

        /* ============================================================
           TIMER STOPWATCH FUNCTIONS
           ============================================================ */
        function toggleTimer() {
            const btn = document.getElementById('timer-control-btn');
            if (G.timer.running) {
                // Pause timer
                clearInterval(G.timer.interval);
                G.timer.running = false;
                btn.textContent = 'START';
                btn.className = "neo-btn bg-brutal-green text-black border border-black p-1 text-[10px] font-black rounded cursor-pointer";
            } else {
                // Start timer
                G.timer.running = true;
                btn.textContent = 'PAUSE';
                btn.className = "neo-btn bg-brutal-pink text-white border border-black p-1 text-[10px] font-black rounded cursor-pointer";
                
                G.timer.interval = setInterval(() => {
                    G.timer.time--;
                    document.getElementById('timer-display').textContent = `${G.timer.time}s`;
                    
                    if (G.timer.time <= 0) {
                        // Play alert sound if wanted, or just simple timeout log
                        clearInterval(G.timer.interval);
                        G.timer.running = false;
                        btn.textContent = 'START';
                        btn.className = "neo-btn bg-brutal-green text-black border border-black p-1 text-[10px] font-black rounded cursor-pointer";
                        
                        document.getElementById('timer-display').textContent = `WAKTU HABIS!`;
                        document.getElementById('timer-display').classList.add('text-brutal-pink');
                        
                        const logs = document.getElementById('play-event-logs');
                        const logDiv = document.createElement('div');
                        logDiv.className = 'font-black text-brutal-pink uppercase';
                        logDiv.innerHTML = `⚠️ WAKTU HABIS! Silakan percepat giliran berbicara pemain berikutnya.`;
                        logs.appendChild(logDiv);
                        logs.scrollTop = logs.scrollHeight;
                    }
                }, 1000);
            }
        }

        function resetTimer() {
            clearInterval(G.timer.interval);
            G.timer.time = 30;
            G.timer.running = false;
            
            document.getElementById('timer-display').textContent = '30s';
            document.getElementById('timer-display').classList.remove('text-brutal-pink');
            
            const btn = document.getElementById('timer-control-btn');
            btn.textContent = 'START';
            btn.className = "neo-btn bg-brutal-green text-black border border-black p-1 text-[10px] font-black rounded cursor-pointer";
        }

        /* ============================================================
           ABORT & RESET RESETS
           ============================================================ */
        function abortGame() {
            if (confirm('Apakah Anda yakin ingin membatalkan dan mengakhiri permainan? Progres game saat ini akan hilang!')) {
                clearInterval(G.timer.interval);
                G.timer.running = false;
                showScreen('screen-setup');
            }
        }

        function resetGame() {
            showScreen('screen-setup');
        }
    </script>
</body>
</html>
