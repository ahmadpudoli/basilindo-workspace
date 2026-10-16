@if (count($rows) > 0)
    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800/60">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Nama field</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @foreach ($rows as $row)
                    <tr>
                        <th scope="row" class="whitespace-nowrap px-4 py-3 text-left font-medium text-gray-900 dark:text-gray-100">
                            {{ $row['label'] }}
                        </th>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            @if (is_array($row['value']))
                                {{ json_encode($row['value'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}
                            @elseif (is_bool($row['value']))
                                {{ $row['value'] ? 'Ya' : 'Tidak' }}
                            @elseif (filled($row['value']))
                                {{ $row['value'] }}
                            @else
                                <span class="text-gray-400">Belum diisi</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada metadata tambahan.</p>
@endif
