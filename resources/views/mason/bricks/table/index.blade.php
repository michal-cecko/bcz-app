@if(! empty($headers) && ! empty($rows))
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    @foreach($headers as $header)
                        <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ $header['label'] ?? '' }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row['cells'] ?? [] as $cell)
                            <td class="border border-gray-300 px-4 py-2">{{ $cell['value'] ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
