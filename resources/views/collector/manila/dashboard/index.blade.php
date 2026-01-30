@foreach ($areas as $area)
    <div class="card mb-2">
        <div class="card-body">
            <h5>{{ $area->location_name }}</h5>
            <p>Area Code: {{ $area->areas_name }}</p>
        </div>
    </div>
@endforeach

<form action="{{ route('auth.logout.request') }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="dropdown-item d-flex gap-2 align-items-center">
        Logout
    </button>
</form>
