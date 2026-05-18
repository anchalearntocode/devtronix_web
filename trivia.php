<?php
// FILE: website/trivia.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trivia Kelas - Devtronix</title>
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
        .ans-btn {
            transition: all 0.15s cubic-bezier(0,0,0,1);
            cursor: pointer;
        }
        .ans-btn:hover {
            background-color: #FFF000;
            transform: translate(-2px, -2px);
        }
        .ans-btn:active {
            transform: translate(1px, 1px);
        }
        .ans-btn.correct {
            background-color: #00FF66 !important;
            color: black !important;
            box-shadow: 2px 2px 0px 0px rgba(0,0,0,1) !important;
            transform: none !important;
        }
        .ans-btn.wrong {
            background-color: #FF007A !important;
            color: black !important;
            box-shadow: 2px 2px 0px 0px rgba(0,0,0,1) !important;
            transform: none !important;
        }
    </style>
</head>
<body class="text-black antialiased min-h-screen flex flex-col items-center p-6 selection:bg-brutal-yellow">

    <!-- Header Navigation -->
    <nav class="w-full max-w-2xl flex justify-between items-center mb-12 bg-white border-[3px] border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="flex items-center gap-2 font-black text-xl uppercase tracking-tight">
            <span>🧠</span> TRIVIA DEVTRONIX
        </div>
        <a href="index.php" class="neo-btn bg-brutal-cyan text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
            ⭠ KEMBALI
        </a>
    </nav>

    <!-- Main Container -->
    <div class="max-w-xl w-full text-center">
        <!-- 1. SETUP SCREEN -->
        <div id="setup-screen" class="bg-white border-[3px] border-black rounded-2xl p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
            <h1 class="text-3xl font-black mb-4 uppercase leading-tight tracking-tight">Seberapa Kenal Kamu Dengan Kelasmu?</h1>
            <p class="text-slate-800 font-bold mb-8 text-sm">Uji pengetahuanmu tentang warga Devtronix dan pelajaran kita secara interaktif!</p>
            <button onclick="startTrivia()" class="neo-btn bg-brutal-green text-black border-2 border-black font-black px-12 py-4 rounded-xl text-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider cursor-pointer">
                Mulai Kuis ➜
            </button>
        </div>

        <!-- 2. GAME SCREEN -->
        <div id="game-screen" class="hidden space-y-6">
            <div class="flex justify-between items-center bg-white border-[3px] border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <span id="progress" class="text-xs font-black font-mono text-slate-800 uppercase tracking-widest bg-brutal-yellow border border-black px-3 py-1 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">SOAL 1 DARI 5</span>
                <span id="score" class="text-xs font-black font-mono text-slate-800 uppercase tracking-widest bg-brutal-cyan border border-black px-3 py-1 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">SKOR: 0</span>
            </div>
            
            <div class="bg-white border-[3px] border-black rounded-2xl p-6 md:p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <h2 id="question" class="text-xl md:text-2xl font-black leading-relaxed italic mb-8 text-black bg-[#FAF6EE] p-4 border-2 border-black border-dashed rounded-xl">
                    Memuat pertanyaan...
                </h2>
                <div id="answers" class="grid grid-cols-1 gap-4 text-left">
                    <!-- Dynamic Buttons -->
                </div>
            </div>
        </div>

        <!-- 3. RESULT SCREEN -->
        <div id="result-screen" class="hidden bg-white border-[3px] border-black rounded-2xl p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
            <div class="text-6xl mb-6">🏆</div>
            <h2 class="text-3xl font-black mb-2 uppercase tracking-tight">Kuis Selesai!</h2>
            <p class="text-slate-800 font-bold mb-8 text-lg">Skor akhir kamu: <span id="final-score" class="text-brutal-pink text-3xl font-black bg-[#FAF6EE] border-2 border-black px-4 py-1 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] ml-2">0</span></p>
            <button onclick="location.reload()" class="neo-btn bg-brutal-green text-black border-2 border-black font-black px-8 py-3.5 rounded-xl text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider cursor-pointer">
                Main Lagi ➜
            </button>
        </div>
    </div>

    <script>
        const questions = [
            { q: "Siapa ketua kelas Devtronix saat ini?", a: ["Muhammad Hamzah", "Rasyah Risqullah", "Natasya", "Riskia"], c: 1 },
            { q: "Apa nama komponen yang berfungsi sebagai saklar elektronik?", a: ["Resistor", "Kapasitor", "Transistor", "Dioda"], c: 2 },
            { q: "Di mana lokasi foto kelas kita diambil?", a: ["Halaman Sekolah", "Studio Foto", "Ruang Kelas", "Taman Kota"], c: 2 },
            { q: "Apa singkatan dari LED?", a: ["Light Electronic Device", "Light Emitting Diode", "Low Energy Diode", "Light Emitting Device"], c: 1 },
            { q: "Siapa member yang paling jago ngoding?", a: ["Semua Jago!", "Belum Ada", "Hanya Admin", "Rahasia"], c: 0 }
        ];

        let currentIdx = 0;
        let score = 0;
        let answered = false;

        function startTrivia() {
            document.getElementById('setup-screen').classList.add('hidden');
            document.getElementById('game-screen').classList.remove('hidden');
            renderQuestion();
        }

        function renderQuestion() {
            answered = false;
            const q = questions[currentIdx];
            document.getElementById('progress').textContent = `SOAL ${currentIdx + 1} DARI ${questions.length}`;
            document.getElementById('question').textContent = `"${q.q}"`;
            
            const ab = document.getElementById('answers');
            ab.innerHTML = '';
            q.a.forEach((ans, i) => {
                const btn = document.createElement('button');
                btn.className = 'ans-btn w-full p-4 rounded-xl text-left text-sm font-black text-black border-2 border-black bg-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]';
                btn.innerHTML = `<span class="bg-black text-white rounded px-2.5 py-0.5 mr-2 font-mono">${String.fromCharCode(65 + i)}</span> ${ans}`;
                btn.onclick = () => checkAnswer(i, btn);
                ab.appendChild(btn);
            });
        }

        function checkAnswer(idx, btn) {
            if (answered) return;
            answered = true;
            const q = questions[currentIdx];
            const btns = document.querySelectorAll('.ans-btn');
            
            if (idx === q.c) {
                score += 20;
                btn.classList.add('correct');
            } else {
                btn.classList.add('wrong');
                btns[q.c].classList.add('correct');
            }
            
            document.getElementById('score').textContent = `SKOR: ${score}`;
            
            setTimeout(() => {
                currentIdx++;
                if (currentIdx < questions.length) renderQuestion();
                else showResult();
            }, 1500);
        }

        function showResult() {
            document.getElementById('game-screen').classList.add('hidden');
            document.getElementById('result-screen').classList.remove('hidden');
            document.getElementById('final-score').textContent = score;
        }
    </script>
</body>
</html>
