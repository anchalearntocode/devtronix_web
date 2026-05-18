<?php
// Silakan tambahkan logika backend PHP di sini jika ke depannya 
// Anda ingin membuat game ini menjadi online multiplayer.
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Werewolf — Malam yang Mencekam</title>
<link rel="icon" type="image/svg+xml" href="uploads/kelas_logo_circle.svg?v=3">
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
:root {
  --gold: #FFF000;
  --red: #FF007A;
  --green: #00FF66;
  --blue: #00F0FF;
  --purple: #9E00FF;
  --silver: #ffffff;
  --orange: #FFF000;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
  background-color: #FAF6EE;
  background-image: 
    radial-gradient(#000000 1.5px, transparent 1.5px), 
    radial-gradient(#000000 1.5px, #FAF6EE 1.5px);
  background-size: 30px 30px;
  background-position: 0 0, 15px 15px;
  color: #000000;
  font-family: 'Space Grotesk', sans-serif;
  min-height: 100vh;
  overflow-x: hidden;
}

h1, h2, h3 { font-family: 'Space Grotesk', sans-serif; font-weight: 900; }
h1 { font-size: clamp(2rem, 5vw, 3.5rem); text-align: center; text-transform: uppercase; line-height: 1.1; }
h2 { font-size: clamp(1.4rem, 4vw, 2.2rem); text-align: center; text-transform: uppercase; }
h3 { font-size: 1.1rem; font-weight: 900; text-transform: uppercase; margin-bottom: 12px; }
p { font-size: 1rem; line-height: 1.6; }

.screen { display: none; min-height: 100vh; position: relative; z-index: 1; flex-direction: column; align-items: center; padding: 40px 20px 80px; }
.screen.active { display: flex; }
.container { width: 100%; max-width: 900px; margin: 0 auto; }
.narrow-container { width: 100%; max-width: 500px; margin: 0 auto; text-align: center; }
.div { width: 100px; height: 4px; background: #000000; margin: 16px auto; border-radius: 2px; }

.glass-panel { background: #ffffff; border: 3px solid #000000; border-radius: 16px; padding: 24px; box-shadow: 4px 4px 0px 0px rgba(0,0,0,1); }

.neo-btn {
  font-weight: 900;
  letter-spacing: 0.05em;
  padding: 14px 28px;
  border: 2px solid #000000;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.15s cubic-bezier(0, 0, 0, 1);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-transform: uppercase;
  color: #000000;
  width: 100%;
}
@media (min-width: 480px) { .neo-btn { width: auto; } }
.neo-btn:hover:not(:disabled) { transform: translate(-3px, -3px); box-shadow: 3px 3px 0px 0px rgba(0,0,0,1); }
.neo-btn:active:not(:disabled) { transform: translate(1px, 1px); box-shadow: none; }
.neo-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.btn-gold { background: #FFF000; }
.btn-red { background: #FF007A; }
.btn-green { background: #00FF66; }
.btn-blue { background: #00F0FF; }
.btn-ghost { background: #ffffff; }

.grid-2 { display: grid; grid-template-columns: 1fr; gap: 16px; }
@media (min-width: 600px) { .grid-2 { grid-template-columns: 1fr 1fr; } }
@media (min-width: 900px) { .setup-grid { display: grid; grid-template-columns: 1fr 1.1fr; gap: 30px; align-items: start; } }

.panduan-grid { display: flex; flex-direction: column; gap: 12px; }
.legend-item { display: flex; gap: 14px; align-items: flex-start; padding: 14px; background: #ffffff; border: 2px solid #000000; border-radius: 12px; transition: all 0.15s; word-break: break-word; box-shadow: 2px 2px 0px 0px rgba(0,0,0,1); }
.legend-item:hover { background: #FFF000; transform: translate(-2px, -2px); box-shadow: 4px 4px 0px 0px rgba(0,0,0,1); }
.l-emoji { font-size: 1.8rem; line-height: 1; flex-shrink: 0; }
.l-name { font-size: 1rem; font-weight: 900; margin-bottom: 4px; text-transform: uppercase; }
.l-desc { font-size: 0.85rem; color: #333333; line-height: 1.5; font-weight: 700; word-break: break-word; }

.count-ctrl { display: flex; align-items: center; justify-content: center; gap: 20px; }
.ctrl-btn { width: 44px; height: 44px; border-radius: 10px; border: 2px solid #000000; background: #FFF000; color: #000000; font-size: 1.5rem; font-weight: 950; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: center; shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]; }
.ctrl-btn:hover { transform: translate(-2px, -2px); box-shadow: 2px 2px 0px 0px rgba(0,0,0,1); }
.ctrl-btn:active { transform: translate(1px, 1px); box-shadow: none; }
.cnt-val { font-size: 2.2rem; font-weight: 900; color: #000000; width: 60px; text-align: center; }

.names-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
.name-wrap { display: flex; align-items: center; gap: 10px; background: #FAF6EE; padding: 8px 12px; border-radius: 10px; border: 2px solid #000000; shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]; }
.name-num { font-size: 0.85rem; color: #000000; font-weight: 900; width: 20px; }
input[type=text] { flex: 1; background: transparent; border: none; color: #000000; font-family: 'Space Grotesk', sans-serif; font-size: 0.95rem; font-weight: 700; outline: none; width: 100%; padding: 4px 0; }
input[type=text]::placeholder { color: #888888; opacity: 0.7; }

.role-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #FAF6EE; border: 2px solid #000000; border-radius: 10px; transition: all 0.15s; margin-bottom: 10px; box-shadow: 2px 2px 0px 0px rgba(0,0,0,1); }
.role-row:hover { background: #ffffff; transform: translate(-2px, -2px); box-shadow: 4px 4px 0px 0px rgba(0,0,0,1); }
.role-rname { font-size: 0.95rem; font-weight: 900; display: flex; align-items: center; gap: 10px; text-transform: uppercase; }
.role-rname span:first-child { font-size: 1.5rem; }
.role-ctrl { display: flex; align-items: center; gap: 10px; }
.role-cnt { font-size: 1.1rem; color: #000000; font-weight: 900; width: 24px; text-align: center; }
.rb { width: 30px; height: 30px; border-radius: 6px; border: 2px solid #000000; background: #ffffff; color: #000000; cursor: pointer; transition: all 0.15s; font-size: 1.2rem; font-weight: 900; display: flex; align-items: center; justify-content: center; }
.rb:hover { background: #FFF000; transform: translate(-1px, -1px); box-shadow: 1px 1px 0px 0px rgba(0,0,0,1); }
.rb:active { transform: translate(1px, 1px); box-shadow: none; }

.psel-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin: 20px 0; }
.roles-grid { display: grid; grid-template-columns: 1fr; gap: 8px; }
@media (min-width: 600px) { .roles-grid { grid-template-columns: 1fr 1fr; } }
.psel { padding: 14px 10px; background: #ffffff; border: 2px solid #000000; border-radius: 12px; color: #000000; font-family: 'Space Grotesk', sans-serif; font-size: 1rem; font-weight: 900; cursor: pointer; transition: all 0.15s; text-align: center; box-shadow: 2px 2px 0px 0px rgba(0,0,0,1); }
.psel:hover:not(:disabled) { background: #FFF000; transform: translate(-2px, -2px); box-shadow: 4px 4px 0px 0px rgba(0,0,0,1); }
.psel:active:not(:disabled) { transform: translate(1px, 1px); box-shadow: none; }
.psel.sel { border-color: #000000; background: #FFF000; font-weight: 900; box-shadow: 1px 1px 0px 0px rgba(0,0,0,1); transform: translate(1px, 1px); }
.psel:disabled { opacity: 0.35; cursor: not-allowed; text-decoration: line-through; box-shadow: none; }

.info-box { background: #00F0FF; border: 2px solid #000000; border-radius: 12px; padding: 16px; font-size: 0.95rem; color: #000000; text-align: center; line-height: 1.6; font-weight: 800; box-shadow: 3px 3px 0px 0px rgba(0,0,0,1); }
.info-box strong { color: #FF007A; font-weight: 900; }
.ann { border-radius: 12px; border: 2px solid #000000; padding: 16px; text-align: center; margin-bottom: 16px; font-size: 1.05rem; font-weight: 900; box-shadow: 3px 3px 0px 0px rgba(0,0,0,1); }
.ann-dead { background: #FF007A; color: #ffffff; }
.ann-safe { background: #00FF66; color: #000000; }

.vote-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: #ffffff; border: 2px solid #000000; border-radius: 12px; margin-bottom: 12px; transition: all 0.15s; box-shadow: 2px 2px 0px 0px rgba(0,0,0,1); }
.vote-row:hover { background: #FAF6EE; transform: translate(-2px, -2px); box-shadow: 4px 4px 0px 0px rgba(0,0,0,1); }
.pname { font-size: 1.1rem; font-weight: 900; text-transform: uppercase; }
.v-ctrl { display: flex; align-items: center; gap: 12px; }
.v-btn { width: 36px; height: 36px; border-radius: 8px; border: 2px solid #000000; background: #00F0FF; color: #000000; cursor: pointer; font-size: 1.2rem; font-weight: 900; transition: all 0.15s; display: flex; align-items: center; justify-content: center; }
.v-btn:hover { background: #FFF000; transform: translate(-1px, -1px); box-shadow: 1px 1px 0px 0px rgba(0,0,0,1); }
.v-btn:active { transform: translate(1px, 1px); box-shadow: none; }
.v-cnt { font-size: 1.4rem; color: #000000; font-weight: 900; width: 30px; text-align: center; }

.status-bar { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
.schip { font-size: 0.8rem; font-weight: 900; padding: 6px 16px; border-radius: 20px; border: 2px solid #000000; box-shadow: 2px 2px 0px 0px rgba(0,0,0,1); text-transform: uppercase; }
.schip-wolf { background: #FF007A; color: #ffffff; }
.schip-village { background: #00FF66; color: #000000; }
.schip-all { background: #ffffff; color: #000000; }
.tag { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 900; margin: 4px; border: 2px solid #000000; background: #ffffff; color: #000000; box-shadow: 1px 1px 0px 0px rgba(0,0,0,1); text-transform: uppercase; }

.modal-bg { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); backdrop-blur-sm: 4px; display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; opacity: 0; pointer-events: none; transition: opacity 0.2s; }
.modal-bg.active { opacity: 1; pointer-events: auto; }
.modal-box { background: #FAF6EE; border: 3px solid #000000; border-radius: 20px; padding: 30px; width: 100%; max-width: 440px; text-align: center; box-shadow: 8px 8px 0px 0px rgba(0, 0, 0, 1); transform: translateY(20px); transition: transform 0.2s; }
.modal-bg.active .modal-box { transform: translateY(0); }

@keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.fade-up { animation: fadeIn 0.4s ease forwards; }
.mt-4 { margin-top: 16px; } .mt-6 { margin-top: 24px; } .mt-8 { margin-top: 32px; }
.text-center { text-align: center; } .flex-center { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
</style>
</head>
<body class="selection:bg-brutal-yellow">

<!-- STICKY BRUTALIST NAVBAR -->
<nav class="bg-white border-b-[3px] border-black p-4 flex justify-between items-center sticky top-0 z-[100] shadow-[0px_4px_0px_0px_rgba(0,0,0,1)]">
  <div class="flex items-center gap-3 font-black text-xl text-black uppercase tracking-tight">
    <img src="uploads/werewolf_logo.png?v=2" alt="Logo" class="w-8 h-8 rounded-full border-2 border-black shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] bg-white p-0.5"> DEVTRONIX WEREWOLF
  </div>
  <div class="flex gap-3 align-items-center">
    <button id="bgm-btn" onclick="toggleBGM()" class="neo-btn bg-white text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] max-h-[38px] cursor-pointer">
      🔇 MUSIC OFF
    </button>
    <button id="end-game-btn" onclick="endGameKeepSetup()" style="display: none;" class="neo-btn bg-brutal-pink text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] max-h-[38px] cursor-pointer">
      ⏹ AKHIRI
    </button>
    <a href="index.php" class="neo-btn bg-brutal-cyan text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] max-h-[38px] text-center">
      ⭠ KEMBALI
    </a>
  </div>
</nav>


<!-- SETUP SCREEN -->
<div id="setup" class="screen active">
  <div class="container fade-up">
    <div class="text-center mb-8">
      <div class="flex justify-center mb-4">
        <img src="uploads/werewolf_logo.png?v=2" alt="Werewolf Logo" class="h-28 w-28 object-contain rounded-full border-4 border-black bg-white p-1.5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
      </div>
      <h1 class="font-black text-black">WEREWOLF</h1>
      <p class="text-black font-black uppercase tracking-widest text-sm bg-brutal-yellow border-2 border-black inline-block px-4 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mt-3">
        Malam yang Mencekam (24 Roles)
      </p>
      <div class="div"></div>
    </div>

    <div class="setup-grid">
      <div>
        <div class="glass-panel mb-6">
          <h3 class="border-b-2 border-black pb-2">👥 Jumlah Pemain</h3>
          <div class="count-ctrl mt-4">
            <button class="ctrl-btn" onclick="changePC(-1)">−</button>
            <span class="cnt-val" id="pc-display">8</span>
            <button class="ctrl-btn" onclick="changePC(1)">+</button>
          </div>
        </div>

        <div class="glass-panel mb-6">
          <div class="flex justify-between items-center border-b-2 border-black pb-2">
            <h3 class="m-0">✏️ Nama Pemain</h3>
            <div class="flex gap-2">
              <button onclick="resetToDefault()" class="text-xs bg-brutal-yellow text-black border-2 border-black px-3 py-1.5 font-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] cursor-pointer hover:bg-[#E6D600] active:translate-y-[1px]">
                🔄 RESET DEFAULT
              </button>
              <button onclick="addPlayer()" class="text-xs bg-brutal-green text-black border-2 border-black px-3 py-1.5 font-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] cursor-pointer hover:bg-[#33FF99] active:translate-y-[1px]">
                ➕ TAMBAH PEMAIN
              </button>
            </div>
          </div>
          <div class="names-grid mt-4" id="names-grid"></div>
        </div>

        <div class="glass-panel mb-6">
          <div class="flex justify-between items-center border-b-2 border-black pb-2">
            <h3 class="m-0">⚙️ Konfigurasi Peran</h3>
            <span id="sum-status" class="text-xs font-black uppercase px-3 py-1 rounded-lg border-2 border-black shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]"></span>
          </div>
          <div id="roles-cfg" class="roles-grid mt-4"></div>
        </div>
      </div>

      <div>
        <div class="glass-panel sticky top-24">
          <h3 class="border-b-2 border-black pb-2 mb-4">📖 Panduan Peran (24 Role)</h3>
          <div style="max-height: 55vh; overflow-y: auto; padding-right: 8px;" class="panduan-grid">
            <!-- Populated via JS -->
          </div>
          <div class="mt-6 text-center border-t-2 border-black border-dashed pt-4">
            <button class="neo-btn btn-gold w-full py-4 text-base shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer" id="start-btn" onclick="startGame()">
              Mulai Permainan 🌙
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- REVEAL SCREEN -->
<div id="reveal" class="screen">
  <div class="narrow-container fade-up">
    <h3 id="rev-label" class="bg-brutal-yellow border-2 border-black px-4 py-1.5 rounded-full inline-block text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">PEMAIN 1 DARI X</h3>
    <p class="mt-4" id="rev-hint" class="font-bold text-slate-800 italic">Berikan perangkat ke pemain ini.</p>
    <h2 class="mt-4 font-black uppercase text-3xl md:text-5xl" id="rev-pname">—</h2>
    <div class="mt-6 glass-panel" id="rev-content" style="min-height: 250px; display: flex; flex-direction: column; justify-content: center;"></div>
    <div class="mt-6 flex-center" id="rev-btns"></div>
  </div>
</div>

<!-- NIGHT COVER -->
<div id="nightcover" class="screen">
  <div class="narrow-container fade-up">
    <span class="text-7xl block mb-4 drop-shadow-[4px_4px_0px_rgba(0,0,0,1)]">🌙</span>
    <h2 class="text-black font-black">Malam Turun</h2>
    <div class="div"></div>
    <p class="mt-4 font-bold text-slate-800 italic">Semua pemain menutup mata dan menundukkan kepala...</p>
    <h3 class="mt-6 bg-brutal-pink border-2 border-black text-white px-4 py-2 rounded-lg inline-block text-sm font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" id="nc-round"></h3>
    <div class="mt-8">
      <button class="neo-btn btn-gold cursor-pointer shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]" onclick="beginNight()">Mulai Fase Malam ➜</button>
    </div>
  </div>
</div>

<!-- NIGHT PHASE -->
<div id="night" class="screen">
  <div class="narrow-container fade-up" style="max-width: 600px;">
    <h3 id="night-label" class="bg-black text-white border-2 border-black px-4 py-1.5 rounded-full inline-block text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">MALAM — FASE 1</h3>
    <h2 id="night-title" class="font-black text-3xl mt-4">—</h2>
    <div class="div"></div>
    <div id="night-body" class="mt-6"></div>
  </div>
</div>

<!-- DAWN / RESULT -->
<div id="dawn" class="screen">
  <div class="narrow-container fade-up">
    <span class="text-7xl block mb-4 drop-shadow-[4px_4px_0px_rgba(0,0,0,1)]">🌅</span>
    <h2 class="text-black font-black">Fajar Tiba</h2>
    <div class="div"></div>
    <div id="dawn-body" class="mt-6 glass-panel text-left"></div>
    <div id="dawn-log" class="mt-6 text-left text-xs text-black font-black font-mono border-2 border-black bg-[#FAF6EE] p-4 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hidden max-h-48 overflow-y-auto"></div>
    <div class="mt-8 flex-center" id="dawn-actions"></div>
  </div>
</div>

<!-- HUNTER -->
<div id="hunter" class="screen">
  <div class="narrow-container fade-up">
    <span class="text-7xl block mb-4 drop-shadow-[4px_4px_0px_rgba(0,0,0,1)]">🏹</span>
    <h2 class="text-black font-black">Hunter Menembak!</h2>
    <div class="div"></div>
    <p class="mt-4 font-black uppercase text-xl" id="hunter-label" style="color: #FF007A;"></p>
    <p class="mt-4 font-bold text-slate-800">Pilih satu pemain untuk ikut gugur bersamamu:</p>
    <div class="psel-grid" id="hunter-targets"></div>
    <div class="mt-6 flex-center">
      <button class="neo-btn btn-ghost cursor-pointer shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="skipHunter()">Lewati (Tidak Menembak)</button>
    </div>
  </div>
</div>

<!-- DAY VOTING -->
<div id="day" class="screen">
  <div class="container fade-up" style="max-width: 600px;">
    <div class="text-center">
      <h3 id="day-label" class="bg-brutal-yellow border-2 border-black px-4 py-1.5 rounded-full inline-block text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">SIANG — RONDE 1</h3>
      <h2 class="font-black text-3xl mt-4">☀️ Diskusi & Voting</h2>
      <div class="div"></div>
      <div class="status-bar mt-4" id="day-status"></div>
    </div>
    
    <div class="glass-panel mt-6">
      <p class="text-center mb-6 font-black text-sm uppercase tracking-wider">📢 Diskusikan & beri suara siapa yang akan dieksekusi:</p>
      <div id="vote-list"></div>
    </div>
    
    <div class="mt-8 flex-center">
      <button class="neo-btn btn-red cursor-pointer shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]" onclick="confirmVote()">⚖️ Eksekusi Tervoting</button>
      <button class="neo-btn btn-ghost cursor-pointer shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="skipDay()">Lewati Voting</button>
    </div>
  </div>
</div>

<!-- GAMEOVER -->
<div id="gameover" class="screen">
  <div class="container fade-up text-center">
    <span id="go-icon" class="text-8xl block mb-6 drop-shadow-[5px_5px_0px_rgba(0,0,0,1)]">🐺</span>
    <h1 id="go-title" class="font-black text-4xl md:text-6xl text-black">SERIGALA MENANG!</h1>
    <div class="div" style="width: 140px;"></div>
    <p class="mt-4 font-black italic text-lg max-w-lg mx-auto" id="go-sub" style="color: #333333;"></p>
    
    <div class="grid-2 mt-8 text-left">
      <div class="glass-panel">
        <h3 class="border-b-2 border-black pb-2 font-black uppercase text-[#00FF66]" style="text-shadow:1px 1px 0px #000">🏆 Yang Bertahan Hidup</h3>
        <div id="go-survivors" class="mt-4 flex flex-wrap gap-2"></div>
      </div>
      <div class="glass-panel">
        <h3 class="border-b-2 border-black pb-2 font-black uppercase text-slate-700">💀 Identitas Semua Pemain</h3>
        <div id="go-roles" class="mt-4 flex flex-col gap-3 max-h-[350px] overflow-y-auto pr-2"></div>
      </div>
    </div>
    
    <div class="mt-10 border-t-2 border-black border-dashed pt-6">
      <button class="neo-btn btn-gold cursor-pointer shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] py-4 text-lg px-12" onclick="restartGame()">
        🔄 Main Lagi ➜
      </button>
    </div>
  </div>
</div>

<!-- DIALOG MODAL SYSTEM -->
<div class="modal-bg" id="modal-bg">
  <div class="modal-box">
    <div id="modal-body" class="font-black text-black text-lg"></div>
    <div class="flex-center mt-6" id="modal-btns"></div>
  </div>
</div>

<script>
/* ============================================================
   DATA ROLES (24 ROLES)
 ============================================================ */
const ROLES = {
  werewolf:{ name:'Werewolf', emoji:'🐺', color:'#FF007A', team:'wolf', desc:'Setiap malam, bersama sesama serigala pilih korban untuk dibunuh.' },
  villager:{ name:'Villager', emoji:'👨‍🌾', color:'#333333', team:'village', desc:'Warga biasa. Gunakan logika dan debat untuk mendeteksi serigala.' },
  seer:{ name:'Seer (Peramal)', emoji:'🔮', color:'#00F0FF', team:'village', desc:'Setiap malam memeriksa identitas pemain (Serigala / Bukan).' },
  doctor:{ name:'Doctor (Dokter)', emoji:'💉', color:'#00FF66', team:'village', desc:'Setiap malam melindungi satu pemain dari serangan.' },
  hunter:{ name:'Hunter (Pemburu)', emoji:'🏹', color:'#FFF000', team:'village', desc:'Saat mati, boleh menembak satu pemain lain untuk ikut gugur bersamanya.' },
  witch:{ name:'Witch (Penyihir)', emoji:'🧙', color:'#9E00FF', team:'village', desc:'Punya 1x ramuan penyembuh dan 1x racun mematikan.' },
  fool:{ name:'Fool (Orang Gila)', emoji:'🤡', color:'#FFF000', team:'neutral', desc:'Ingin mati! Menang seketika jika dieksekusi melalui voting siang hari.' },
  mayor:{ name:'Mayor (Walikota)', emoji:'🎩', color:'#00F0FF', team:'village', desc:'Pemimpin berwibawa. Suara Anda dalam voting bernilai 2 (beritahu admin).' },
  sorcerer:{ name:'Sorcerer (Dukun)', emoji:'🦹‍♂️', color:'#9E00FF', team:'wolf', desc:'Membantu Serigala! Tiap malam memeriksa 1 orang mencari Peramal asli.' },
  lycan:{ name:'Lycan (Kutukan)', emoji:'🐺👤', color:'#333333', team:'village', desc:'Dipihak Warga. Namun jika diterawang Peramal, terlihat sebagai Serigala!' },
  
  serial_killer:{ name:'Serial Killer', emoji:'🔪', color:'#FF007A', team:'neutral', desc:'Pembunuh Berantai! Bangun tiap malam membunuh 1 orang. Menang jika bertahan sendirian.' },
  cursed:{ name:'Cursed (Terkutuk)', emoji:'🧟', color:'#333333', team:'village', desc:'Warga biasa. Jika diserang oleh Serigala, Anda tidak mati, melainkan BERUBAH menjadi Serigala!' },
  mason:{ name:'Mason (Pekerja)', emoji:'👷', color:'#333333', team:'village', desc:'Warga rahasia. Anda akan saling mengetahui siapa saja Mason lainnya sejak awal.' },
  apprentice_seer:{ name:'Murid Peramal', emoji:'👁️', color:'#00F0FF', team:'village', desc:'Warga biasa. Namun jika Peramal (Seer) mati, Anda akan menggantikannya menerawang tiap malam.' },
  cupid:{ name:'Cupid (Kupidon)', emoji:'🏹💕', color:'#FFF000', team:'village', desc:'Bangun di Malam 1 memilih 2 Kekasih. Jika salah satu mati, pasangannya ikut mati karena patah hati.' },
  bodyguard:{ name:'Bodyguard', emoji:'🛡️', color:'#00FF66', team:'village', desc:'Melindungi 1 orang tiap malam. Jika orang itu diserang Serigala/Pembunuh, ANDA yang mati.' },
  
  wolf_cub:{ name:'Anak Serigala', emoji:'🐺👶', color:'#FF007A', team:'wolf', desc:'Tim Serigala. Jika kamu mati, Serigala akan sangat marah dan membunuh 2 orang di malam berikutnya!' },
  gunner:{ name:'Gunner (Penembak)', emoji:'🔫', color:'#FFF000', team:'village', desc:'Memiliki 1 peluru. Bangun di malam hari untuk menembak mati 1 orang (bisa disimpan).' },
  priest:{ name:'Priest (Pendeta)', emoji:'✝️', color:'#00FF66', team:'village', desc:'Memiliki 1x Air Suci. Jika disiram ke Serigala, Serigala mati. Jika disiram ke Warga, ANDA yang mati.' },
  diseased:{ name:'Diseased (Penyakitan)', emoji:'🤢', color:'#333333', team:'village', desc:'Warga biasa. Jika Serigala memangsamu, mereka akan sakit dan tidak bisa memangsa siapapun di malam berikutnya.' },
  tough_guy:{ name:'Pria Tangguh', emoji:'🥊', color:'#333333', team:'village', desc:'Sangat kuat! Jika diserang Serigala, kamu bertahan 1 hari dan baru gugur di pagi hari berikutnya.' },
  traitor:{ name:'Traitor (Pengkhianat)', emoji:'🐀', color:'#FF007A', team:'wolf', desc:'Warga yang memihak Serigala (diam-diam). Jika semua Serigala mati, KAMU akan menjadi Serigala baru.' },
  cult_leader:{ name:'Ketua Sekte', emoji:'🛐', color:'#9E00FF', team:'neutral', desc:'Bangun tiap malam untuk mencuci otak 1 orang. Menang jika seluruh orang yang hidup adalah pengikutmu.' },
  doppelganger:{ name:'Doppelganger', emoji:'🎭', color:'#FFF000', team:'neutral', desc:'Bangun di Malam 1 untuk memilih target. Jika targetmu mati, kamu akan mendapatkan dan menggantikan perannya!' }
};

/* ============================================================
   STATE
 ============================================================ */
let G = {
  players:[], 
  playerNames:[],
  roles:{ werewolf:2, villager:1, seer:1, doctor:1, hunter:0, witch:0, fool:0, mayor:1, sorcerer:1, lycan:0, serial_killer:0, cursed:0, mason:0, apprentice_seer:0, cupid:0, bodyguard:0, wolf_cub:0, gunner:0, priest:0, diseased:0, tough_guy:0, traitor:0, cult_leader:0, doppelganger:0 },
  playerCount:8, round:1,
  
  wolfKills:[], doctorSave:null, bodyguardSave:null, serialKill:null,
  witchHeal:false, witchPoison:null, gunnerKill:null, priestKill:null, cultTarget:null, doppelTarget:null,
  
  witchHealUsed:false, witchPoisonUsed:false, gunnerUsed:false, priestUsed:false,
  lovers:[], cultists:[], toughGuyDying:null, wolfDoubleKill:false, wolvesStarved:false,
  
  nightPhase:null, revealIdx:0, revealShown:false, votes:{}, pendingHunter:null, hunterCtx:null, modalCbs:{},
  log: []
};

const BGM = new Audio('https://www.soundhelix.com/examples/mp3/SoundHelix-Song-15.mp3'); // Ambient track
BGM.loop = true;
let bgmOn = false;

/* ============================================================
   UTILITIES
 ============================================================ */
function esc(s){ return s.replace(/'/g,"\\'"); }
function showScreen(id){ 
  document.querySelectorAll('.screen').forEach(s=>s.classList.remove('active')); 
  document.getElementById(id).classList.add('active'); 
  window.scrollTo(0,0); 
  
  const endBtn = document.getElementById('end-game-btn');
  if(endBtn) endBtn.style.display = (id === 'setup') ? 'none' : 'block';
}

function endGameKeepSetup() {
  if(confirm('Akhiri permainan saat ini dan kembali ke pengaturan awal dengan daftar pemain yang sama?')) {
    showScreen('setup');
  }
}

function showModal(html, btns){
  document.getElementById('modal-body').innerHTML = html;
  const bc = document.getElementById('modal-btns'); bc.innerHTML = '';
  btns.forEach((b,i)=>{
    G.modalCbs['m'+i] = b.fn;
    const el = document.createElement('button'); el.className = 'neo-btn '+b.cls;
    el.textContent = b.label; el.onclick = ()=>{ G.modalCbs['m'+i](); };
    bc.appendChild(el);
  });
  document.getElementById('modal-bg').classList.add('active');
}
function closeModal(){ document.getElementById('modal-bg').classList.remove('active'); }

function logEvent(txt){
  G.log.push(`Ronde ${G.round}: ${txt}`);
}

/* ============================================================
   SETUP
 ============================================================ */
function totalRoles(){ return Object.values(G.roles).reduce((a,b)=>a+b,0); }

function autoBalanceRoles() {
  const n = G.playerCount;
  const keys = Object.keys(G.roles);
  keys.forEach(k => G.roles[k] = 0);
  
  let wolves = Math.max(1, Math.floor(n / 4));
  let seer = 1;
  let doctor = 1;
  
  G.roles.werewolf = wolves;
  G.roles.seer = seer;
  G.roles.doctor = doctor;
  
  let allocated = wolves + seer + doctor;
  
  if (n >= 6 && allocated < n) { G.roles.mayor = 1; allocated++; }
  if (n >= 8 && allocated < n) { G.roles.sorcerer = 1; allocated++; }
  if (n >= 10 && allocated < n) { G.roles.hunter = 1; allocated++; }
  if (n >= 12 && allocated < n) { G.roles.witch = 1; allocated++; }
  if (n >= 14 && allocated < n) { G.roles.fool = 1; allocated++; }
  if (n >= 16 && allocated < n) { G.roles.serial_killer = 1; allocated++; }
  if (n >= 18 && allocated < n) { G.roles.bodyguard = 1; allocated++; }
  if (n >= 20 && allocated < n) { G.roles.cupid = 1; allocated++; }
  
  if (allocated < n) {
    G.roles.villager = n - allocated;
  } else if (allocated > n) {
    G.roles.villager = 0;
    let sum = totalRoles();
    while (sum > n) {
      if (G.roles.werewolf > 1) { G.roles.werewolf--; }
      else if (G.roles.sorcerer > 0) { G.roles.sorcerer--; }
      else if (G.roles.mayor > 0) { G.roles.mayor--; }
      else if (G.roles.doctor > 1) { G.roles.doctor--; }
      else if (G.roles.seer > 1) { G.roles.seer--; }
      else { break; }
      sum = totalRoles();
    }
  }
}

function changePC(d){
  if(d > 0) {
    G.playerNames.push(`Pemain ${G.playerNames.length + 1}`);
  } else if(d < 0 && G.playerNames.length > 4) {
    G.playerNames.pop();
  }
  G.playerCount = G.playerNames.length;
  localStorage.setItem('devtronix_ww_names', JSON.stringify(G.playerNames));
  autoBalanceRoles();
  renderSetup();
}

function deletePlayer(i) {
  if(G.playerNames.length <= 4) {
    showModal('<p class="text-center font-black">Minimal jumlah pemain adalah 4 orang!</p>', [{label:'Mengerti', cls:'btn-gold', fn:closeModal}]);
    return;
  }
  G.playerNames.splice(i, 1);
  G.playerCount = G.playerNames.length;
  localStorage.setItem('devtronix_ww_names', JSON.stringify(G.playerNames));
  autoBalanceRoles();
  renderSetup();
}

function addPlayer() {
  if(G.playerNames.length >= 30) {
    showModal('<p class="text-center font-black">Maksimal jumlah pemain adalah 30 orang!</p>', [{label:'Mengerti', cls:'btn-gold', fn:closeModal}]);
    return;
  }
  G.playerNames.push(`Pemain Baru`);
  G.playerCount = G.playerNames.length;
  localStorage.setItem('devtronix_ww_names', JSON.stringify(G.playerNames));
  autoBalanceRoles();
  renderSetup();
}

function resetToDefault() {
  if(confirm('Apakah Anda yakin ingin mereset seluruh daftar nama ke daftar default bawaan kode? Perubahan nama yang Anda buat saat ini akan hilang.')) {
    localStorage.removeItem('devtronix_ww_names');
    localStorage.removeItem('devtronix_ww_names_v2');
    location.reload();
  }
}

function updatePlayerName(i, val) {
  G.playerNames[i] = val.trim();
  localStorage.setItem('devtronix_ww_names', JSON.stringify(G.playerNames));
}

function changeRole(r,d){ const nv = G.roles[r]+d; if(r==='werewolf' && nv<1) return; if(nv<0) return; G.roles[r] = nv; renderSetup(); }

function renderSetup(){
  document.getElementById('pc-display').textContent = G.playerCount;
  
  const ng = document.getElementById('names-grid');
  ng.innerHTML = '';
  G.playerNames.forEach((name, i) => {
    const w = document.createElement('div');
    w.className = 'name-wrap flex items-center justify-between gap-2 bg-[#FAF6EE] border-2 border-black rounded-lg p-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]';
    w.innerHTML = `
      <span class="name-num font-black text-xs text-black w-6">${i+1}.</span>
      <input type="text" class="bg-transparent border-none text-black font-bold outline-none flex-1 py-1" placeholder="Pemain ${i+1}" value="${name}" oninput="updatePlayerName(${i}, this.value)">
      <button class="text-xs bg-brutal-pink text-white border-2 border-black rounded px-2 py-1 font-black cursor-pointer shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF3399] active:translate-y-[1px]" onclick="deletePlayer(${i})">
        🗑️
      </button>
    `;
    ng.appendChild(w);
  });
  
  const rc = document.getElementById('roles-cfg');
  const rolesArr = Object.keys(ROLES);
  rc.innerHTML = rolesArr.map(r=>{
    const d=ROLES[r];
    return `<div class="role-row">
      <div class="role-rname"><span>${d.emoji}</span><span style="color:${d.color}">${d.name}</span></div>
      <div class="role-ctrl"><button class="rb" onclick="changeRole('${r}',-1)">−</button><span class="role-cnt">${G.roles[r]}</span><button class="rb" onclick="changeRole('${r}',1)">+</button></div>
    </div>`;
  }).join('');
  
  const lg = document.querySelector('.panduan-grid');
  lg.innerHTML = rolesArr.map(r=>{
    const d=ROLES[r];
    return `<div class="legend-item"><span class="l-emoji">${d.emoji}</span><div><div class="l-name" style="color:${d.color}">${d.name}</div><div class="l-desc">${d.desc}</div></div></div>`;
  }).join('');

  const tot = totalRoles(), n=G.playerCount, diff=n-tot, ss = document.getElementById('sum-status'), sb = document.getElementById('start-btn');
  if(diff===0){ ss.textContent='✓ Sesuai'; ss.style.color='#00FF66'; ss.style.backgroundColor='#ffffff'; ss.style.borderColor='#000000'; sb.disabled=false; }
  else if(diff>0){ ss.textContent=`⚠ Kurang ${diff} Role`; ss.style.color='#FF007A'; ss.style.backgroundColor='#ffffff'; ss.style.borderColor='#000000'; sb.disabled=true; }
  else{ ss.textContent=`⚠ Kelebihan ${-diff} Role`; ss.style.color='#FF007A'; ss.style.backgroundColor='#ffffff'; ss.style.borderColor='#000000'; sb.disabled=true; }
}

function startGame(){
  if(totalRoles()!==G.playerCount) return;
  const names = G.playerNames.map((n,i)=>n.trim()||`Pemain ${i+1}`);
  
  // Simpan nama pemain ke localStorage
  localStorage.setItem('devtronix_ww_names', JSON.stringify(names));
  
  const roleList=[]; Object.entries(G.roles).forEach(([r,c])=>{ for(let i=0;i<c;i++) roleList.push(r); });
  for(let i=roleList.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [roleList[i],roleList[j]]=[roleList[j],roleList[i]]; }
  
  G.players = names.map((name,i)=>({name, role:roleList[i], originalRole:roleList[i], alive:true, cursedTurned:false}));
  G.round=1; G.revealIdx=0; G.revealShown=false; G.log=[];
  
  G.witchHealUsed=false; G.witchPoisonUsed=false; G.gunnerUsed=false; G.priestUsed=false;
  G.lovers=[]; G.cultists=[]; G.toughGuyDying=null; G.wolfDoubleKill=false; G.wolvesStarved=false;
  
  showReveal();
}

function toggleBGM() {
  bgmOn = !bgmOn;
  const btn = document.getElementById('bgm-btn');
  if(bgmOn) {
    BGM.play().catch(e=>console.log("Audio block:", e));
    btn.innerHTML = '🔊 MUSIC ON';
    btn.style.backgroundColor = '#00FF66';
  } else {
    BGM.pause();
    btn.innerHTML = '🔇 MUSIC OFF';
    btn.style.backgroundColor = '#ffffff';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const defaultList = [
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
  
  const v2_flag = localStorage.getItem('devtronix_ww_names_v2');
  const saved = localStorage.getItem('devtronix_ww_names');
  
  if(v2_flag && saved) {
    try {
      G.playerNames = JSON.parse(saved);
    } catch(e) {
      G.playerNames = [...defaultList];
    }
  } else {
    // Paksa muat daftar 24 pemain baru berhuruf kecil
    G.playerNames = [...defaultList];
    localStorage.setItem('devtronix_ww_names', JSON.stringify(G.playerNames));
    localStorage.setItem('devtronix_ww_names_v2', 'true');
  }
  
  G.playerCount = G.playerNames.length;
  autoBalanceRoles();
  renderSetup();
});

/* ============================================================
   ROLE REVEAL
 ============================================================ */
function showReveal(){
  showScreen('reveal'); const i=G.revealIdx, p=G.players[i], n=G.playerCount;
  document.getElementById('rev-label').textContent=`PEMAIN ${i+1} DARI ${n}`;
  document.getElementById('rev-pname').textContent=p.name;
  const cont=document.getElementById('rev-content'), btns=document.getElementById('rev-btns');

  if(!G.revealShown){
    document.getElementById('rev-hint').textContent='Berikan perangkat ke pemain ini. Rahasiakan peranmu!';
    cont.innerHTML=`<div style="cursor:pointer; padding: 40px 20px; text-align:center;" onclick="showRoleCard()"><span style="font-size:4.5rem; display:block; margin-bottom:15px; filter: drop-shadow(2px 2px 0px #000)">🎴</span><p style="color:#000000; font-weight:900; text-transform:uppercase; letter-spacing:0.05em; background:#FFF000; display:inline-block; border:2px solid #000; padding:6px 12px; rounded-lg; shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]">Ketuk untuk melihat peran</p></div>`;
    btns.innerHTML=`<button class="neo-btn btn-gold cursor-pointer" onclick="showRoleCard()">👁 Lihat Peranku</button>`;
  } else {
    document.getElementById('rev-hint').textContent='Ingat peranmu! Jangan perlihatkan ke siapapun.';
    const r=ROLES[p.role];
    let teamTag = p.role==='fool'||p.role==='serial_killer'||p.role==='cult_leader'||p.role==='doppelganger' ? '<span class="tag" style="border-color:#000; background:#FFF000; color:#000;">Tim Netral ⚪</span>' : 
                  (r.team==='wolf' ? '<span class="tag" style="border-color:#000; background:#FF007A; color:#fff">Tim Serigala 🐺</span>' : '<span class="tag" style="border-color:#000; background:#00FF66; color:#000">Tim Warga 🏘️</span>');
    
    let extra='';
    if(p.role==='werewolf' || p.role==='sorcerer' || p.role==='wolf_cub'){
      const allies=G.players.filter((pl,idx)=>(pl.role==='werewolf'||pl.role==='wolf_cub')&&idx!==i);
      if(allies.length) extra=`<div class="info-box mt-4" style="background:#FF007A; color:white; border-color:#000;">🐺 Serigala lainnya: <strong>${allies.map(a=>a.name).join(', ')}</strong></div>`;
    }
    if(p.role==='mason'){
      const allies=G.players.filter((pl,idx)=>pl.role==='mason'&&idx!==i);
      if(allies.length) extra=`<div class="info-box mt-4" style="background:#FAF6EE; border-color:#000;">👷 Rekan Mason Anda: <strong>${allies.map(a=>a.name).join(', ')}</strong></div>`;
      else extra=`<div class="info-box mt-4">Anda adalah satu-satunya Mason di desa ini.</div>`;
    }

    cont.innerHTML=`
      <div class="fade-up text-center">
        <span style="font-size:4.5rem; display:block; margin-bottom:10px; filter:drop-shadow(2px 2px 0px #000)">${r.emoji}</span>
        <h2 style="color:${r.color}; font-size:2rem; text-shadow:1.5px 1.5px 0px #000">${r.name}</h2>
        <div class="mt-4">${teamTag}</div>
        <p class="mt-4 font-bold text-slate-800" style="font-size:1.05rem;">${r.desc}</p>
        ${extra}
      </div>`;
      
    if(i<n-1) btns.innerHTML=`<button class="neo-btn btn-gold cursor-pointer" onclick="nextReveal()">Pemain Selanjutnya →</button>`;
    else btns.innerHTML=`<button class="neo-btn btn-blue cursor-pointer" onclick="goNightCover()">🌙 Mulai Malam Pertama</button>`;
  }
}
function showRoleCard(){ G.revealShown=true; showReveal(); }
function nextReveal(){ G.revealIdx++; G.revealShown=false; showReveal(); }
function goNightCover(){ showScreen('nightcover'); document.getElementById('nc-round').textContent=`RONDE ${G.round}`; }

/* ============================================================
   NIGHT PHASE
 ============================================================ */
function beginNight(){ 
  G.wolfKills=[]; G.doctorSave=null; G.bodyguardSave=null; G.serialKill=null;
  G.witchHeal=false; G.witchPoison=null; G.gunnerKill=null; G.priestKill=null; G.cultTarget=null;
  
  const phases = getNightPhases();
  if(phases.length === 0) { processDawn(); return; }
  G.nightPhase = phases[0]; 
  renderNight(); 
}

function getNightPhases(){
  const ph = [];
  if(G.round === 1 && G.players.some(p=>p.role==='doppelganger' && p.alive)) ph.push('doppelganger');
  if(G.round === 1 && G.players.some(p=>p.role==='cupid' && p.alive)) ph.push('cupid');
  if(G.players.some(p=>p.role==='bodyguard' && p.alive)) ph.push('bodyguard');
  
  if(G.players.some(p=>(p.role==='werewolf' || p.role==='wolf_cub' || p.cursedTurned) && p.alive)) ph.push('werewolf');
  if(G.players.some(p=>p.role==='cult_leader' && p.alive)) ph.push('cult_leader');
  if(G.players.some(p=>p.role==='serial_killer' && p.alive)) ph.push('serial_killer');
  if(G.players.some(p=>p.role==='sorcerer' && p.alive)) ph.push('sorcerer');
  
  const seerAlive = G.players.some(p=>p.role==='seer' && p.alive);
  const appSeerAlive = G.players.some(p=>p.role==='apprentice_seer' && p.alive);
  if(seerAlive) ph.push('seer');
  else if(appSeerAlive) ph.push('apprentice_seer');
  
  if(G.players.some(p=>p.role==='doctor' && p.alive)) ph.push('doctor');
  if(G.players.some(p=>p.role==='witch' && p.alive)) ph.push('witch');
  if(G.players.some(p=>p.role==='priest' && p.alive)) ph.push('priest');
  if(G.players.some(p=>p.role==='gunner' && p.alive)) ph.push('gunner');
  
  return ph;
}

function renderNight(){
  showScreen('night'); const phases=getNightPhases(), ph=G.nightPhase, idx=phases.indexOf(ph), body=document.getElementById('night-body');
  document.getElementById('night-label').textContent=`MALAM RONDE ${G.round} — FASE ${idx+1}/${phases.length}`;
  const aliveTargets = G.players.filter(p=>p.alive);

  if(ph==='doppelganger'){
    document.getElementById('night-title').textContent='🎭 Doppelganger';
    body.innerHTML=`
      <div class="info-box">Bangunkan <strong>Doppelganger</strong>.</div>
      <p class="mt-4 text-center font-bold">Pilih 1 orang. Jika dia mati (kapanpun itu), kamu akan mengambil identitas dan perannya:</p>
      <div class="psel-grid">${aliveTargets.filter(p=>p.role!=='doppelganger').map(p=>`<button class="psel" onclick="selectSingle('doppelTarget', '${esc(p.name)}', this, 'dop-ok')">${p.name}</button>`).join('')}</div>
      <div class="flex-center mt-6"><button class="neo-btn btn-gold cursor-pointer" id="dop-ok" onclick="nextNightPhase()" disabled>🎭 Targetkan</button></div>`;
  }
  else if(ph==='cupid'){
    document.getElementById('night-title').textContent='🏹💕 Kupidon Beraksi';
    body.innerHTML=`
      <div class="info-box">Bangunkan <strong>Cupid (Kupidon)</strong>.</div>
      <p class="mt-4 text-center font-bold">Pilih 2 pemain untuk dijadikan Kekasih sehidup semati:</p>
      <div class="psel-grid" id="cupid-targets">${aliveTargets.map(p=>`<button class="psel" onclick="selectMulti('lovers', '${esc(p.name)}', this, 'cupid-ok', 2)">${p.name}</button>`).join('')}</div>
      <div class="flex-center mt-6"><button class="neo-btn btn-gold cursor-pointer" id="cupid-ok" onclick="nextNightPhase()" disabled>💕 Konfirmasi (Pilih 2)</button></div>`;
  }
  else if(ph==='bodyguard'){
    document.getElementById('night-title').textContent='🛡️ Pengawal Bertugas';
    body.innerHTML=`
      <div class="info-box">Bangunkan <strong>Bodyguard</strong>.</div>
      <p class="mt-4 text-center font-bold">Pilih 1 pemain untuk dilindungi nyawanya (kecuali diri sendiri):</p>
      <div class="psel-grid">${aliveTargets.filter(p=>p.role!=='bodyguard').map(p=>`<button class="psel" onclick="selectSingle('bodyguardSave', '${esc(p.name)}', this, 'bg-ok')">${p.name}</button>`).join('')}</div>
      <div class="flex-center mt-6"><button class="neo-btn btn-green cursor-pointer" id="bg-ok" onclick="nextNightPhase()" disabled>🛡️ Konfirmasi</button></div>`;
  }
  else if(ph==='werewolf'){
    document.getElementById('night-title').textContent='🐺 Serigala Memburu';
    const wolves = G.players.filter(p=>(p.role==='werewolf'||p.role==='wolf_cub'||p.cursedTurned)&&p.alive);
    if(G.wolvesStarved) {
       body.innerHTML=`
         <div class="info-box" style="background:#FF007A; color:white;">Bangunkan <strong>${wolves.map(w=>w.name).join(', ')}</strong> (Werewolf).</div>
         <p class="mt-4 text-center font-black" style="color:#FF007A;">Kalian memakan daging "Diseased" semalam!<br>Kalian terlalu sakit untuk berburu malam ini.</p>
         <div class="flex-center mt-6"><button class="neo-btn btn-ghost cursor-pointer" onclick="nextNightPhase()">Tutup Mata (Lewati Malam)</button></div>`;
    } else {
       const maxKills = G.wolfDoubleKill ? 2 : 1;
       body.innerHTML=`
         <div class="info-box" style="background:#FF007A; color:white;">Bangunkan <strong>${wolves.map(w=>w.name).join(', ')}</strong> (Werewolf).</div>
         <p class="mt-4 text-center font-bold">Pilih <strong style="color:#FF007A">${maxKills}</strong> korban malam ini ${G.wolfDoubleKill?'(Balas dendam Anak Serigala!)':''}:</p>
         <div class="psel-grid">${aliveTargets.filter(p=>p.role!=='werewolf'&&p.role!=='wolf_cub'&&!p.cursedTurned).map(p=>`<button class="psel" onclick="selectMulti('wolfKills', '${esc(p.name)}', this, 'wolf-ok', ${maxKills})">${p.name}</button>`).join('')}</div>
         <div class="flex-center mt-6"><button class="neo-btn btn-red cursor-pointer" id="wolf-ok" onclick="nextNightPhase()" disabled>🐺 Eksekusi Korban</button></div>`;
    }
  }
  else if(ph==='cult_leader'){
    document.getElementById('night-title').textContent='🛐 Sekte Sesat';
    body.innerHTML=`
      <div class="info-box" style="background:#9E00FF; color:white;">Bangunkan <strong>Ketua Sekte</strong>.</div>
      <p class="mt-4 text-center font-bold">Pilih 1 orang untuk dicuci otaknya (direkrut ke Sekte):</p>
      <div class="psel-grid">${aliveTargets.filter(p=>p.role!=='cult_leader'&&!G.cultists.includes(p.name)).map(p=>`<button class="psel" onclick="selectSingle('cultTarget', '${esc(p.name)}', this, 'cult-ok')">${p.name}</button>`).join('')}</div>
      <div class="flex-center mt-6"><button class="neo-btn btn-gold cursor-pointer" id="cult-ok" onclick="nextNightPhase()" disabled>🛐 Rekrut</button></div>`;
  }
  else if(ph==='serial_killer'){
    document.getElementById('night-title').textContent='🔪 Pembunuh Berantai';
    body.innerHTML=`
      <div class="info-box" style="background:#FF007A; color:white;">Bangunkan <strong>Serial Killer</strong>.</div>
      <p class="mt-4 text-center font-bold">Pilih 1 orang untuk dibunuh tanpa ampun:</p>
      <div class="psel-grid">${aliveTargets.filter(p=>p.role!=='serial_killer').map(p=>`<button class="psel" onclick="selectSingle('serialKill', '${esc(p.name)}', this, 'sk-ok')">${p.name}</button>`).join('')}</div>
      <div class="flex-center mt-6"><button class="neo-btn btn-red cursor-pointer" id="sk-ok" onclick="nextNightPhase()" disabled>🔪 Bunuh</button></div>`;
  }
  else if(ph==='sorcerer'){
    document.getElementById('night-title').textContent='🦹‍♂️ Dukun Mencari';
    body.innerHTML=`
      <div class="info-box" style="background:#9E00FF; color:white;">Bangunkan <strong>Sorcerer</strong>.</div>
      <p class="mt-4 text-center font-bold">Pilih 1 orang untuk dicek apakah dia Peramal (Seer) asli:</p>
      <div class="psel-grid">${aliveTargets.filter(p=>p.role!=='sorcerer'&&p.role!=='werewolf'&&p.role!=='wolf_cub'&&!p.cursedTurned).map(p=>`<button class="psel" onclick="doCheck('sorc', '${esc(p.name)}')">${p.name}</button>`).join('')}</div>`;
  }
  else if(ph==='seer' || ph==='apprentice_seer'){
    const roleName = ph==='seer' ? 'Seer (Peramal)' : 'Murid Peramal';
    document.getElementById('night-title').textContent='🔮 Terawangan Mistis';
    body.innerHTML=`
      <div class="info-box" style="background:#00F0FF; color:black;">Bangunkan <strong>${roleName}</strong>.</div>
      <p class="mt-4 text-center font-bold">Pilih 1 pemain untuk melihat apakah ia Serigala atau bukan:</p>
      <div class="psel-grid">${aliveTargets.filter(p=>p.role!==ph).map(p=>`<button class="psel" onclick="doCheck('seer', '${esc(p.name)}')">${p.name}</button>`).join('')}</div>`;
  }
  else if(ph==='doctor'){
    document.getElementById('night-title').textContent='💉 Dokter Menyelamatkan';
    body.innerHTML=`
      <div class="info-box" style="background:#00FF66; color:black;">Bangunkan <strong>Doctor</strong>.</div>
      <p class="mt-4 text-center font-bold">Pilih 1 pemain untuk disuntik penawar (kebal serangan malam):</p>
      <div class="psel-grid">${aliveTargets.map(p=>`<button class="psel" onclick="selectSingle('doctorSave', '${esc(p.name)}', this, 'doc-ok')">${p.name}${p.role==='doctor'?' (Diri Sendiri)':''}</button>`).join('')}</div>
      <div class="flex-center mt-6"><button class="neo-btn btn-green cursor-pointer" id="doc-ok" onclick="nextNightPhase()" disabled>💉 Suntik Penawar</button></div>`;
  }
  else if(ph==='witch'){
    document.getElementById('night-title').textContent='🧙 Ramuan Penyihir';
    let victims = [];
    if(G.wolfKills && G.wolfKills.length>0) victims.push(...G.wolfKills);
    if(G.serialKill) victims.push(G.serialKill);
    victims = [...new Set(victims)]; 
    
    const healOk = !G.witchHealUsed && victims.length > 0;
    const poisonOk = !G.witchPoisonUsed;
    let victimText = victims.length > 0 ? `Korban gigitan/tusukan malam ini: <strong>${victims.join(' & ')}</strong>` : 'Tidak ada korban sejauh ini.';
    let healBtnText = victims.length > 0 ? `Sembuhkan Semua Korban` : `Simpan Ramuan Penyembuh`;
    
    body.innerHTML=`
      <div class="info-box" style="background:#9E00FF; color:white;">Bangunkan <strong>Witch (Penyihir)</strong>.</div>
      <div class="ann ${victims.length>0?'ann-dead':'ann-safe'} mt-4">${victimText}</div>
      
      <div class="grid-2 mt-4">
        <button class="psel text-left flex items-center gap-3 p-4" ${!healOk?'disabled':''} onclick="toggleWitchHeal(this)">
          <span class="text-3xl">🧪</span>
          <div><div class="font-black text-black" style="color:#00FF66">Ramuan Penyembuh</div><div style="font-size:0.85rem; color:#444;">${healBtnText}</div></div>
        </button>
      </div>
      
      ${poisonOk ? `
        <p class="mt-6 text-center font-bold">Gunakan racun (Opsional):</p>
        <div class="psel-grid" id="witch-poison-targets">${aliveTargets.map(p=>`<button class="psel" onclick="selectSingle('witchPoison', '${esc(p.name)}', this, null, '#witch-poison-targets')">${p.name}</button>`).join('')}</div>
      ` : `<div class="info-box mt-6" style="opacity:0.5; background:gray;">🧪 Ramuan Racun sudah habis</div>`}
      
      <div class="flex-center mt-8"><button class="neo-btn btn-gold cursor-pointer shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]" onclick="confirmWitch()">✓ Selesai (Tutup Mata)</button></div>`;
  }
  else if(ph==='gunner'){
    document.getElementById('night-title').textContent='🔫 Penembak Jitu';
    const used = G.gunnerUsed;
    body.innerHTML=`
      <div class="info-box">Bangunkan <strong>Gunner</strong>.</div>
      ${used ? `<div class="mt-4 text-center font-bold" style="opacity:0.6;">Sisa peluru: 0. Kamu sudah menembak sebelumnya.</div>` : `
      <p class="mt-4 text-center font-bold">Kamu punya 1 peluru. Mau menembak seseorang malam ini?</p>
      <div class="psel-grid" id="gunner-targets">${aliveTargets.filter(p=>p.role!=='gunner').map(p=>`<button class="psel" onclick="selectSingle('gunnerKill', '${esc(p.name)}', this, null, '#gunner-targets')">${p.name}</button>`).join('')}</div>
      `}
      <div class="flex-center mt-6"><button class="neo-btn btn-gold cursor-pointer" onclick="${used?'':'if(G.gunnerKill) G.gunnerUsed=true;'} nextNightPhase()">Tutup Mata</button></div>`;
  }
  else if(ph==='priest'){
    document.getElementById('night-title').textContent='✝️ Pendeta (Air Suci)';
    const used = G.priestUsed;
    body.innerHTML=`
      <div class="info-box" style="background:#00FF66; color:black;">Bangunkan <strong>Pendeta</strong>.</div>
      ${used ? `<div class="mt-4 text-center font-bold" style="opacity:0.6;">Air Suci sudah dipakai.</div>` : `
      <p class="mt-4 text-center font-bold">Siram Air Suci (1x pakai). Membunuh Serigala secara instan, namun jika salah sasaran ke Warga, KAMU yang mati!</p>
      <div class="psel-grid" id="priest-targets">${aliveTargets.filter(p=>p.role!=='priest').map(p=>`<button class="psel" onclick="selectSingle('priestKill', '${esc(p.name)}', this, null, '#priest-targets')">${p.name}</button>`).join('')}</div>
      `}
      <div class="flex-center mt-6"><button class="neo-btn btn-gold cursor-pointer" onclick="${used?'':'if(G.priestKill) G.priestUsed=true;'} nextNightPhase()">Tutup Mata</button></div>`;
  }
}

function selectSingle(stateKey, name, btn, okBtnId, containerSel = '.psel-grid') {
  if(G[stateKey] === name) { G[stateKey] = null; btn.classList.remove('sel'); if(okBtnId) document.getElementById(okBtnId).disabled = true; return; }
  G[stateKey] = name;
  document.querySelectorAll(`${containerSel} .psel`).forEach(b=>b.classList.remove('sel'));
  btn.classList.add('sel');
  if(okBtnId) document.getElementById(okBtnId).disabled = false;
}

function selectMulti(stateKey, name, btn, okBtnId, maxItems) {
   if(!G[stateKey]) G[stateKey] = [];
   if(G[stateKey].includes(name)) {
      G[stateKey] = G[stateKey].filter(n=>n!==name);
      btn.classList.remove('sel');
   } else {
      if(G[stateKey].length < maxItems) {
         G[stateKey].push(name);
         btn.classList.add('sel');
      }
   }
   if(okBtnId) document.getElementById(okBtnId).disabled = (G[stateKey].length !== maxItems);
}

function doCheck(type, targetName){
  const p = G.players.find(pl=>pl.name===targetName);
  let res = "";
  if(type==='sorc') {
    res = p.role==='seer' ? "Dia adalah PERAMAL!" : "Dia BUKAN Peramal.";
    logEvent(`Sorcerer mengecek ${targetName}.`);
  } else {
    const isWolf = (p.role==='werewolf'||p.role==='wolf_cub'||p.cursedTurned);
    res = isWolf ? "Waspada! Dia adalah SERIGALA." : "Tenang, dia adalah Warga biasa.";
    logEvent(`Peramal mengecek ${targetName}.`);
  }
  showModal(`Hasil Terawangan:<br><br><h2 style="color:#00F0FF; text-shadow: 1.5px 1.5px 0px #000">${res}</h2>`, [{label:'Tutup Mata', cls:'btn-gold', fn:()=>{ closeModal(); nextNightPhase(); }}]);
}

function toggleWitchHeal(btn) { G.witchHeal = !G.witchHeal; btn.classList.toggle('sel', G.witchHeal); }
function confirmWitch(){ if(G.witchHeal) { G.witchHealUsed = true; logEvent("Penyihir menggunakan ramuan penyembuh."); } if(G.witchPoison) { G.witchPoisonUsed = true; logEvent("Penyihir menggunakan ramuan racun."); } nextNightPhase(); }

function nextNightPhase(){
  const phases = getNightPhases(), idx = phases.indexOf(G.nightPhase);
  if(idx < phases.length - 1) { G.nightPhase = phases[idx+1]; renderNight(); } else processDawn();
}

/* ============================================================
   DAWN & DAY
 ============================================================ */
function processDawn(){
  showScreen('dawn');
  let deaths = [];
  
  // 0. Tough Guy previously bitten
  if(G.toughGuyDying) {
      let tg = G.players.find(p=>p.name === G.toughGuyDying);
      if(tg && tg.alive) deaths.push({name: G.toughGuyDying, reason: 'akhirnya gugur akibat luka gigitan Serigala yang tertunda di malam sebelumnya'});
      G.toughGuyDying = null;
  }
  
  // 1. Werewolf kills
  if(G.wolvesStarved) {
      G.wolvesStarved = false; // Reset starvation
  } else {
      (G.wolfKills||[]).forEach(kill => {
         let victim = G.players.find(p=>p.name===kill);
         if(victim && victim.alive) {
            if(victim.role === 'cursed') {
               victim.cursedTurned = true; // SILENT TURN!
            } else if(victim.role === 'tough_guy') {
               G.toughGuyDying = victim.name; // Dies next dawn
            } else {
               if(G.bodyguardSave === kill) {
                  let bg = G.players.find(p=>p.role==='bodyguard');
                  if(bg && bg.alive) {
                    deaths.push({name: bg.name, reason: 'terbunuh saat melindungi targetnya dari gigitan Serigala'});
                    logEvent(`${bg.name} tewas melindungi ${kill}.`);
                  }
               } else if(!G.witchHeal && G.doctorSave !== kill) {
                  deaths.push({name: kill, reason: 'diserang dan dirobek oleh Serigala'});
                  if(victim.role === 'diseased') G.wolvesStarved = true;
                  if(victim.role === 'wolf_cub') G.wolfDoubleKill = true;
               } else if(G.doctorSave === kill) {
                  logEvent(`${kill} selamat dari serangan Serigala berkat Dokter.`);
               }
            }
         }
      });
      G.wolfDoubleKill = false; // Reset if used
  }
  G.wolfKills = [];

  // 2. Serial Killer
  if(G.serialKill) {
    let victim = G.players.find(p=>p.name===G.serialKill);
    if(victim && victim.alive) {
      if(G.bodyguardSave === G.serialKill) {
         let bg = G.players.find(p=>p.role==='bodyguard');
         if(bg && bg.alive && !deaths.find(d=>d.name===bg.name)) {
            deaths.push({name: bg.name, reason: 'terbunuh karena menahan pisau Pembunuh Berantai untuk targetnya'});
            logEvent(`${bg.name} tewas melindungi ${G.serialKill}.`);
         }
      } else if(!G.witchHeal && G.doctorSave !== G.serialKill) {
         deaths.push({name: G.serialKill, reason: 'dihabisi tanpa ampun oleh Pembunuh Berantai'});
         if(victim.role === 'wolf_cub') G.wolfDoubleKill = true;
      } else {
        logEvent(`${G.serialKill} selamat dari serangan Pembunuh Berantai.`);
      }
    }
  }
  
  // 3. Witch Poison
  if(G.witchPoison) {
      deaths.push({name: G.witchPoison, reason: 'meminum racun misterius di kegelapan'});
      let victim = G.players.find(p=>p.name===G.witchPoison);
      if(victim && victim.role === 'wolf_cub') G.wolfDoubleKill = true;
  }
  
  // 4. Gunner Kill
  if(G.gunnerKill) {
      deaths.push({name: G.gunnerKill, reason: 'tertembak peluru Gunner di tengah malam'});
      let victim = G.players.find(p=>p.name===G.gunnerKill);
      if(victim && victim.role === 'wolf_cub') G.wolfDoubleKill = true;
  }
  
  // 5. Priest Kill
  if(G.priestKill) {
      let victim = G.players.find(p=>p.name===G.priestKill);
      if(victim && victim.alive) {
         if(victim.role === 'werewolf' || victim.role === 'wolf_cub' || victim.role === 'lycan' || victim.cursedTurned) {
            deaths.push({name: G.priestKill, reason: 'terbakar jadi abu karena Air Suci Pendeta'});
            if(victim.role === 'wolf_cub') G.wolfDoubleKill = true;
         } else {
            let priest = G.players.find(p=>p.role==='priest');
            if(priest && priest.alive) deaths.push({name: priest.name, reason: 'mati karena kualat melemparkan Air Suci ke orang yang tak bersalah'});
         }
      }
  }
  
  // 6. Cult Leader
  if(G.cultTarget) {
      let ct = G.players.find(p=>p.name === G.cultTarget);
      if(ct && ct.alive && !G.cultists.includes(G.cultTarget)) {
        G.cultists.push(G.cultTarget);
        logEvent(`Ketua Sekte merekrut ${G.cultTarget}.`);
      }
  }

  // Filter Unique deaths
  const uniqueDeaths = [];
  deaths.forEach(d => { if(!uniqueDeaths.find(x => x.name === d.name)) uniqueDeaths.push(d); });
  let newlyDeadNames = uniqueDeaths.map(d=>d.name);
  
  // 7. Lovers Logic
  let loversDied = newlyDeadNames.filter(n => G.lovers.includes(n)).length > 0;
  if(loversDied) {
      G.lovers.forEach(lName => {
         if(!newlyDeadNames.includes(lName) && G.players.find(p=>p.name===lName)?.alive) {
            uniqueDeaths.push({name: lName, reason: 'meninggal seketika karena kutukan patah hati (Kupidon)'});
            newlyDeadNames.push(lName);
         }
      });
  }

  // Apply deaths
  const huntersToFire = [];
  uniqueDeaths.forEach(d => {
    const p = G.players.find(x => x.name === d.name);
    if(p && p.alive) { p.alive = false; if(p.role === 'hunter') huntersToFire.push(p.name); }
  });
  
  // 8. Doppelganger Shift (Silent)
  if(G.doppelTarget && newlyDeadNames.includes(G.doppelTarget)) {
      let dop = G.players.find(p=>p.role === 'doppelganger');
      let target = G.players.find(p=>p.name === G.doppelTarget);
      if(dop && dop.alive && target) { dop.role = target.originalRole; }
  }

  // Traitor logic check
  const wolvesAlive = G.players.filter(p=>(p.role==='werewolf'||p.role==='wolf_cub'||p.cursedTurned)&&p.alive).length;
  
  const db = document.getElementById('dawn-body'); db.innerHTML = '';
  const dl = document.getElementById('dawn-log'); dl.innerHTML = '<strong>Riwayat Malam:</strong><br>' + G.log.filter(l=>l.startsWith(`Ronde ${G.round}`)).join('<br>');
  dl.classList.remove('hidden');

  if(uniqueDeaths.length === 0){
    db.innerHTML = '<div class="ann ann-safe">Kabar Baik! Semalam tidak ada korban jiwa. Semua warga aman.</div>';
  } else {
    db.innerHTML = `<div class="ann ann-dead">Kabar Buruk. Semalam ada ${uniqueDeaths.length} korban:</div>` + 
      uniqueDeaths.map(d => {
        const p = G.players.find(x => x.name === d.name);
        return `<div class="vote-row"><span class="pname" style="color:#FF007A">${d.name}</span><span class="schip schip-wolf">${ROLES[p.originalRole].emoji} ${ROLES[p.originalRole].name}</span></div>`;
      }).join('');
    uniqueDeaths.forEach(d => {
      const p = G.players.find(x => x.name === d.name);
      logEvent(`${d.name} (${ROLES[p.originalRole].name}) tewas di malam hari.`);
    });
  }
  
  if(wolvesAlive === 0) {
      let traitor = G.players.find(p=>p.role==='traitor');
      if(traitor && traitor.alive && !traitor.cursedTurned) traitor.cursedTurned = true; 
  }

  const act=document.getElementById('dawn-actions'), win=checkWin();
  if(win){ act.innerHTML=`<button class="neo-btn btn-gold cursor-pointer" onclick="endGame('${win}')">Lihat Hasil Akhir →</button>`; return; }
  if(huntersToFire.length>0){ G.pendingHunter=huntersToFire[0]; G.hunterCtx='night'; act.innerHTML=`<button class="neo-btn btn-red cursor-pointer" onclick="showHunter()">🏹 Hunter Menembak</button>`; }
  else { act.innerHTML=`<button class="neo-btn btn-gold cursor-pointer" onclick="showDay()">☀️ Mulai Diskusi Siang</button>`; }
}

function showHunter(){
  showScreen('hunter'); document.getElementById('hunter-label').textContent=`${G.pendingHunter} (Hunter) telah gugur!`;
  document.getElementById('hunter-targets').innerHTML = G.players.filter(p=>p.alive).map(p=>`<button class="psel" onclick="hunterShoot('${esc(p.name)}')">${p.name}</button>`).join('');
}
function hunterShoot(name){
  showModal(`<div class="text-center"><span class="text-6xl block">💥</span><h2 class="mt-4 text-black font-black">DOR!</h2><p class="mt-4 font-bold">Hunter menembak mati <strong>${name}</strong>!</p></div>`,
  [{label:'Lanjutkan', cls:'btn-red', fn:()=>{ closeModal(); handleElimination(name, 'hunter'); }}]);
}
function skipHunter(){ G.pendingHunter=null; afterElimCheck(); }

function showDay(){
  showScreen('day'); document.getElementById('day-label').textContent=`SIANG — RONDE ${G.round}`;
  const wolves = G.players.filter(p=>(p.role==='werewolf'||p.role==='wolf_cub'||p.cursedTurned)&&p.alive).length;
  const village = G.players.filter(p=>!['werewolf','wolf_cub'].includes(p.role)&&!p.cursedTurned&&p.alive).length;
  document.getElementById('day-status').innerHTML=`<span class="schip schip-wolf">🐺 Serigala: ${wolves}</span> <span class="schip schip-village">🏘️ Non-Serigala: ${village}</span> <span class="schip schip-all">Hidup: ${wolves+village}</span>`;
  
  G.votes={}; G.players.filter(p=>p.alive).forEach(p=>{ G.votes[p.name]=0; });
  renderVoteList();
}

function renderVoteList(){
  document.getElementById('vote-list').innerHTML=G.players.filter(p=>p.alive).map(p=>{
    const mayorIcon = p.role==='mayor' ? ' <span title="Walikota (Suara x2)" class="text-xl">🎩</span>' : '';
    return `<div class="vote-row"><span class="pname">${p.name}${mayorIcon}</span><div class="v-ctrl"><button class="v-btn" onclick="cv('${esc(p.name)}',-1)">−</button><span class="v-cnt" id="vc-${CSS.escape(p.name)}">${G.votes[p.name]||0}</span><button class="v-btn" onclick="cv('${esc(p.name)}',1)">+</button></div></div>`;
  }).join('');
}
function cv(name,d){ G.votes[name]=Math.max(0,(G.votes[name]||0)+d); document.getElementById('vc-'+CSS.escape(name)).textContent=G.votes[name]; }

function confirmVote(){
  let max=0, cands=[]; Object.entries(G.votes).forEach(([n,v])=>{ if(v>max){ max=v; cands=[n]; } else if(v===max&&v>0) cands.push(n); });
  if(!max){ showModal('<p class="text-center">Tidak ada suara yang diberikan!</p>',[{label:'Kembali',cls:'btn-ghost',fn:closeModal}]); return; }
  if(cands.length>1){
    showModal(`<h3 class="text-center" style="color:#FF007A">⚖️ Suara Seri!</h3><p class="text-center mt-4">Suara sama untuk: <strong>${cands.join(', ')}</strong>.<br>Pilih yang dieksekusi:</p>
      <div class="flex-center mt-6">${cands.map(c=>`<button class="neo-btn btn-red cursor-pointer" onclick="handleElimination('${esc(c)}', 'vote')">${c}</button>`).join('')}</div>`,
      [{label:'Batalkan',cls:'btn-ghost',fn:closeModal}]);
    return;
  }
  handleElimination(cands[0], 'vote');
}

function handleElimination(name, context){
  closeModal(); const p=G.players.find(x=>x.name===name); if(!p) return; p.alive=false;
  
  if(p.role === 'fool' && context === 'vote') { endGame('fool'); return; }
  if(p.role === 'wolf_cub') G.wolfDoubleKill = true;

  let loverMsg = '';
  if(G.lovers.includes(name)) {
     const otherName = G.lovers.find(l => l !== name);
     const other = G.players.find(x => x.name === otherName);
     if(other && other.alive) {
        other.alive = false;
        loverMsg = `<div class="info-box mt-4" style="background:#FF007A; color:white; border-color:#000;"><p>Karena ikatan <strong>Kupidon</strong>, <strong>${other.name}</strong> ikut meninggal karena patah hati!</p><p style="font-size:0.85rem; margin-top:4px;">Peran ${other.name}: <strong>${ROLES[other.originalRole].emoji} ${ROLES[other.originalRole].name}</strong></p></div>`;
     }
  }

  if(G.doppelTarget === name) {
      let dop = G.players.find(x=>x.role === 'doppelganger');
      if(dop && dop.alive) { dop.role = p.originalRole; }
  }

  const wolvesAlive = G.players.filter(x=>(x.role==='werewolf'||x.role==='wolf_cub'||x.cursedTurned)&&x.alive).length;
  if(wolvesAlive === 0) {
      let traitor = G.players.find(x=>x.role==='traitor');
      if(traitor && traitor.alive && !traitor.cursedTurned) traitor.cursedTurned = true; 
  }

  const r = ROLES[p.originalRole];
  let title = context === 'vote' ? 'Dieksekusi Warga' : 'Tertembak Hunter';
  
  showModal(`<div class="text-center"><span class="text-6xl block">⚖️</span><h3 class="mt-4 font-black" style="color:#FF007A">${title}</h3><p class="mt-4"><strong>${name}</strong> telah gugur.</p><div class="mt-4 border-2 border-black bg-white py-2 px-6 rounded-xl inline-block font-black">Perannya: <strong style="color:${r.color}">${r.emoji} ${r.name}</strong></div>${loverMsg}</div>`,
  [{label:'Lanjutkan',cls:'btn-gold',fn:()=>{ closeModal(); if(p.role==='hunter') { G.pendingHunter=name; G.hunterCtx='day'; showHunter(); } else afterElimCheck(); }}]);
}

function afterElimCheck(){ const win=checkWin(); if(win){ endGame(win); return; } if(G.hunterCtx==='night' && !G.pendingHunter) showDay(); else if(!G.pendingHunter) { G.round++; goNightCover(); } }
function skipDay(){ G.round++; goNightCover(); }

/* ============================================================
   ENDGAME
 ============================================================ */
function checkWin(){
  const wolves = G.players.filter(p=>(p.role==='werewolf'||p.role==='wolf_cub'||p.cursedTurned)&&p.alive).length;
  const sk = G.players.filter(p=>p.role==='serial_killer'&&p.alive).length;
  const aliveCount = G.players.filter(p=>p.alive).length;
  const cultistsAlive = G.players.filter(p=>p.alive && (p.role==='cult_leader' || G.cultists.includes(p.name))).length;
  
  if(cultistsAlive === aliveCount && aliveCount > 0) return 'cult_leader';
  if(sk > 0 && aliveCount <= 2 && wolves === 0) return 'serial_killer';
  if(wolves === 0 && sk === 0) return 'village';
  if(wolves >= (aliveCount - wolves)) {
      if(sk > 0 && aliveCount === 2 && wolves === 1) return 'serial_killer'; 
      return 'wolf'; 
  }
  return null;
}

function endGame(winner){
  showScreen('gameover');
  const tw=document.getElementById('go-title'), ic=document.getElementById('go-icon'), sub=document.getElementById('go-sub');
  
  if(winner === 'fool'){
    tw.textContent='ORANG GILA MENANG!'; ic.textContent='🤡';
    sub.textContent='Seluruh desa telah tertipu! Orang Gila berhasil memancing warga untuk mengeksekusinya siang ini.';
  } else if(winner === 'serial_killer'){
    tw.textContent='PEMBUNUH MENANG!'; ic.textContent='🔪';
    sub.textContent='Pembunuh Berantai berhasil menghabisi semua nyawa di desa dan menjadi satu-satunya yang tersisa!';
  } else if(winner === 'cult_leader'){
    tw.textContent='SEKTE MENANG!'; ic.textContent='🛐';
    sub.textContent='Seluruh warga yang tersisa telah dicuci otaknya. Sekte menguasai desa sepenuhnya!';
  } else if(winner==='wolf'){
    tw.textContent='SERIGALA MENANG!'; ic.textContent='🐺';
    sub.textContent='Kegelapan menyelimuti... Serigala berhasil melahap habis warga desa!';
  } else {
    tw.textContent='WARGA MENANG!'; ic.textContent='🏘️';
    sub.textContent='Cahaya kebenaran mengusir bayangan malam! Desa berhasil selamat dari ancaman Serigala.';
  }
  
  const alive=G.players.filter(p=>p.alive);
  document.getElementById('go-survivors').innerHTML = alive.map(p=>`<div class="tag" style="border-color:#000; background:#FFF000; color:black; font-size:1rem; padding:8px 16px;">${ROLES[p.role].emoji} ${p.name}</div>`).join('');
  document.getElementById('go-roles').innerHTML = G.players.map(p=>{ 
    const r=ROLES[p.role]; 
    const status = p.alive ? '<span class="bg-brutal-green border border-black px-2 py-0.5 rounded text-xs">HIDUP</span>' : '<span class="bg-brutal-pink text-white border border-black px-2 py-0.5 rounded text-xs">💀 GUGUR</span>';
    const orig = (p.originalRole !== p.role) ? ` <span style="font-size:0.8rem; color:#666; font-style:italic;">(Asli: ${ROLES[p.originalRole].name})</span>` : '';
    const cursedNote = p.cursedTurned ? ' <span style="font-size:0.8rem; color:#FF007A; font-style:italic;">(Jadi Serigala)</span>' : '';
    const cultNote = (!p.alive && p.role!=='cult_leader' && G.cultists.includes(p.name)) ? ' <span style="font-size:0.8rem; color:#9E00FF; font-style:italic;">(Sekte)</span>' : '';
    
    return `<div class="legend-item" style="opacity:${p.alive?1:0.7}"><span class="l-emoji">${r.emoji}</span><div style="flex:1"><div class="l-name" style="color:${r.color}">${p.name}</div><div class="l-desc">${r.name}${orig}${cursedNote}${cultNote}</div></div><div style="font-size:0.85rem; font-weight:900;">${status}</div></div>`; 
  }).join('');
}

function restartGame(){ 
  G.players=[]; G.round=1; G.revealIdx=0; G.revealShown=false; G.lovers=[]; G.cultists=[];
  renderSetup(); showScreen('setup'); 
}

renderSetup();
</script>
</body>
</html>