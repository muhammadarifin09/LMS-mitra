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
                    <div class="contact-info">
                        <p>Telepon: +62 512 21092</p>
                        <p>Fax: +62 512 3113</p>
                        <p>Email: bps6301@bps.go.id</p>
                        <p>bps6301@gmail.com</p>
                    </div>
                    
                    <!-- Gambar BerAKHLAK dan Manual S&K Daftar Tarakan -->
                    <div class="berakhlak-container">   
                        <!-- Coba berbagai path yang mungkin -->
                        <img src="/img/berakhlak-logo.png" alt="BerAKHLAK" class="berakhlak-image" onerror="this.style.display='none'">

                        
                        <ul class="berakhlak-links">
                            <li><a href="#">Manual S&K Daftar Tarakan</a></li>
                        </ul>
                    </div>
                </div>

            <!-- Tentang Kami -->
            <div class="footer-section">
                <h3>Tentang Kami</h3>
                <ul class="footer-links">
                    <li><a href="https://ppid.bps.go.id/app/konten/6301/Profil-BPS.html?_gl=1*15t609r*_ga*MjQxOTY0MDAzLjE3NjEyNzM4MzU.*_ga_XXTTVXWHDB*czE3NjEyNzM4MzQkbzEkZzAkdTE3NjEyNzM4MzQkajYwJGwwJGgw">Profil BPS</a></li>
                    <li><a href="#">PPID</a></li>
                    <li><a href="#">Kebijakan Diseminasi</a></li>
                </ul>
            </div>

            <!-- Tautan Lainnya -->
            <div class="footer-section">
                <h3>Tautan Lainnya</h3>
                <ul class="footer-links">
                    <li><a href="#">ASEAN Stats</a></li>
                    <li><a href="#">Forum Masyarakat Statistik</a></li>
                    <li><a href="#">Reformasi Birokrasi</a></li>
                    <li><a href="#">Layanan Pengaduan Secara Elektronik</a></li>
                    <li><a href="#">Politeknik Statistika STIS</a></li>
                    <li><a href="#">Pusdiklat BPS</a></li>
                    <li><a href="#">JDIH BPS</a></li>
                </ul>
            </div>

            <!-- Government Public Relation -->
            <div class="footer-section">
                <h3>Government Public Relation</h3>
                <div class="news-item">
                    <div class="news-date">21 October 2025, 19:23 WEB</div>
                    <div class="news-title">Sertifikasi Pemerintah Indonesia: Mendorong 18.805 UMKM dan Sektor Tenaga Kerja</div>
                </div>
                <div class="news-item">
                    <div class="news-date">21 October 2025, 19:22 WEB</div>
                    <div class="news-title">Sertifikasi Pemerintah Indonesia: Capai Swasembada 225 Ribu Hektar, Target 480 Ribu Hektar Tahun Depan</div>
                </div>
            </div>
        </div>

        <div class="footer-divider"></div>

        <div class="footer-bottom">
            <div class="copyright">
                Hak Cipta © 2023 Badan Pusat Statistik
            </div>
        </div>
    </div>
</footer>

<style>
    /* Footer */
    .main-footer {
        background: #1a365d;
        color: white;
        padding: 50px 0 25px;
        margin-top: auto;
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

    .news-item {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .news-date {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 5px;
    }

    .news-title {
        font-weight: 500;
        line-height: 1.4;
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
        position: relative;
    }

    .berakhlak-image {
        max-width: 200px;
        height: auto;
        margin-bottom: 15px;
        display: block;


    }

    /* Style untuk fallback image */
    .image-fallback {
        width: 200px;
        height: 80px;
        background: linear-gradient(135deg, #2d74da, #1a365d);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        border-radius: 4px;
        font-weight: bold;
        border: 1px solid rgba(255, 255, 255, 0.2);
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

    /* Responsif */
    @media (max-width: 1200px) {
        .footer-content {
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
    }

    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .footer-container {
            padding: 0 15px;
        }
        
        .berakhlak-image {
            max-width: 150px;
        }
        
        .image-fallback {
            width: 150px;
            height: 60px;
            font-size: 14px;
        }
    }
</style>

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
    
    // Jika tidak ada gambar yang berhasil load setelah 1 detik, tampilkan fallback
    setTimeout(() => {
        if (!imageFound) {
            const fallback = document.querySelector('.image-fallback');
            if (fallback) {
                fallback.style.display = 'flex';
            }
        }
    }, 1000);
});
</script>