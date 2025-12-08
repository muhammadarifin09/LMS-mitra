<style>
    /* Footer */
    .main-footer {
        background: #1a365d;
        color: white;
        padding: 50px 0 25px;
        margin-top: 100px;
        width: 100%;
        font-size: 14px;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .footer-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1.5fr;
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-section h3 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: white;
        border-bottom: 2px solid #2d74da;
        padding-bottom: 8px;
    }

    .footer-section h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: white;
    }

    .footer-address {
        line-height: 1.7;
        margin-bottom: 20px;
    }

    .footer-address p {
        margin-bottom: 8px;
    }

    .footer-links {
        list-style: none;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        transition: color 0.3s ease;
        font-size: 14px;
    }

    .footer-links a:hover {
        color: white;
        text-decoration: underline;
    }

    .footer-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.2);
        margin: 30px 0;
    }

    .footer-bottom {
        display: flex;
        justify-content: center;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
    }

    .bps-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: white;
    }

    .contact-info {
        margin-top: 15px;
    }

    .contact-info p {
        margin-bottom: 5px;
    }

    .berakhlak-container {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .berakhlak-image {
        max-width: 200px;
    }

    .berakhlak-links {
        list-style: none;
        margin-top: 10px;
    }

    .berakhlak-links li {
        margin-bottom: 8px;
    }

    .berakhlak-links a {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        transition: color 0.3s ease;
        font-size: 14px;
    }

    .berakhlak-links a:hover {
        color: white;
        text-decoration: underline;
    }

    /* ===== RESPONSIVE FIXES ===== */
    @media (max-width: 1200px) {
        .footer-content {
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
    }

    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr !important;
            gap: 30px;
        }
        
        .footer-section {
            text-align: left !important; /* Ubah dari center ke left */
            order: 0; /* Reset order */
        }
        
        /* Urutkan section sesuai kebutuhan */
        .footer-section:first-child {
            order: 1; /* bps-title + footer-address + berakhlak-container */
        }
        
        .footer-section:nth-child(3) {
            order: 2; /* Tentang Kami */
        }
        
        .footer-section:nth-child(2) {
            order: 3; /* Kontak Kami */
        }
        
        /* Style untuk elemen dalam section pertama */
        .bps-title {
            text-align: left;
            margin-bottom: 15px;
        }
        
        .footer-address {
            text-align: left;
            margin-bottom: 20px;
        }
        
        .berakhlak-container {
            text-align: left;
            margin-top: 20px;
        }
        
        .berakhlak-image {
            max-width: 150px !important;
            margin: 0 !important; /* Hilangkan margin center */
        }
        
        /* Style untuk section lainnya */
        .footer-section h3 {
            text-align: left;
            margin-bottom: 15px;
        }
        
        .contact-info {
            text-align: left;
        }
        
        .footer-links {
            text-align: left;
            padding-left: 0;
            list-style-position: inside;
        }
        
        .footer-links li {
            margin-bottom: 8px;
        }
        
        .footer-links a {
            text-align: left;
            justify-content: flex-start !important;
        }
        
        .main-footer {
            margin-top: 50px !important;
            padding: 30px 0 15px !important;
        }
        
        .footer-bottom {
            text-align: left !important;
            margin-top: 30px;
        }
        
        .copyright {
            text-align: left;
        }
    }

    @media (max-width: 576px) {
        .main-footer {
            padding: 25px 0 10px !important;
        }
        
        .footer-content {
            gap: 25px;
        }
        
        .berakhlak-image {
            max-width: 130px !important;
        }
        
        .bps-title {
            font-size: 1.3rem;
        }
        
        .footer-section h3 {
            font-size: 1.1rem;
        }
    }

    /* Prevent horizontal scroll */
    html, body {
        overflow-x: hidden;
        max-width: 100%;
    }

    /* Improve text readability on mobile */
    @media (max-width: 768px) {
        body {
            font-size: 14px;
            line-height: 1.5;
        }
    }
</style>

<!-- Footer -->
<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-content">
            <!-- Alamat BPS + BerAKHLAK -->
            <div class="footer-section">
                <div class="bps-title">BADAN PUSAT STATISTIK</div>
                <div class="footer-address">
                    <p>Badan Pusat Statistik Kabupaten Tanah Laut (BPS-Statistics of Tanah Laut Regency)</p>
                    <p>Alamat: Jalan A. Syairani No. 9 Pelaihari Kab. Tanah Laut</p>
                    <p>Prov. Kalimantan Selatan</p>
                    <p>76914</p>
                    <p>Indonesia</p>
                </div>
                
                <!-- Gambar BerAKHLAK -->
                <div class="berakhlak-container">
                    <img src="{{ asset('img/cover.jpg') }}" alt="BerAKHLAK" class="berakhlak-image">
                    <ul class="berakhlak-links">
                </div>
            </div>

            <!-- Kontak Kami -->
            <div class="footer-section">
                <h3>Kontak Kami</h3>
                <div class="contact-info">
                    <p>Telepon: +62 512 21092</p>
                    <p>Fax: +62 512 3113</p>
                    <p>Email: bps6301@bps.go.id</p>
                    <p>bps6301@gmail.com</p>
                </div>
            </div>

            <!-- Tentang Kami -->
            <div class="footer-section">
                <h3>Tentang Kami</h3>
                <ul class="footer-links">
                    <li><a href="https://ppid.bps.go.id/app/konten/6301/Profil-BPS.html?_gl=1*15t609r*_ga*MjQxOTY0MDAzLjE3NjEyNzM4MzU.*_ga_XXTTVXWHDB*czE3NjEyNzM4MzQkbzEkZzAkdTE3NjEyNzM4MzQkajYwJGwwJGgw">Profil BPS</a></li>
                    <li><a href="https://ppid.bps.go.id/?mfd=6301&_gl=1*1yli45g*_ga*MTQyMzAzNDgwMC4xNzQwMjk0NzU4*_ga_XXTTVXWHDB*czE3NjQyMDY0ODIkbzEwJGcwJHQxNzY0MjA2NDkyJGo1MCRsMCRoMA..">PPID</a></li>
                    <li><a href="https://ppid.bps.go.id/app/konten/0000/Layanan-BPS.html?_gl=1*8lxw4a*_ga*MTQyMzAzNDgwMC4xNzQwMjk0NzU4*_ga_XXTTVXWHDB*czE3NjQyMDY0ODIkbzEwJGcwJHQxNzY0MjA2NDkyJGo1MCRsMCRoMA..#pills-3">Kebijakan Diseminasi</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                Hak Cipta © 2023 Badan Pusat Statistik
            </div>
        </div>
    </div>
</footer>

<script>
// Script untuk menangani gambar yang tidak ditemukan
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.berakhlak-image');
    let imageFound = false;
    
    images.forEach(img => {
        img.onerror = function() {
            this.style.display = 'none';
        };
        img.onload = function() {
            imageFound = true;
            // Sembunyikan gambar lainnya yang berhasil load
            images.forEach(otherImg => {
                if (otherImg !== this) {
                    otherImg.style.display = 'none';
                }
            });
        };
    });
});
</script>