@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Beranda LMS BPS Tanah Laut</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-primary">Hari Ini</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Minggu Ini</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Bulan Ini</button>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <h2 class="card-title display-6 text-primary">92.185</h2>
                    <p class="card-text fs-6">Kirkulustan - Alumni DTS 2023</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <h2 class="card-title display-6 text-success">101.993</h2>
                    <p class="card-text fs-6">Kirkulustan - Alumni DTS 2024</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Pendaftaran -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-body">
                    <h2 class="card-title display-6 text-warning">47.118</h2>
                    <p class="card-text fs-6">Sudhir menevidesikan Pkutshan Moto Sisti - Upakite (17.03.2025)</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-body">
                    <h2 class="card-title display-6 text-info">37.984</h2>
                    <p class="card-text fs-6">Bidun Menevidesikan Pkutshan Moto Sisti - Upakite (17.03.2025)</p>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <!-- Informasi Penting -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informasi Penting</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Avoida Gigena demnasa-senari menyasa para partner di menyagi, andova usuki.senari och senaria 
                        Senaria Giorna LMS mu, Senaria akut kenin selang di pinnata nataluk ne dalam sanma seniatus.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Alumni dan Program -->
    <div class="row">
        <!-- Alumni -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">Alumni</h6>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong>Remembrant Remindered after Digital</strong></p>
                    <p class="card-text">2 Million Mondials Best Kit - 5 Million Pump, VST ID</p>
                </div>
            </div>
        </div>

        <!-- Prasad Last -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">Prasad Last</h6>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong>Digital Talent Scholarship</strong></p>
                    <p class="card-text">Remembrant Remindered after Digital</p>
                    <p class="card-text">Sultan Pro-geratningen: 2024 Saturday</p>
                </div>
            </div>
        </div>

        <!-- Akses Cepat -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">Akses Cepat</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><strong>Government Transformation of Academy (CTA)</strong></li>
                        <li>Tamil-Granular Academy (BCA)</li>
                        <li>International Social Graduate Academy (GCCA)</li>
                        <li>Professional Academy (PBSA)</li>
                        <li>Digital Entrepreneurship Academy (BCA)</li>
                        <li>Digital Leadership Academy (CPA)</li>
                        <li>Thomson Academy (PTA)</li>
                        <li>Tamil-Security Academy (TPA)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="text-center text-muted">
                <small>Activate Windows - Go to Settings to activate Windows.</small>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.display-6 {
    font-size: 2rem;
    font-weight: 300;
    line-height: 1.2;
}

.card-header {
    font-weight: 600;
}

.list-unstyled li {
    margin-bottom: 5px;
    font-size: 0.9rem;
}
</style>
@endsection