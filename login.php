<?php
// FILE: website/login.php
session_start();

// Redirect to admin if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    // Default hardcoded password
    if ($password === 'devtronix123') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = 'Kata sandi salah! Coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devtronix - Admin Login</title>
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
            transform: translate(2px, 2px);
        }
    </style>
</head>
<body class="text-black antialiased min-h-screen flex items-center justify-center font-sans p-4 select-none">

    <div class="w-full max-w-md p-8 bg-white border-[4px] border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
        <!-- Colored corner decoration -->
        <div class="absolute top-0 right-0 w-24 h-24 bg-brutal-pink border-b-4 border-l-4 border-black -mr-8 -mt-8 rotate-45 z-0"></div>
        
        <div class="text-center mb-8 relative z-10">
            <!-- Polaroid Frame for Logo -->
            <div class="w-24 h-28 bg-[#FAF6EE] border-[3px] border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-2.5 mx-auto mb-6 transform -rotate-3 transition-transform hover:rotate-2">
                <img src="uploads/kelas_logo.jpg" alt="Logo Kelas" class="w-full h-18 object-cover border-2 border-black">
                <p class="text-[9px] font-black tracking-widest mt-1.5 text-black uppercase">DEVTRONIX</p>
            </div>
            
            <h1 class="text-3xl font-black text-black uppercase leading-tight tracking-tight">Admin Access</h1>
            <p class="text-xs font-black text-slate-700 mt-1.5 uppercase tracking-widest">Sistem Manajemen File Kelas</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-brutal-pink border-[3px] border-black text-black font-black px-4 py-3 rounded-xl mb-6 text-sm text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                💥 <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-6 relative z-10">
            <div>
                <label class="block text-xs font-black text-black mb-2 uppercase tracking-widest">Passkey Admin</label>
                <input type="password" name="password" required autofocus class="w-full bg-white border-[3px] border-black rounded-xl px-4 py-3.5 text-sm text-black font-black focus:outline-none focus:bg-brutal-yellow focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all text-center tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]" placeholder="••••••••••••">
            </div>
            
            <button type="submit" class="neo-btn w-full bg-brutal-green text-black font-black py-3.5 border-[3px] border-black rounded-xl transition-all text-sm shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider cursor-pointer">
                Masuk Dashboard ➜
            </button>
        </form>

        <div class="mt-8 text-center relative z-10 pt-4 border-t-2 border-black border-dashed">
            <a href="index.php" class="inline-block bg-brutal-cyan text-black border-2 border-black px-4 py-2 font-black font-mono text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px] transition-all">
                ⭠ Kembali ke utama
            </a>
        </div>
    </div>
</body>
</html>
