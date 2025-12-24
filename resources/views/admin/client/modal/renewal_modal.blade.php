<div class="modal fade" id="renewalClientModal" tabindex="-1" aria-labelledby="renewalClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewalClientModalLabel">Renewal for {{ $client->fullname }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.add.renewal.client.request') }}" method="POST" id="addRenewalClientForm"
                class="needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column: Personal Info -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Personal Information</h6>

                            <div class="card-body text-left">

                                <!-- Profile Image -->
                                <img src="http://pluspng.com/img-png/png-user-icon-circled-user-icon-2240.png"
                                    alt="User Avatar" class="rounded-circle mb-3 border"
                                    style="height: 100px; width: 100px;">


                                <!-- User Details -->
                                <p class="fw-bold mb-1">
                                    <i class="fa fa-user me-1 text-primary"></i>
                                    <span style="font-size: 10px">{{ $client->fullname }}</span>
                                </p>


                                <p class="fw-bold mb-1">
                                    <i class="fa fa-venus-mars me-1 text-primary"></i>
                                    <span style="font-size: 10px">{{ $client->gender }}</span>
                                </p>

                                <p class="fw-bold mb-1">
                                    <i class="fa fa-phone me-1 text-primary"></i>
                                    <span style="font-size: 10px">{{ $client->phone }}</span>
                                </p>

                                <p class="fw-bold mb-0">
                                    <i class="fa fa-map-marker-alt me-1 text-primary"></i>
                                    <span style="font-size: 10px">{{ $client->address }}</span>
                                </p>

                                <h6 style="font-size: 12px" class="text-primary mt-3">Last Loan</h6>
                                <table style="font-size: 11px; border: 1px solid #ff6b35 !important"
                                    class="table table-bordered" style="font-size: 11px;">
                                    <thead>
                                        <tr>
                                            <th>Profiling PN</th>
                                            <th>Released PN</th>
                                            <th>Amount</th>
                                            <th>Period</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($last_loans)
                                            <tr>
                                                <td>{{ $last_loans->pn_number }}</td>
                                                <td>{{ $last_loans->release_number }}</td>
                                                <td>₱{{ number_format($last_loans->loan_amount, 2) }}</td>
                                                <td>{{ $last_loans->loan_from }} → {{ $last_loans->loan_to }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No previous loan
                                                    found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <!-- Right Column: Account Info -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Renewal Loan Form</h6>
                            <div class="row">
                                <input type="hidden" name="client_id" value="{{ $client->id }}">

                                <div class="col-lg-12 mb-3">
                                    <label for="pn_number" class="form-label">PN
                                        Number <span style="color: rgb(126, 30, 30)">*</span></label>
                                    <input type="text" class="form-control" id="pn_number" name="pn_number" required>
                                    <div class="invalid-feedback">
                                        Please enter pn number.
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <label for="release_number" class="form-label">Release
                                        Number <span style="color: rgb(126, 30, 30)">*</span></label>
                                    <input type="text" class="form-control" id="release_number" name="release_number"
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter release number.
                                    </div>
                                </div>

                                <!-- Loan From -->
                                <div class="col-lg-6 mb-3">
                                    <label for="loan_from" class="form-label">Loan From <span
                                            style="color: rgb(126, 30, 30)">*</span></label>
                                    <input type="date" class="form-control" id="loan_from" name="loan_from" required>
                                    <div class="invalid-feedback">Please enter a valid date.
                                    </div>
                                </div>

                                <!-- Loan To -->
                                <div class="col-lg-6 mb-3">
                                    <label for="loan_to" class="form-label">Loan To <span
                                            style="color: rgb(126, 30, 30)">*</span></label>
                                    <input type="date" class="form-control" id="loan_to" name="loan_to" required>
                                    <div class="invalid-feedback">Please enter a valid date.
                                    </div>
                                </div>
                                <!-- Loan Amount -->
                                <div class="col-lg-6 mb-3">
                                    <label for="loan_amount" class="form-label">Loan
                                        Amount <span style="color: rgb(126, 30, 30)">*</span></label>
                                    <input type="number" class="form-control" id="loan_amount" name="loan_amount"
                                        required min="0" step="0.01">
                                    <div class="invalid-feedback">
                                        Please enter amount.
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="daily" class="form-label">Daily
                                        Payment <span style="color: rgb(126, 30, 30)">*</span></label>
                                    <input type="number" class="form-control" id="daily" name="daily"
                                        required min="0" step="0.01">
                                    <div class="invalid-feedback">
                                        Please enter payment.
                                    </div>
                                </div>
                                <!-- Loan Terms -->
                                <div class="col-lg-6 mb-3">
                                    <label for="loan_terms" class="form-label">Loan
                                        Terms</label>
                                    <input style="background-color: gray; color: white !important;" type="text"
                                        class="form-control" id="loan_terms" name="loan_terms" value="100"
                                        placeholder="100" readonly required>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.getElementById('locationSelect').addEventListener('change', function() {
        let selectedLocation = this.value;
        let areaSelect = document.getElementById('areaSelect');

        areaSelect.value = "";

        Array.from(areaSelect.options).forEach(option => {
            if (!option.dataset.location) return;

            option.style.display =
                option.dataset.location === selectedLocation ? 'block' : 'none';
        });
    });
</script>
