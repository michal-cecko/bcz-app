@if(! empty($headers) && ! empty($rows))
 <div class="overflow-x-auto border border-[#222222] bg-[#111111]">
 <table class="w-full border-collapse">
 <thead>
 <tr class="bg-[#1A1A1A]">
 @foreach($headers as $header)
 <th class="px-5 py-3 text-left text-sm font-semibold text-white">{{ brick_trans($header['label'] ?? []) }}</th>
 @endforeach
 </tr>
 </thead>
 <tbody>
 @foreach($rows as $row)
 <tr class="{{ !$loop->last ? 'border-b border-[#222222]' : '' }}">
 @foreach($row['cells'] ?? [] as $cell)
 <td class="px-5 py-3 text-sm text-[#CCCCCC]">{{ brick_trans($cell['value'] ?? []) }}</td>
 @endforeach
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
@endif
