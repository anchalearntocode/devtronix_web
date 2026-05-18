<?php
// FILE: website/tictactoe.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe - Devtronix</title>
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
        .cell {
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.15s cubic-bezier(0, 0, 0, 1);
        }
        .cell:hover:empty {
            background-color: #FFF000;
            transform: translate(-2px, -2px);
        }
        .cell:active:empty {
            transform: translate(1px, 1px);
        }
        .x-color {
            color: #FF007A;
            text-shadow: 2px 2px 0px #000;
        }
        .o-color {
            color: #00F0FF;
            text-shadow: 2px 2px 0px #000;
        }
    </style>
</head>
<body class="text-black antialiased min-h-screen flex flex-col items-center p-6 selection:bg-brutal-yellow">

    <!-- Header Navigation -->
    <nav class="w-full max-w-lg flex justify-between items-center mb-12 bg-white border-[3px] border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="flex items-center gap-3 font-black text-xl uppercase tracking-tight">
            <img src="uploads/tictactoe_logo.png?v=3" alt="Logo" class="w-8 h-8 rounded-full border-2 border-black shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] bg-white p-0.5"> TIC TAC TOE
        </div>
        <a href="index.php" class="neo-btn bg-brutal-cyan text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
            ⭠ KEMBALI
        </a>
    </nav>

    <div class="max-w-md w-full text-center">
        <!-- Score Card Container -->
        <div class="flex justify-between items-center mb-8 bg-white border-[3px] border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <div id="player-x" class="flex flex-col items-center flex-1 border-r-2 border-black border-dashed">
                <span class="text-xs font-black uppercase tracking-widest text-slate-700 mb-1">Player X</span>
                <span class="text-3xl font-black x-color">0</span>
            </div>
            
            <div id="turn-indicator" class="px-5 py-2 bg-brutal-yellow text-black border-2 border-black rounded-full text-xs font-black mx-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider">
                GILIRAN X
            </div>
            
            <div id="player-o" class="flex flex-col items-center flex-1">
                <span class="text-xs font-black uppercase tracking-widest text-slate-700 mb-1">Player O</span>
                <span class="text-3xl font-black o-color">0</span>
            </div>
        </div>

        <!-- Tic Tac Toe Board -->
        <div id="grid" class="grid grid-cols-3 gap-4 bg-[#FAF6EE] border-[3px] border-black p-4 rounded-2xl mb-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] bg-white">
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(0)"></div>
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(1)"></div>
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(2)"></div>
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(3)"></div>
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(4)"></div>
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(5)"></div>
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(6)"></div>
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(7)"></div>
            <div class="cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" onclick="play(8)"></div>
        </div>

        <!-- Reset Button -->
        <button onclick="reset()" class="neo-btn bg-brutal-green text-black border-2 border-black px-8 py-3 rounded-xl text-sm font-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider cursor-pointer">
            🧹 Reset Papan
        </button>
    </div>

    <script>
        let board = Array(9).fill(null);
        let current = 'X';
        let scores = { X: 0, O: 0 };
        let active = true;

        function play(idx) {
            if (!active || board[idx]) return;
            
            board[idx] = current;
            render();
            
            if (checkWin()) {
                active = false;
                scores[current]++;
                updateScores();
                const indicator = document.getElementById('turn-indicator');
                indicator.textContent = current + " MENANG! 🎉";
                indicator.className = `px-5 py-2 bg-brutal-green text-black border-2 border-black rounded-full text-xs font-black mx-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider`;
                return;
            }
            
            if (!board.includes(null)) {
                active = false;
                const indicator = document.getElementById('turn-indicator');
                indicator.textContent = "SERI! 🤝";
                indicator.className = `px-5 py-2 bg-brutal-purple text-white border-2 border-black rounded-full text-xs font-black mx-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider`;
                return;
            }
            
            current = current === 'X' ? 'O' : 'X';
            const indicator = document.getElementById('turn-indicator');
            indicator.textContent = "GILIRAN " + current;
            indicator.className = current === 'X' ? 
                `px-5 py-2 bg-brutal-pink text-black border-2 border-black rounded-full text-xs font-black mx-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider` :
                `px-5 py-2 bg-brutal-cyan text-black border-2 border-black rounded-full text-xs font-black mx-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider`;
        }

        function render() {
            const cells = document.querySelectorAll('.cell');
            board.forEach((val, i) => {
                cells[i].textContent = val || '';
                cells[i].className = `cell bg-[#FAF6EE] border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] ${val === 'X' ? 'x-color' : (val === 'O' ? 'o-color' : '')}`;
            });
        }

        function checkWin() {
            const wins = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
            return wins.some(w => board[w[0]] && board[w[0]] === board[w[1]] && board[w[0]] === board[w[2]]);
        }

        function updateScores() {
            document.querySelector('#player-x span:last-child').textContent = scores.X;
            document.querySelector('#player-o span:last-child').textContent = scores.O;
        }

        function reset() {
            board = Array(9).fill(null);
            current = 'X';
            active = true;
            const indicator = document.getElementById('turn-indicator');
            indicator.textContent = "GILIRAN X";
            indicator.className = `px-5 py-2 bg-brutal-pink text-black border-2 border-black rounded-full text-xs font-black mx-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider`;
            render();
        }
    </script>
</body>
</html>
