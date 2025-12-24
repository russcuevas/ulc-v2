<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Add Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.add.client.request') }}" method="POST" id="addClientForm"
                class="needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column: Personal Info -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Personal Information</h6>
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name <span
                                        style="color: rgb(126, 30, 30)">*</span></label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required>
                                <div class="invalid-feedback">
                                    Please enter full name.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone <span
                                        style="color: rgb(126, 30, 30)">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" required
                                    pattern="\d{11}" minlength="11" maxlength="11">
                                <div class="invalid-feedback">
                                    Please enter a valid phone number (11 digits required).
                                </div>
                            </div>


                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span
                                        style="color: rgb(126, 30, 30)">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" required>
                                <div class="invalid-feedback">
                                    Please enter an address.
                                </div>
                            </div>
                            <div class="row">
                                <!-- Loan From -->
                                <div class="col-lg-7 mb-3">
                                    <label class="form-label">Select Location <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="locationSelect" required>
                                        <option value="" disabled selected>SELECT LOCATION</option>

                                        @foreach ($locations as $location)
                                            <option value="{{ $location->location_name }}">
                                                {{ $location->location_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Select an location.
                                    </div>

                                    <label class="form-label">Select Area <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="area_id" id="areaSelect" required>
                                        <option value="" disabled selected>SELECT AREA</option>

                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}"
                                                data-location="{{ $area->location_name }}">
                                                {{ $area->areas_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="invalid-feedback">
                                        Select an area.
                                    </div>
                                </div>

                                <!-- Loan To -->
                                <div class="col-lg-5 mb-3">
                                    <label class="form-label d-block">Gender <span
                                            style="color: rgb(126, 30, 30)">*</span></label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="genderMale"
                                            value="Male" checked>
                                        <label class="form-check-label" for="genderMale">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="genderFemale"
                                            value="Female">
                                        <label class="form-check-label" for="genderFemale">Female</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Right Column: Account Info -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Loan Information</h6>
                            <div class="row">
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
                                    <input type="text" class="form-control" id="release_number"
                                        name="release_number" required>
                                    <div class="invalid-feedback">
                                        Please enter release number.
                                    </div>
                                </div>

                                <!-- Loan From -->
                                <div class="col-lg-6 mb-3">
                                    <label for="loan_from" class="form-label">Loan From <span
                                            style="color: rgb(126, 30, 30)">*</span></label>
                                    <input type="date" class="form-control" id="loan_from" name="loan_from"
                                        required>
                                    <div class="invalid-feedback">Please enter a valid date.
                                    </div>
                                </div>

                                <!-- Loan To -->
                                <div class="col-lg-6 mb-3">
                                    <label for="loan_to" class="form-label">Loan To <span
                                            style="color: rgb(126, 30, 30)">*</span></label>
                                    <input type="date" class="form-control" id="loan_to" name="loan_to"
                                        required>
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
