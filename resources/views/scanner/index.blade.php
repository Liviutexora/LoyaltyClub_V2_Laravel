@extends('layouts.ecosystem')

@section('title', 'QR Scanner')

@push('styles')
<style>

.scanner-modal{
    max-width:460px;
    margin:40px auto;
    background:#fff;
    border-radius:6px;
    box-shadow:0 4px 24px rgba(0,0,0,.15);
    overflow:hidden;
}

.scanner-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 22px;
    border-bottom:1px solid #dee2e6;
}

.scanner-header h4{
    margin:0;
    font-size:18px;
    font-weight:700;
    color:#343a40;
}

#scanner-close-x{
    border:none;
    background:none;
    font-size:34px;
    color:#777;
    cursor:pointer;
    line-height:1;
}

.scanner-body{
    padding:18px;
}

#qr-reader{
    min-height:320px;
}

.scanner-footer{
    border-top:1px solid #dee2e6;
    padding:16px;
    text-align:center;
}

#scanner-cancel-btn{
    background:#6c757d;
    color:#fff;
    border:none;
    padding:10px 28px;
    border-radius:4px;
    cursor:pointer;
    font-size:16px;
}

</style>
@endpush

@section('content')
<div class="scanner-modal">

    <div class="scanner-header">
        <h4>Scan Loyalty QR</h4>

        <button id="scanner-close-x" type="button">
            &times;
        </button>
    </div>

    <div class="scanner-body">
        <div id="qr-reader"></div>
    </div>

    <div class="scanner-footer">
        <button id="scanner-cancel-btn" type="button">
            Cancel
        </button>
    </div>

</div>

    @include('scanner.partials.transaction-modal')

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let qrScanner = null;
    let scannerStarted = false;
    let processingScan = false;

    function closeScannerUI() {

        const modal = document.querySelector('.scanner-modal');

        if (modal) {
            modal.style.display = 'none';
        }
    }

    function stopScanner() {

        if (!qrScanner) {
            closeScannerUI();
            return;
        }

        qrScanner.stop()
            .then(function(){
                qrScanner.clear();
                qrScanner = null;
                scannerStarted = false;
                closeScannerUI();
            })
            .catch(function(){
                qrScanner = null;
                scannerStarted = false;
                closeScannerUI();
            });
    }

    function startScanner() {

        if (scannerStarted) return;

        qrScanner = new Html5Qrcode("qr-reader");

        Html5Qrcode.getCameras()
            .then(function(cameras){

                if (!cameras || cameras.length === 0) {
                    console.log('No camera found');
                    return;
                }

                scannerStarted = true;

                qrScanner.start(
                    cameras[0].id,
                    {
                        fps: 10,
                        qrbox: 250
                    },
                    function(decodedText){
                        if (processingScan) {
                            return;
                        }
                        processingScan = true;

                        console.log('QR detected:', decodedText);

                        window.lastScannedQr = decodedText;

                        // Lookup QR via POST
                        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
                        fetch('/scanner/lookup', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                            },
                            body: JSON.stringify({ qr: decodedText })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Lookup response:', data);
                            if (data.success === true && data.user) {
                                const nameEl = document.getElementById('qr-client-name');
                                if (nameEl) nameEl.value = data.user.name;
                                const idEl = document.getElementById('qr-legacy-id');
                                if (idEl) idEl.value = data.user.legacy_user_id;

                                // Open modal and hide scanner UI immediately, then stop and clear scanner async
                                const modalEl = document.getElementById('transactionModal');
                                if (modalEl) {
                                    $('#transactionModal').modal('show');
                                    closeScannerUI();
                                    // Reset processingScan when modal is closed
                                    $('#transactionModal').one('hidden.bs.modal', function () {

                                        processingScan = false;

                                        const scannerModal = document.querySelector('.scanner-modal');

                                        if (scannerModal) {
                                            scannerModal.style.display = 'block';
                                        }

                                        const qrReader = document.getElementById('qr-reader');

                                        if (qrReader) {
                                            qrReader.innerHTML = '';
                                        }

                                        qrScanner = null;
                                        scannerStarted = false;

                                        startScanner();
                                    });
                                }

                                if (qrScanner) {
                                    qrScanner.stop().then(function () {
                                        qrScanner.clear();
                                        qrScanner = null;
                                        scannerStarted = false;
                                    }).catch(function () {
                                        qrScanner = null;
                                        scannerStarted = false;
                                    });
                                }
                            }
                            else {
                                processingScan = false;
                                alert('Client not found. Please scan a valid Loyalty QR.');
                            }
                        })
                        .catch(error => {
                            processingScan = false;
                            console.error('Lookup error:', error);
                        });
                    },
                    function(error){}
                );
            })
            .catch(function(err){
                console.log(err);
            });
    }

    startScanner();

    document.getElementById('scanner-cancel-btn').addEventListener('click', function(){

        if (!qrScanner) return;

        stopScanner();
    });

    document.getElementById('scanner-close-x').addEventListener('click', function(){

        if (!qrScanner) return;

        stopScanner();
    });
});
</script>
@endpush
