<div class="modal fade" id="editSecretaryModal{{ $secretary->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-top modal-dialog-scrollable">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.secretary.update', $secretary->id) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Secretary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <!-- LEFT: PERSONAL INFORMATION -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Personal Information</h6>

                            <div class="mb-3">
                                <label class="form-label">Fullname <span style="color: red">*</span></label>
                                <input type="text" name="fullname" class="form-control"
                                    value="{{ $secretary->fullname }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control"
                                    value="{{ $secretary->phone_number }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gender <span style="color: red">*</span></label>
                                <select name="gender" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="male" {{ $secretary->gender == 'male' ? 'selected' : '' }}>Male
                                    </option>
                                    <option value="female" {{ $secretary->gender == 'female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- RIGHT: ACCOUNT INFORMATION -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Account Information</h6>

                            <div class="mb-3">
                                <label class="form-label">Email <span style="color: red">*</span></label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ $secretary->email }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Leave blank to keep current password">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div>
                                    @if ($secretary->status)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-secondary">Not Verified</span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-sm">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>
