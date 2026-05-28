<div id="transactionModal" class="modal" style="display:none;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:460px;">
        <div class="modal-content">

            <div class="modal-header pb-3 pt-3" style="background:#eaf2ff; border-bottom:1px solid #c9dcff;">
                <div class="w-100">
                    <h4 class="mb-1 text-primary font-weight-bold" style="letter-spacing:0.01em;">
                        {{ $company_name ?? 'Company' }}
                    </h4>

                    <div class="mb-0" style="margin-top:2px; font-size:1.05em; color:#6c757d; letter-spacing:0.01em;">
                        <span style="display:inline-block; margin-top:2px;">
                            Validate Loyalty Transaction
                        </span>
                    </div>
                </div>
                <button class="close" type="button" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <div class="col-md-8 mb-3">
                        <label>Client Name</label>

                        <input
                            type="text"
                            class="form-control"
                            id="qr-client-name"
                            readonly
                            placeholder="Client identified after scan"
                            style="background-color:#f8f9fa; color:#495057;">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Loyalty ID</label>

                        <input
                            type="text"
                            class="form-control"
                            id="qr-legacy-id"
                            readonly
                            placeholder="Legacy ID"
                            style="background-color:#f8f9fa; color:#495057;">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Transaction Amount *</label>

                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="transaction-amount"
                                placeholder="0.00"
                                style="background-color:#fff9e6;">

                            <div class="input-group-append">
                                <span class="input-group-text">RON</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Loyalty %</label>

                        <input
                            type="text"
                            class="form-control"
                            id="company-loyalty-percent"
                            readonly
                            placeholder="%"
                            value="{{ $company_loyalty_percent ?? '' }}"
                            style="background-color:#f8f9fa; color:#495057;">
                    </div>

                    <div class="col-md-8 mb-3">
                        <label>Loyalty Value</label>

                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="loyalty-value"
                                readonly
                                placeholder="0.00"
                                style="background-color:#f8f9fa; color:#495057;">

                            <div class="input-group-append">
                                <span class="input-group-text">RON</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label>
                            Invoice / Fiscal Receipt Series
                            <small class="text-muted">(Optional)</small>
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Optional invoice / receipt reference"
                            style="background-color:#fffdf2;">
                    </div>

                </div>
            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-cancel-soft"
                    type="button"
                    data-dismiss="modal">
                    Cancel
                </button>

                <button
                    class="btn btn-primary"
                    type="button">
                    Validate Transaction
                </button>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var amountInput = document.getElementById('transaction-amount');
    var percentInput = document.getElementById('company-loyalty-percent');
    var loyaltyValueInput = document.getElementById('loyalty-value');

    if (!amountInput || !percentInput || !loyaltyValueInput) {
        return;
    }

    amountInput.addEventListener('input', function () {
        var amount = parseFloat((amountInput.value || '').replace(',', '.'));
        var loyaltyPercent = parseFloat((percentInput.value || '').replace(',', '.'));

        if (isNaN(amount) || isNaN(loyaltyPercent)) {
            loyaltyValueInput.value = '';
            loyaltyValueInput.style.backgroundColor = '#f8f9fa';
            return;
        }

        var loyaltyValue = amount * loyaltyPercent / 100;
        loyaltyValueInput.value = loyaltyValue.toFixed(2);
        loyaltyValueInput.style.backgroundColor = loyaltyValue > 0 ? '#e9f8ee' : '#f8f9fa';
    });
});
</script>