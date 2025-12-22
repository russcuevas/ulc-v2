<div class="modal fade" id="editCollectorModal{{ $collector->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-top modal-dialog-scrollable">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.collector.update', $collector->id) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Collector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="mb-3">
                            <label class="form-label">Fullname <span style="color: red">*</span></label>
                            <input type="text" name="fullname" class="form-control"
                                value="{{ $collector->fullname }}" required>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>
