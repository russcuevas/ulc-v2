@foreach ($secretaries as $secretary)
    <div class="modal fade" id="areasModal{{ $secretary->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ $secretary->fullname }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @php
                        // Get all areas for this secretary
                        $secretaryAreas = $areas->where('secretary_id', $secretary->id);
                        // Get the first location name (or null)
                        $locationName = $secretaryAreas->first()?->location_name;
                    @endphp

                    @if ($secretaryAreas->isEmpty())
                        <p class="text-center text-muted">No areas assigned</p>
                    @else
                        <p><strong style="text-transform: uppercase; font-size: 25px; text-align: center !important">
                                {{ $locationName }}</strong></p>

                        {{-- Display all area names under that location --}}
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Area Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($secretaryAreas as $area)
                                    <tr>
                                        <td>{{ $area->areas_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endforeach
