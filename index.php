<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monsev - Server Monitoring Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen p-6 md:p-10 font-sans selection:bg-blue-500 selection:text-white">
    <div class="max-w-6xl mx-auto">
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

    <script>
        async function fetchServerStatus() {
            let icon = document.getElementById('refresh-icon');
            icon.classList.add('rotate-180');
            setTimeout(() => icon.classList.remove('rotate-180'), 500);

            try {
                let response = await fetch('api-monitor.php');
                let result = await response.json();
                
                let container = document.getElementById('server-container');
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

                    // Fitur tambahan: GeoServer hanya di Server 38
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

                    // Status Web Server
                    let isWebActive = srv.web_server === 'Active';
                    let webColor = isWebActive ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-slate-500 bg-slate-100 border-slate-200';
                    
                    let card = `
                        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                            <div>
                                <!-- Header Kartu -->
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

                                <!-- Metrik Performa -->
                                <div class="space-y-3 text-sm text-slate-600">
                                    <!-- CPU Load -->
                                    <div class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                        <span class="text-slate-500 font-medium">CPU Load (1m / 5m):</span> 
                                        <span class="font-mono font-semibold text-amber-600">${srv.cpu_load ?? 'N/A'} / ${srv.cpu_load_5m ?? 'N/A'}</span>
                                    </div>

                                    <!-- RAM Usage -->
                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">RAM Usage:</span> 
                                            <span class="font-mono font-semibold text-slate-700">${srv.ram_used ?? '-'} / ${srv.ram_total ?? '-'} (${srv.ram_pct ?? '0%'})</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: ${ramPctNum}%"></div>
                                        </div>
                                    </div>

                                    <!-- Swap Memory -->
                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">Swap Usage:</span> 
                                            <span class="font-mono font-semibold text-slate-700">${srv.swap_used ?? '-'} / ${srv.swap_total ?? '-'} (${srv.swap_pct ?? '0%'})</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: ${swapPctNum}%"></div>
                                        </div>
                                    </div>

                                    <!-- Disk Usage -->
                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">Disk Usage:</span> 
                                            <span class="font-mono font-semibold text-slate-700">${srv.disk_used ?? '-'} / ${srv.disk_total ?? '-'} (${srv.disk_pct ?? '0%'})</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: ${diskPctNum}%"></div>
                                        </div>
                                    </div>

                                    <!-- Status Web Server -->
                                    <div class="flex justify-between items-center py-2 px-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                                        <span class="text-slate-500 font-medium">Web Server (Nginx/Apache):</span> 
                                        <span class="px-2 py-0.5 font-semibold rounded-md border ${webColor}">
                                            ${srv.web_server ?? 'Offline'}
                                        </span>
                                    </div>

                                    <!-- GeoServer (Hanya Server 38) -->
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

        // Panggil saat halaman pertama dibuka
        fetchServerStatus();
        
        // Auto refresh tiap 10 detik
        setInterval(fetchServerStatus, 10000);
    </script>
</body>
</html>