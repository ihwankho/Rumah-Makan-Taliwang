import "./bootstrap";
import "./echo";

// === LOGIKA REAL-TIME UNTUK DAPUR ===
if (window.location.pathname.includes("dapur")) {
    // Beri jeda 0.5 detik agar Echo siap sepenuhnya
    setTimeout(() => {
        window.Echo.channel("dapur-channel").listen(
            "PesananBaruDibuat",
            (event) => {
                // elemen div untuk notifikasi
                const toastDiv = document.createElement("div");
                toastDiv.id = "realtime-toast";

                // 
                toastDiv.className =
                    "fixed top-6 left-1/2 transform -translate-x-1/2 z-[100] transition-all duration-500 ease-in-out opacity-0 -translate-y-5";

                // 
                toastDiv.innerHTML = `
                    <div class="bg-blue-100 border border-green-400-400 text-green-800 px-6 py-3 rounded-2xl shadow-xl text-lg font-bold flex items-center gap-4">
                        <span>🔔 ${event.pesan} (Meja ${event.nomor_meja})</span>
                        
                        <button onclick="window.location.reload()" class="text-blue-600 hover:text-blue-900 text-2xl leading-none focus:outline-none">
                            &times;
                        </button>
                    </div>
                `;

                // notifikasi ke dalam layar 
                document.body.appendChild(toastDiv);

                // animasi "Turun & Muncul"
                setTimeout(() => {
                    toastDiv.classList.remove("opacity-0", "-translate-y-5");
                }, 50);

                //  hilangkan dan reload halamannya
                setTimeout(() => {
                    toastDiv.classList.add("opacity-0", "-translate-y-5"); // Animasi naik

                    // refresh untuk memunculkan antrian baru
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }, 3000);
            },
        );
    }, 500);
}
// ==========================================
// 2. LOGIKA REAL-TIME UNTUK KASIR
// ==========================================
if (window.location.pathname.includes("kasir")) {
    setTimeout(() => {
        //
        window.Echo.channel("dapur-channel").listen(
            "PesananBaruDibuat",
            (event) => {
                const toastDivKasir = document.createElement("div");
                toastDivKasir.id = "realtime-toast-kasir";
                toastDivKasir.className =
                    "fixed top-6 left-1/2 transform -translate-x-1/2 z-[100] transition-all duration-500 ease-in-out opacity-0 -translate-y-5";

                // Desain notifikasi Kasir
                toastDivKasir.innerHTML = `
                    <div class="bg-indigo-100 border border-indigo-400 text-indigo-800 px-6 py-3 rounded-2xl shadow-xl text-lg font-bold flex items-center gap-4">
                        <span>🛒 Meja ${event.nomor_meja} Baru Saja Memesan!</span>
                    </div>
                `;

                document.body.appendChild(toastDivKasir);

                setTimeout(() => {
                    toastDivKasir.classList.remove(
                        "opacity-0",
                        "-translate-y-5",
                    );
                }, 50);

                // Reload halaman kasir setelah 2.5 detik
                setTimeout(() => {
                    toastDivKasir.classList.add("opacity-0", "-translate-y-5");
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }, 2500);
            },
        );
    }, 500);
}
