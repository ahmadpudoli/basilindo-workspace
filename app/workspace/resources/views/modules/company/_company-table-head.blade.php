<thead>
    <tr>
        @foreach(['name' => 'Nama', 'code' => 'Kode', 'entity_type' => 'Jenis', 'classification' => 'Klasifikasi', 'status' => 'Status'] as $column => $label)
            @php($nextDirection = $sort === $column && $direction === 'asc' ? 'desc' : 'asc')
            <th scope="col"><a class="workspace-sort-link {{ $sort === $column ? 'active' : '' }}" data-company-sort href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null, 'partial' => null]) }}">{{ $label }} <span aria-hidden="true">{{ $sort === $column ? ($direction === 'asc' ? '↑' : '↓') : '↕' }}</span></a></th>
        @endforeach
        <th scope="col"><span class="visually-hidden">Aksi</span></th>
    </tr>
</thead>
