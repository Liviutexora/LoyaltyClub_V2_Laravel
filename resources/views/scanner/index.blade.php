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

                        console.log('QR detected:', decodedText);
                        window.lastScannedQr = decodedText;

                        if (!qrScanner) return;

                        stopScanner();

                        setTimeout(function () {
                            const modal = document.getElementById('transactionModal');
                                if (modal) {
                                    modal.classList.add('show');
                                    modal.style.display = 'block';
                                    modal.setAttribute('aria-modal', 'true');
                                    modal.removeAttribute('aria-hidden');
                                    document.body.classList.add('modal-open');
                            }
                        }, 300);
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
