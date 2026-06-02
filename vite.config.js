import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import os from "os";

// Fungsi untuk mendeteksi IP WiFi/LAN laptop secara otomatis
function getNetworkIp() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        // Abaikan interface virtual (jika kamu pakai Docker atau VM)
        if (name.startsWith("docker") || name.startsWith("veth")) continue;

        for (const iface of interfaces[name]) {
            // Ambil IPv4 dan pastikan bukan localhost (127.0.0.1)
            if (iface.family === "IPv4" && !iface.internal) {
                return iface.address;
            }
        }
    }
    return "localhost";
}

export default defineConfig({
    server: {
        host: "0.0.0.0", // Mengizinkan akses dari jaringan luar
        port: 5173,
        strictPort: true,
        cors: true, // <-- TAMBAHKAN BARIS INI UNTUK MENGATASI CORS
        hmr: {
            // HMR akan selalu menggunakan IP WiFi kamu yang sedang aktif
            host: getNetworkIp(),
        },
    },

    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
