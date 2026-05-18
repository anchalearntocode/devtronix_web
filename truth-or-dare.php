<?php
// FILE: website/truth-or-dare.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truth or Dare - Devtronix</title>
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
<body class="text-black antialiased min-h-screen flex flex-col items-center justify-center p-6 selection:bg-brutal-yellow">

    <!-- Header Navigation -->
    <nav class="fixed top-0 left-0 right-0 p-6 flex justify-between items-center z-50">
        <div class="flex items-center gap-3 font-black text-xl bg-white border-[3px] border-black px-4 py-2 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] uppercase tracking-tight">
            <img src="uploads/truth_or_dare_logo.png?v=3" alt="Logo" class="w-8 h-8 rounded-full border-2 border-black shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] bg-white p-0.5"> TRUTH OR DARE
        </div>
        <a href="index.php" class="neo-btn bg-brutal-cyan text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
            ⭠ KEMBALI
        </a>
    </nav>

    <!-- Main Container -->
    <div class="max-w-md w-full text-center mt-20">
        <h1 class="text-4xl font-black mb-2 uppercase leading-none tracking-tight">Pilih Nasibmu</h1>
        <p class="text-slate-800 font-bold mb-10">Berani berkata jujur atau berani beraksi nyata?</p>

        <!-- Choices Grid -->
        <div class="grid grid-cols-2 gap-6 mb-10">
            <button onclick="pick('truth')" class="neo-btn p-6 rounded-2xl bg-brutal-green border-[3px] border-black text-black font-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-[#00E057] active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] group cursor-pointer">
                <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">💡</div>
                <div class="font-black uppercase tracking-widest text-lg">Truth</div>
            </button>
            
            <button onclick="pick('dare')" class="neo-btn p-6 rounded-2xl bg-brutal-pink border-[3px] border-black text-black font-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] group cursor-pointer">
                <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">🔥</div>
                <div class="font-black uppercase tracking-widest text-lg">Dare</div>
            </button>
        </div>

        <!-- Display Box -->
        <div id="display-container" class="hidden transform transition-all duration-300">
            <div class="bg-white border-[3px] border-black rounded-2xl p-8 min-h-[250px] flex flex-col items-center justify-center relative shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <div id="type-label" class="absolute -top-4 px-6 py-2 border-2 border-black rounded-full font-black uppercase tracking-widest text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    TRUTH
                </div>
                
                <p id="content" class="text-xl font-bold leading-relaxed italic text-black bg-[#FAF6EE] p-5 border-2 border-black border-dashed rounded-xl w-full">
                    Memuat...
                </p>
                
                <button onclick="hide()" class="mt-6 neo-btn bg-white border-2 border-black text-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] cursor-pointer">
                    Tutup & Lanjut ➜
                </button>
            </div>
        </div>
    </div>

    <script>
        const data = {
            truth: [
                "Siapa teman sekelas yang paling diam-diam menghanyutkan?",
                "Pernah naksir sama siapa di kelas ini?",
                "Hal memalukan apa yang pernah kamu lakukan saat presentasi?",
                "Siapa guru/dosen favoritmu dan berikan alasan jujurnya?",
                "Apa rahasia kocak yang belum pernah kamu ceritakan ke siapapun?",
                "Siapa teman yang menurutmu paling cerdas tapi pemalas?",
                "Jika harus bertukar kehidupan dengan satu teman sekelas, siapa dia?",
                "Apa kesan pertamamu ketika menginjakkan kaki di Devtronix?",
                "Pernah menyontek saat kuis/ujian di kelas? Ceritakan!",
                "Siapa yang paling sering menghibur dan membuatmu tertawa?"
            ],
            dare: [
                "Lakukan push-up 10 kali sambil menyebutkan nama-nama komponen elektronika.",
                "Bicara menggunakan logat daerah asalmu yang paling kental sampai game berakhir.",
                "Tunjukkan chat WhatsApp terakhirmu pada orang di sebelah kananmu.",
                "Posting status WhatsApp: 'Devtronix adalah keluarga terbaikku! ❤️' selama 1 jam.",
                "Peragakan cara berjalan atau gaya bicara salah satu teman tanpa menyebut nama.",
                "Minum air putih satu gelas penuh dalam satu tarikan napas.",
                "Pijat santai pundak teman di sebelah kirimu selama tepat satu menit.",
                "Lakukan pantomim sedang merakit robot atau menyolder selama 20 detik.",
                "Kirimkan chat acak ke grup kelas berisi stiker lucu/konyol.",
                "Sebutkan 3 hal positif paling keren tentang teman sebangkumu dengan lantang."
            ]
        };

        function pick(type) {
            const list = data[type];
            const random = list[Math.floor(Math.random() * list.length)];
            
            const container = document.getElementById('display-container');
            const label = document.getElementById('type-label');
            const content = document.getElementById('content');
            
            label.textContent = type.toUpperCase();
            if (type === 'truth') {
                label.className = `absolute -top-4 px-6 py-2 border-2 border-black rounded-full font-black uppercase tracking-widest text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] bg-brutal-yellow text-black`;
            } else {
                label.className = `absolute -top-4 px-6 py-2 border-2 border-black rounded-full font-black uppercase tracking-widest text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] bg-brutal-pink text-black`;
            }
            
            content.textContent = `"${random}"`;
            
            container.classList.remove('hidden');
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function hide() {
            document.getElementById('display-container').classList.add('hidden');
        }
    </script>
</body>
</html>
