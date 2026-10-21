<div class="row g-3 mb-4">
    @foreach ($stats as $stat)
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ $stat['label'] }}</div>
                    <div class="fs-3 fw-semibold">{{ number_format($stat['value']) }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
