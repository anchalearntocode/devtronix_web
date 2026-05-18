<?php
// FILE: website/tebak-kata.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tebak Kata - Devtronix</title>
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
    </style>
</head>
<body class="text-black antialiased min-h-screen flex flex-col items-center p-6 selection:bg-brutal-yellow">

    <!-- Header Navigation -->
    <nav class="w-full max-w-4xl flex justify-between items-center mb-12 bg-white border-[3px] border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="flex items-center gap-3 font-black text-xl uppercase tracking-tight">
            <img src="uploads/tebak_kata_logo.png?v=3" alt="Logo" class="w-8 h-8 rounded-full border-2 border-black shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] bg-white p-0.5"> TEBAK KATA DEVTRONIX
        </div>
        <a href="index.php" class="neo-btn bg-brutal-cyan text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
            ⭠ KEMBALI
        </a>
    </nav>

    <!-- Game Box -->
    <div class="max-w-2xl w-full text-center bg-white border-[3px] border-black rounded-2xl p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
        <div class="mb-8">
            <div id="hint" class="inline-block bg-brutal-yellow text-black border-2 border-black px-4 py-1.5 rounded-lg text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider mb-6">
                Kategori: Komponen
            </div>
            
            <div id="word-display" class="text-3xl md:text-5xl font-black tracking-[0.2em] font-mono text-black select-none py-4 border-2 border-black border-dashed rounded-xl bg-[#FAF6EE]">
                _ _ _ _ _
            </div>
        </div>

        <div class="flex flex-col items-center mb-10 border-t-2 border-black border-dashed pt-6">
            <div class="text-sm font-black uppercase tracking-wider text-black mb-3">
                Sisa Kesempatan: <span id="lives" class="text-brutal-pink text-lg font-black bg-[#FAF6EE] border-2 border-black px-2.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">6</span>
            </div>
            <div class="w-48 h-6 border-[3px] border-black bg-[#FAF6EE] rounded-full overflow-hidden p-0.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                <div id="health-bar" class="h-full bg-brutal-green border-r-2 border-black transition-all duration-300" style="width: 100%"></div>
            </div>
        </div>

        <div id="keyboard" class="grid grid-cols-7 sm:grid-cols-9 gap-3">
            <!-- Buttons dynamically generated -->
        </div>

        <!-- Pop-up Overlay status -->
        <div id="status-overlay" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] flex flex-col items-center justify-center p-6 animate-fade">
            <div class="bg-brutal-cream border-[4px] border-black p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] max-w-sm w-full text-center relative">
                <div id="status-icon" class="text-6xl mb-4">🎉</div>
                <h2 id="status-title" class="text-3xl font-black mb-2 uppercase tracking-tight">Kamu Menang!</h2>
                <p id="status-desc" class="text-sm font-bold text-slate-800 mb-8 leading-relaxed">Kata yang benar adalah: <span class="text-brutal-pink font-black border border-black bg-white px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">RESISTOR</span></p>
                <button onclick="initGame()" class="neo-btn bg-brutal-green text-black border-2 border-black font-black px-8 py-3.5 rounded-lg text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider cursor-pointer">
                    Main Lagi ➜
                </button>
            </div>
        </div>
    </div>

    <script>
        const words = [
            { w: "RESISTOR", h: "Komponen penghambat arus listrik" },
            { w: "KAPASITOR", h: "Komponen penyimpan muatan listrik" },
            { w: "TRANSISTOR", h: "Saklar elektronik atau penguat sinyal" },
            { w: "ARDUINO", h: "Microcontroller populer untuk hobiis" },
            { w: "DIODA", h: "Penyearah arus listrik" },
            { w: "OSILOSKOP", h: "Alat untuk melihat bentuk gelombang" },
            { w: "SOLDER", h: "Alat untuk merakit komponen" },
            { w: "MULTIMETER", h: "Alat ukur tegangan, arus, dan hambatan" },
            { w: "LED", h: "Diode yang memancarkan cahaya" },
            { w: "INDUCTOR", h: "Komponen penyimpan energi magnetik" }
        ];

        let target = "";
        let guessed = [];
        let lives = 6;

        function initGame() {
            const item = words[Math.floor(Math.random() * words.length)];
            target = item.w;
            guessed = [];
            lives = 6;
            
            document.getElementById('hint').textContent = "💡 PETUNJUK: " + item.h;
            document.getElementById('status-overlay').classList.add('hidden');
            render();
            renderKeyboard();
        }

        function render() {
            const display = target.split('').map(char => guessed.includes(char) ? char : "_").join(' ');
            document.getElementById('word-display').textContent = display;
            document.getElementById('lives').textContent = lives;
            
            const pct = (lives / 6 * 100);
            const hb = document.getElementById('health-bar');
            hb.style.width = pct + "%";
            
            if (lives <= 2) {
                hb.className = "h-full bg-brutal-pink border-r-2 border-black transition-all duration-300";
            } else if (lives <= 4) {
                hb.className = "h-full bg-brutal-yellow border-r-2 border-black transition-all duration-300";
            } else {
                hb.className = "h-full bg-brutal-green border-r-2 border-black transition-all duration-300";
            }

            if (!display.includes("_")) win();
            if (lives <= 0) lose();
        }

        function renderKeyboard() {
            const kb = document.getElementById('keyboard');
            kb.innerHTML = "";
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split('').forEach(char => {
                const btn = document.createElement('button');
                btn.textContent = char;
                btn.className = "neo-btn h-12 rounded-lg bg-white border-2 border-black font-black text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-brutal-yellow active:translate-y-[2px] disabled:opacity-30 disabled:pointer-events-none disabled:shadow-none";
                btn.disabled = guessed.includes(char);
                btn.onclick = () => guess(char);
                kb.appendChild(btn);
            });
        }

        function guess(char) {
            if (guessed.includes(char) || lives <= 0) return;
            guessed.push(char);
            if (!target.includes(char)) lives--;
            render();
            renderKeyboard();
        }

        function win() {
            document.getElementById('status-icon').textContent = "🎉";
            document.getElementById('status-title').textContent = "Kamu Menang!";
            document.getElementById('status-desc').innerHTML = `Kata yang benar adalah: <br><span class="text-brutal-green border-2 border-black bg-black font-black font-mono text-lg px-4 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mt-3 inline-block uppercase tracking-wider">${target}</span>`;
            document.getElementById('status-overlay').classList.remove('hidden');
        }

        function lose() {
            document.getElementById('status-icon').textContent = "💀";
            document.getElementById('status-title').textContent = "Kamu Kalah!";
            document.getElementById('status-desc').innerHTML = `Jawaban aslinya adalah: <br><span class="text-brutal-pink border-2 border-black bg-black font-black font-mono text-lg px-4 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mt-3 inline-block uppercase tracking-wider">${target}</span>`;
            document.getElementById('status-overlay').classList.remove('hidden');
        }

        initGame();
    </script>
</body>
</html>
