@extends('mitra.layouts.app')

@php
    $noSidebar = true;
@endphp

@section('title', 'Sertifikat Tidak Valid')

<style>
    .container {
        text-align: center; 
        margin-top: 130px;
    }

    /* ===== RESPONSIVE DESIGN UNTUK MOBILE (≤500px) ===== */
    @media (max-width: 500px) {
        .main-content {
            padding: 15px 15px !important;
            margin: 10px 10px !important;
            border-radius: 15px !important;
            max-width: 95% !important;
            min-height: unset !important;
            height: auto !important;
        }

        /* Center vertical + horizontal */
        .container {
            margin: 0 !important;
            height: calc(100vh - 40px); /* ikut padding main-content */
            min-height: auto !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
        }
        
    }
</style>
@section('content')
<div class="container text-center">
    <div class="display-4 text-danger mb-3">
        <i class="fas fa-times-circle"></i>
    </div>
    <h2 class="fw-bold text-danger">Sertifikat Tidak Valid</h2>
    <p class="text-muted">
        ID kredensial ini tidak terdaftar di sistem kami.
    </p>
</div>
@endsection
