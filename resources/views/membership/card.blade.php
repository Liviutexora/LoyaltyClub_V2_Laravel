@extends('layouts.ecosystem')

@section('title', 'Membership Card')

@push('styles')
    <style>
        .card-container { max-width: 400px; margin: 40px auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px 24px 24px 24px; text-align: center; }
        .club-title { font-size: 1.5rem; font-weight: 700; letter-spacing: 2px; color: #222; margin-bottom: 18px; }
        .user-name { font-size: 1.2rem; font-weight: 500; color: #333; margin-bottom: 8px; }
        .user-id, .legacy-id { font-size: 0.98rem; color: #888; margin-bottom: 2px; }
        .status { font-size: 1.05rem; color: #0a7d3b; font-weight: 600; margin-bottom: 18px; letter-spacing: 1px; }
        .qr-section { margin: 0 auto 10px auto; display: flex; justify-content: center; }
        .qr-section img, .qr-section svg { width: 220px; height: 220px; border-radius: 12px; background: #fafafa; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        @media (max-width: 500px) { .card-container { max-width: 98vw; padding: 12vw 2vw; } .qr-section img, .qr-section svg { width: 60vw; height: 60vw; } }
    </style>
@endpush

@section('content')
    <div class="card-container">
        <div class="club-title">Loyalty Club</div>
        <div class="user-name">{{ $name }}</div>
        <div class="user-id">User ID: {{ $id }}</div>
        <div class="legacy-id">Legacy ID: {{ $legacy_id }}</div>
        <div class="status">Status: {{ $status }}</div>
        <div class="qr-section">
            <img src="{{ $qr_image_url }}" alt="QR Code" />
        </div>
        <div style="margin-top:20px;">
            <button type="button" class="btn btn-outline-secondary" onclick="window.close()">Close</button>
        </div>
    </div>
@endsection
