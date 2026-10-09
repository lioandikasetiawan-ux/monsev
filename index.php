<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monsev - Server Monitoring & UJANG E AY Assistant</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Marked.js untuk render Markdown & DOMPurify untuk keamanan HTML -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.8/purify.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen p-6 md:p-10 font-sans selection:bg-blue-500 selection:text-white relative">
    <div class="max-w-6xl mx-auto space-y-12 pb-24">
        
        <!-- BAGIAN 1: MONSEV DASHBOARD -->
        <div>
            <!-- Header Modern -->
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 pb-6 border-b border-slate-200 gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="p-2.5 bg-blue-600 text-white rounded-xl shadow-md shadow-blue-500/20 text-xl">🖥️</span>
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">MONSEV Dashboard</h1>
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Sistem Pemantauan Performa Server 36, 38, & 46 Secara Real-Time</p>
                </div>
                <button onclick="fetchServerStatus()" id="refresh-btn" class="flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold rounded-xl shadow-sm transition-all duration-200 active:scale-95">
                    <svg id="refresh-icon" class="w-4 h-4 transition-transform duration-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Refresh Status</span>
                </button>
            </header>

            <!-- Grid Server -->
            <div id="server-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Loading Skeleton -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-pulse space-y-4">
                    <div class="h-6 bg-slate-200 rounded-lg w-3/4"></div>
                    <div class="h-4 bg-slate-100 rounded-lg w-1/2"></div>
                    <div class="space-y-3 pt-4">
                        <div class="h-4 bg-slate-100 rounded"></div>
                        <div class="h-4 bg-slate-100 rounded"></div>
                        <div class="h-4 bg-slate-100 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- BAGIAN 2: FLOATING / MINIMIZABLE AI ASSISTANT WIDGET (UJANG E AY) -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
        
        <!-- Jendela Chat -->
        <div id="chat-window" class="relative w-[90vw] sm:w-[420px] h-[550px] bg-white rounded-2xl border border-slate-200 shadow-2xl flex flex-col transition-all duration-300 origin-bottom-right scale-100 opacity-100 mb-4 overflow-hidden">
            
            <!-- Header AI Chat -->
            <div class="flex justify-between items-center px-5 py-4 bg-slate-50 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-blue-600 text-white rounded-xl shadow-md shadow-blue-500/20 text-xs font-bold">UJ</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">UJANG E AY (AI Assistant)</h2>
                        <p class="text-[11px] text-slate-500">Memori percakapan & analisa gambar</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5">
                    <!-- Riwayat -->
                    <button onclick="openHistory()" title="Riwayat obrolan" class="p-1.5 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    <!-- Obrolan baru -->
                    <button onclick="newChat()" title="Obrolan baru" class="p-1.5 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                    <!-- Minimize -->
                    <button onclick="toggleChatWindow()" title="Minimize" class="p-1.5 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Chat Container -->
            <div id="chat-container" class="flex-1 overflow-y-auto p-4 space-y-4 scroll-smooth bg-slate-50/50"></div>

            <!-- Preview Gambar yang Akan Dikirim -->
            <div id="image-preview-bar" class="hidden items-center gap-3 px-3 py-2 bg-slate-50 border-t border-slate-100">
                <img id="image-preview" src="" alt="Preview" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                <span class="flex-1 text-[11px] text-slate-500 truncate" id="image-preview-name">Gambar siap dikirim</span>
                <button type="button" onclick="removePendingImage()" title="Hapus gambar" class="p-1.5 hover:bg-slate-200 text-slate-500 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Input Form -->
            <form id="chat-form" onsubmit="handleSendMessage(event)" class="flex gap-2 p-3 bg-white border-t border-slate-100">
                <input type="file" id="image-input" accept="image/*" class="hidden" onchange="handleFileSelect(event)">
                <button type="button" onclick="document.getElementById('image-input').click()" title="Lampirkan gambar" class="px-3 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 rounded-xl transition-all flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                </button>
                <input 
                    type="text" 
                    id="user-input" 
                    placeholder="Tanyakan sesuatu atau lampirkan gambar..." 
                    autocomplete="off"
                    class="flex-1 min-w-0 px-4 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-slate-700 placeholder-slate-400 transition-all"
                >
                <button type="submit" id="send-btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition-all flex items-center justify-center">
                    <svg class="w-4 h-4 rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>

            <!-- PANEL RIWAYAT OBROLAN (menutupi jendela chat) -->
            <div id="history-drawer" class="hidden absolute inset-0 z-10 bg-white flex-col">
                <div class="flex justify-between items-center px-5 py-4 bg-slate-50 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Riwayat Obrolan</h3>
                    <button onclick="closeHistory()" title="Tutup" class="p-1.5 hover:bg-slate-200 text-slate-600 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-3 border-b border-slate-100">
                    <button onclick="newChat()" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        Obrolan baru
                    </button>
                </div>
                <div id="history-list" class="flex-1 overflow-y-auto p-2 space-y-1"></div>
                <div class="p-3 border-t border-slate-100">
                    <button onclick="clearAllHistory()" class="w-full px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                        Hapus semua riwayat
                    </button>
                </div>
            </div>
        </div>

        <!-- Tombol Icon Floating -->
        <button id="chat-toggle-btn" onclick="toggleChatWindow()" class="relative flex items-center justify-center w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-xl shadow-blue-500/30 transition-all duration-300 hover:scale-105 active:scale-95 group">
            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
            <svg class="w-6 h-6 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
        </button>

    </div>

    <script>
        // Gambar yang menunggu dikirim (data URL JPEG hasil kompres)
        let pendingImage = null;

        let isChatOpen = true;
        function toggleChatWindow() {
            let win = document.getElementById('chat-window');
            let btn = document.getElementById('chat-toggle-btn');
            
            isChatOpen = !isChatOpen;
            if (isChatOpen) {
                win.classList.remove('scale-0', 'opacity-0', 'pointer-events-none', 'h-0');
                win.classList.add('scale-100', 'opacity-100', 'h-[550px]');
                btn.classList.add('hidden');
            } else {
                win.classList.remove('scale-100', 'opacity-100', 'h-[550px]');
                win.classList.add('scale-0', 'opacity-0', 'pointer-events-none', 'h-0');
                btn.classList.remove('hidden');
            }
        }

        document.getElementById('chat-toggle-btn').classList.add('hidden');

        // --- SCRIPT UNTUK MONITORING SERVER ---
        async function fetchServerStatus() {
            let icon = document.getElementById('refresh-icon');
            if(icon) {
                icon.classList.add('rotate-180');
                setTimeout(() => icon.classList.remove('rotate-180'), 500);
            }

            try {
                let response = await fetch('api-monitor.php');
                let result = await response.json();
                
                let container = document.getElementById('server-container');
                if(!container) return;
                container.innerHTML = '';

                result.servers.forEach(item => {
                    let srv = item.metrics;
                    let isOnline = srv.status === 'Online';
                    
                    let badgeColor = isOnline 
                        ? 'bg-emerald-50 text-emerald-600 border-emerald-200' 
                        : 'bg-rose-50 text-rose-600 border-rose-200';
                    
                    let dotColor = isOnline ? 'bg-emerald-500' : 'bg-rose-500';

                    let ramPctNum = parseFloat(srv.ram_pct) || 0;
                    let swapPctNum = parseFloat(srv.swap_pct) || 0;
                    let diskPctNum = parseFloat(srv.disk_pct) || 0;

                    let geoServerSection = '';
                    if (item.ip.endsWith('.38')) {
                        let isGeoActive = srv.geoserver === 'Active';
                        let geoColor = isGeoActive ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-slate-500 bg-slate-100 border-slate-200';
                        geoServerSection = `
                            <div class="flex justify-between items-center py-2 px-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                                <span class="text-slate-500 font-medium">GeoServer:</span> 
                                <span class="px-2 py-0.5 font-semibold rounded-md border ${geoColor}">
                                    ${srv.geoserver ?? 'Offline'}
                                </span>
                            </div>
                        `;
                    }

                    let isWebActive = srv.web_server === 'Active';
                    let webColor = isWebActive ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-slate-500 bg-slate-100 border-slate-200';
                    
                    let card = `
                        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                            <div>
                                <div class="flex justify-between items-start mb-5">
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors">${item.name}</h2>
                                        <span class="text-xs font-mono text-slate-400">${item.ip}</span>
                                    </div>
                                    <span class="flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full border ${badgeColor}">
                                        <span class="w-1.5 h-1.5 rounded-full ${dotColor} animate-pulse"></span>
                                        ${srv.status}
                                    </span>
                                </div>

                                <div class="space-y-3 text-sm text-slate-600">
                                    <div class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                        <span class="text-slate-500 font-medium">CPU Load (1m / 5m):</span> 
                                        <span class="font-mono font-semibold text-amber-600">${srv.cpu_load ?? 'N/A'} / ${srv.cpu_load_5m ?? 'N/A'}</span>
                                    </div>

                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">RAM Usage:</span> 
                                            <span class="font-mono font-semibold text-slate-700">${srv.ram_used ?? '-'} / ${srv.ram_total ?? '-'} (${srv.ram_pct ?? '0%'})</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: ${ramPctNum}%"></div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">Swap Usage:</span> 
                                            <span class="font-mono font-semibold text-slate-700">${srv.swap_used ?? '-'} / ${srv.swap_total ?? '-'} (${srv.swap_pct ?? '0%'})</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: ${swapPctNum}%"></div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">Disk Usage:</span> 
                                            <span class="font-mono font-semibold text-slate-700">${srv.disk_used ?? '-'} / ${srv.disk_total ?? '-'} (${srv.disk_pct ?? '0%'})</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: ${diskPctNum}%"></div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center py-2 px-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                                        <span class="text-slate-500 font-medium">Web Server (Nginx/Apache):</span> 
                                        <span class="px-2 py-0.5 font-semibold rounded-md border ${webColor}">
                                            ${srv.web_server ?? 'Offline'}
                                        </span>
                                    </div>

                                    ${geoServerSection}
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += card;
                });
            } catch (error) {
                console.error('Gagal memuat status server:', error);
            }
        }

        fetchServerStatus();
        setInterval(fetchServerStatus, 10000);


        // --- SCRIPT UNTUK UPLOAD & KOMPRES GAMBAR ---
        const MAX_IMAGE_DIMENSION = 1280;   // sisi terpanjang (px)
        const IMAGE_QUALITY = 0.8;          // kualitas JPEG

        function compressImage(file) {
            return new Promise((resolve, reject) => {
                let reader = new FileReader();
                reader.onerror = () => reject(new Error('Gagal membaca file.'));
                reader.onload = () => {
                    let img = new Image();
                    img.onerror = () => reject(new Error('File bukan gambar yang valid.'));
                    img.onload = () => {
                        let { width, height } = img;
                        let scale = Math.min(1, MAX_IMAGE_DIMENSION / Math.max(width, height));
                        let w = Math.round(width * scale);
                        let h = Math.round(height * scale);

                        let canvas = document.createElement('canvas');
                        canvas.width = w;
                        canvas.height = h;
                        let ctx = canvas.getContext('2d');
                        // Latar putih agar PNG transparan tidak jadi hitam
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, w, h);
                        ctx.drawImage(img, 0, 0, w, h);
                        resolve(canvas.toDataURL('image/jpeg', IMAGE_QUALITY));
                    };
                    img.src = reader.result;
                };
                reader.readAsDataURL(file);
            });
        }

        async function setPendingImageFromFile(file) {
            if (!file || !file.type.startsWith('image/')) {
                alert('Silakan pilih file gambar (JPG, PNG, WEBP, dll).');
                return;
            }
            try {
                pendingImage = await compressImage(file);
                document.getElementById('image-preview').src = pendingImage;
                document.getElementById('image-preview-name').textContent = file.name || 'Gambar dari clipboard';
                let bar = document.getElementById('image-preview-bar');
                bar.classList.remove('hidden');
                bar.classList.add('flex');
            } catch (err) {
                alert(err.message);
            }
        }

        function handleFileSelect(event) {
            let file = event.target.files[0];
            setPendingImageFromFile(file);
            event.target.value = ''; // supaya file yang sama bisa dipilih ulang
        }

        function removePendingImage() {
            pendingImage = null;
            let bar = document.getElementById('image-preview-bar');
            bar.classList.add('hidden');
            bar.classList.remove('flex');
            document.getElementById('image-preview').src = '';
        }

        // Paste gambar (Ctrl+V) langsung ke kolom input
        document.getElementById('user-input').addEventListener('paste', function(e) {
            let items = (e.clipboardData || {}).items || [];
            for (let item of items) {
                if (item.type.startsWith('image/')) {
                    e.preventDefault();
                    setPendingImageFromFile(item.getAsFile());
                    break;
                }
            }
        });


        // --- PENYIMPANAN RIWAYAT OBROLAN (localStorage) ---
        const STORAGE_KEY = 'ujang_chats_v1';
        const ACTIVE_KEY = 'ujang_active_chat_v1';
        const MAX_CONVERSATIONS = 50;
        const MAX_MESSAGES_PER_CONV = 100;
        const DEFAULT_TITLE = 'Obrolan baru';

        let conversations = [];   // daftar obrolan tersimpan, terbaru di depan
        let currentConv = null;   // obrolan yang sedang dibuka

        function loadStore() {
            try {
                let raw = localStorage.getItem(STORAGE_KEY);
                let parsed = raw ? JSON.parse(raw) : [];
                conversations = Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                conversations = [];
            }
        }

        // Gambar TIDAK disimpan (terlalu besar untuk localStorage), hanya penanda hasImage
        function serializeConversations() {
            return conversations.map(c => ({
                id: c.id,
                title: c.title,
                createdAt: c.createdAt,
                updatedAt: c.updatedAt,
                messages: c.messages.map(m => ({
                    role: m.role,
                    content: m.content,
                    hasImage: !!(m.image || m.hasImage)
                }))
            }));
        }

        function saveStore() {
            for (let attempt = 0; attempt < 5; attempt++) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(serializeConversations()));
                    localStorage.setItem(ACTIVE_KEY, currentConv ? currentConv.id : '');
                    return;
                } catch (e) {
                    // Penyimpanan penuh: buang obrolan terlama (selain yang sedang dibuka), lalu coba lagi
                    let idx = conversations.length - 1;
                    while (idx >= 0 && conversations[idx] === currentConv) idx--;
                    if (idx < 0) return;
                    conversations.splice(idx, 1);
                }
            }
        }

        function createConversation() {
            let now = Date.now();
            return { id: 'c' + now + Math.random().toString(36).slice(2, 6), title: DEFAULT_TITLE, createdAt: now, updatedAt: now, messages: [] };
        }

        // Pindahkan obrolan aktif ke posisi teratas daftar
        function touchConversation(conv) {
            conv.updatedAt = Date.now();
            let idx = conversations.indexOf(conv);
            if (idx !== -1) conversations.splice(idx, 1);
            conversations.unshift(conv);
            if (conversations.length > MAX_CONVERSATIONS) conversations.length = MAX_CONVERSATIONS;
            if (conv.messages.length > MAX_MESSAGES_PER_CONV) {
                conv.messages.splice(0, conv.messages.length - MAX_MESSAGES_PER_CONV);
            }
        }


        // --- RENDER PESAN ---
        const WELCOME_HTML = `
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 shadow-md">UJ</div>
                <div class="bg-white border border-slate-200 p-3.5 rounded-2xl rounded-tl-sm text-xs sm:text-sm text-slate-700 shadow-sm max-w-[85%] leading-relaxed">
                    Tabe, <b>Bos Ganteng</b>! Abdi <b>UJANG E AY</b>, siap ngabantu Anjeun. Abdi ogé tiasa nganalisa gambar, klik ikon 📎 pikeun ngalampirkeun. Riwayat obrolan bakal diingat terus ku abdi!
                </div>
            </div>
        `;

        function userBubbleHtml(m) {
            let imageHtml = '';
            if (m.image) {
                imageHtml = `<img src="${m.image}" alt="Gambar terlampir" class="max-w-full max-h-48 rounded-xl mb-2 border border-white/30">`;
            } else if (m.hasImage) {
                imageHtml = `<div class="text-[11px] opacity-80 mb-1">🖼️ Gambar terlampir (tidak disimpan di riwayat)</div>`;
            }
            return `
                <div class="flex items-start gap-3 justify-end">
                    <div class="bg-blue-600 text-white p-3.5 rounded-2xl rounded-tr-sm text-xs sm:text-sm shadow-sm max-w-[85%]">
                        ${imageHtml}${escapeHtml(m.content)}
                    </div>
                    <div class="w-7 h-7 rounded-full bg-slate-700 text-white flex items-center justify-center font-bold text-[10px] shrink-0 shadow-md">U</div>
                </div>
            `;
        }

        function aiBubbleHtml(text) {
            let cleanHTML = DOMPurify.sanitize(marked.parse(text));
            return `
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 shadow-md">UJ</div>
                    <div class="bg-white border border-slate-200 p-3.5 rounded-2xl rounded-tl-sm text-xs sm:text-sm text-slate-700 shadow-sm max-w-[85%] leading-relaxed prose prose-sm max-w-none [&_table]:w-full [&_table]:border-collapse [&_th]:border [&_th]:border-slate-200 [&_th]:bg-slate-100 [&_th]:p-1.5 [&_td]:border [&_td]:border-slate-200 [&_td]:p-1.5">
                        ${cleanHTML}
                    </div>
                </div>
            `;
        }

        function errorBubbleHtml(text) {
            return `
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 shadow-md">UJ</div>
                    <div class="bg-red-50 border border-red-200 p-3.5 rounded-2xl rounded-tl-sm text-xs text-red-600 shadow-sm max-w-[85%] break-words">
                        ${escapeHtml(text)}
                    </div>
                </div>
            `;
        }

        function renderConversation() {
            let container = document.getElementById('chat-container');
            let html = WELCOME_HTML;
            currentConv.messages.forEach(m => {
                html += (m.role === 'user') ? userBubbleHtml(m) : aiBubbleHtml(m.content);
            });
            container.innerHTML = html;
            container.scrollTop = container.scrollHeight;
        }


        // --- PANEL RIWAYAT ---
        function formatChatDate(ts) {
            let d = new Date(ts);
            let now = new Date();
            if (d.toDateString() === now.toDateString()) {
                return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: d.getFullYear() === now.getFullYear() ? undefined : 'numeric' });
        }

        function renderHistoryList() {
            let list = document.getElementById('history-list');
            if (conversations.length === 0) {
                list.innerHTML = '<p class="text-center text-xs text-slate-400 py-8">Belum ada riwayat obrolan.</p>';
                return;
            }
            list.innerHTML = conversations.map(c => {
                let active = currentConv && c.id === currentConv.id;
                return `
                    <div class="group flex items-center gap-1 rounded-xl ${active ? 'bg-slate-100' : 'hover:bg-slate-50'} transition-all">
                        <button data-open="${escapeHtml(c.id)}" class="flex-1 min-w-0 text-left px-3 py-2.5">
                            <div class="text-xs sm:text-sm font-medium text-slate-800 truncate">${escapeHtml(c.title)}</div>
                            <div class="text-[11px] text-slate-400">${formatChatDate(c.updatedAt)} · ${c.messages.length} pesan</div>
                        </button>
                        <button data-delete="${escapeHtml(c.id)}" title="Hapus obrolan ini" class="p-2 mr-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
            }).join('');
        }

        document.getElementById('history-list').addEventListener('click', function(e) {
            let delBtn = e.target.closest('[data-delete]');
            if (delBtn) {
                deleteConversation(delBtn.getAttribute('data-delete'));
                return;
            }
            let openBtn = e.target.closest('[data-open]');
            if (openBtn) {
                openConversation(openBtn.getAttribute('data-open'));
            }
        });

        function openHistory() {
            renderHistoryList();
            let drawer = document.getElementById('history-drawer');
            drawer.classList.remove('hidden');
            drawer.classList.add('flex');
        }

        function closeHistory() {
            let drawer = document.getElementById('history-drawer');
            drawer.classList.add('hidden');
            drawer.classList.remove('flex');
        }

        function openConversation(id) {
            let conv = conversations.find(c => c.id === id);
            if (!conv) return;
            currentConv = conv;
            removePendingImage();
            renderConversation();
            closeHistory();
            saveStore();
        }

        function newChat() {
            currentConv = createConversation();
            removePendingImage();
            renderConversation();
            closeHistory();
            saveStore();
            document.getElementById('user-input').focus();
        }

        function deleteConversation(id) {
            let wasCurrent = currentConv && currentConv.id === id;
            conversations = conversations.filter(c => c.id !== id);
            if (wasCurrent) {
                currentConv = createConversation();
                renderConversation();
            }
            saveStore();
            renderHistoryList();
        }

        function clearAllHistory() {
            if (!confirm('Hapus semua riwayat obrolan? Tindakan ini tidak bisa dibatalkan.')) return;
            conversations = [];
            currentConv = createConversation();
            renderConversation();
            saveStore();
            renderHistoryList();
        }


        // --- SCRIPT UNTUK FITUR AI CHAT & MEMORY RIWAYAT ---

        // Gambar hanya dikirim bersama pesan TERAKHIR (hemat ukuran request & kuota)
        function buildPayloadMessages() {
            let msgs = currentConv.messages;
            let lastIndex = msgs.length - 1;
            return msgs.map((m, i) => {
                let msg = { role: m.role, content: m.content };
                if (m.image && i === lastIndex) msg.image = m.image;
                return msg;
            });
        }

        async function handleSendMessage(event) {
            event.preventDefault();
            
            let inputField = document.getElementById('user-input');
            let container = document.getElementById('chat-container');
            let sendBtn = document.getElementById('send-btn');
            let prompt = inputField.value.trim();
            let imageToSend = pendingImage;
            
            if (!prompt && !imageToSend) return;
            if (!prompt && imageToSend) prompt = 'Tolong analisa gambar ini.';

            let convAtSend = currentConv; // jaga-jaga jika pengguna pindah obrolan saat menunggu jawaban

            // 1. Simpan & tampilkan pesan user
            let historyEntry = { role: 'user', content: prompt };
            if (imageToSend) historyEntry.image = imageToSend;
            convAtSend.messages.push(historyEntry);
            if (convAtSend.title === DEFAULT_TITLE) {
                convAtSend.title = prompt.length > 40 ? prompt.slice(0, 40) + '…' : prompt;
            }
            touchConversation(convAtSend);
            saveStore();

            container.innerHTML += userBubbleHtml(historyEntry);
            inputField.value = '';
            removePendingImage();
            container.scrollTop = container.scrollHeight;

            // 2. Tampilkan indikator loading AI
            let loadingId = 'loading-' + Date.now();
            let loadingBubble = `
                <div id="${loadingId}" class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 shadow-md">UJ</div>
                    <div class="bg-white border border-slate-200 p-3.5 rounded-2xl rounded-tl-sm text-xs shadow-sm flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"></span>
                        <span class="w-2 h-2 bg-blue-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                        <span class="w-2 h-2 bg-blue-600 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                    </div>
                </div>
            `;
            container.innerHTML += loadingBubble;
            container.scrollTop = container.scrollHeight;
            sendBtn.disabled = true;

            let payload = buildPayloadMessages();

            try {
                // 3. Kirim riwayat chat (+ gambar terbaru) ke backend PHP
                let response = await fetch('api-chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ messages: payload })
                });

                let result = await response.json();
                let loadingEl = document.getElementById(loadingId);
                if (loadingEl) loadingEl.remove();

                let isSuccess = result.status === 'success' && result.reply;
                let aiReply = result.reply || 'Maaf, terjadi kesalahan pada sistem.';

                if (isSuccess) {
                    // Hanya jawaban sukses yang masuk riwayat
                    convAtSend.messages.push({ role: 'assistant', content: aiReply });
                    touchConversation(convAtSend);
                    saveStore();
                }

                if (currentConv === convAtSend) {
                    container.innerHTML += isSuccess ? aiBubbleHtml(aiReply) : errorBubbleHtml(aiReply);
                }

            } catch (error) {
                let loadingEl = document.getElementById(loadingId);
                if (loadingEl) loadingEl.remove();
                if (currentConv === convAtSend) {
                    container.innerHTML += errorBubbleHtml('Gagal terhubung ke backend AI (' + String(error) + '). Pastikan file api-chat.php sudah diperbarui.');
                }
            } finally {
                sendBtn.disabled = false;
                container.scrollTop = container.scrollHeight;
                inputField.focus();
            }
        }

        function escapeHtml(text) {
            let map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }


        // --- INISIALISASI: buka obrolan terakhir (jika ada), kalau tidak mulai obrolan baru ---
        loadStore();
        let lastActiveId = null;
        try { lastActiveId = localStorage.getItem(ACTIVE_KEY); } catch (e) {}
        currentConv = conversations.find(c => c.id === lastActiveId) || createConversation();
        renderConversation();
    </script>
</body>
</html>