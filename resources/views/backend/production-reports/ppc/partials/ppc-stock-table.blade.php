{{-- Partial: PPC Stock Table for one tab (CED or ZINC) --}}
{{-- Variables: $stocks (collection), $tabType (string: ced|zinc), $tabLabel (string: CED|ZINC) --}}

<div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
    <div class="p-4 border-b border-slate-200">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">{{ $tabLabel }} Verified Stock List</h3>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">Total Items:</span>
                <span class="text-xs font-semibold text-slate-900">{{ collect($stocks)->count() }}</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="border-b border-slate-200" style="background: linear-gradient(to right, #141d30, #2d3a52);">
                <tr>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">#</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item Code</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Item Name</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Size</th>
                    <th class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Total Received</th>
                    <th class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">In Stock</th>
                    <th class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Assigned to SF3</th>
                    <th class="px-3 py-2 text-center text-[10px] font-semibold text-white uppercase tracking-wider whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($stocks as $index => $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-2.5 text-slate-700">{{ $index + 1 }}</td>
                    <td class="px-3 py-2.5">
                        <span class="font-medium text-slate-900">{{ !empty($item->code_sf2) && !empty($item->name_sf2) ? $item->code_sf2 : $item->code }}</span>
                    </td>
                    <td class="px-3 py-2.5">
                        <div class="flex items-center gap-2">
                            <i data-lucide="box" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="font-medium text-slate-900">{{ !empty($item->code_sf2) && !empty($item->name_sf2) ? $item->name_sf2 : $item->name }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-2.5">
                        <span class="text-slate-600">{{ $item->size }}</span>
                    </td>
                    <td class="px-3 py-2.5 text-center">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700">
                            <i data-lucide="package" class="w-3 h-3"></i>
                            <span class="font-semibold">{{ number_format($item->total_accepted, 0) }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 text-center">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700">
                            <i data-lucide="hourglass" class="w-3 h-3"></i>
                            <span class="font-semibold">{{ number_format($item->pending_quantity, 0) }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 text-center">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700">
                            <i data-lucide="check-check" class="w-3 h-3"></i>
                            <span class="font-semibold">{{ number_format($item->total_transferred, 0) }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-2.5">
                        <div class="flex items-center justify-center gap-2">
                            <button
                                type="button"
                                onclick="openTransferModal(this)"
                                data-item-id="{{ $item->id }}"
                                data-item-name="{{ !empty($item->code_sf2) && !empty($item->name_sf2) ? $item->name_sf2 : $item->name }}"
                                data-type="{{ $tabType }}"
                                data-available-stock="{{ (int) $item->pending_quantity }}"
                                class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-white rounded-lg hover:opacity-90 transition-all"
                                style="background: linear-gradient(to right, #141d30, #2d3a52);"
                            >
                                <i data-lucide="arrow-right-left" class="w-3 h-3"></i>
                                Transfer to SF3
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                <i data-lucide="package-x" class="w-8 h-8 text-slate-400"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-900">No items found</p>
                                <p class="text-xs text-slate-500 mt-1">No verified stock exists yet</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(collect($stocks)->count() > 0)
    <div class="p-4 border-t border-slate-200 bg-slate-50">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="text-xs text-slate-600">
                <i data-lucide="info" class="w-3.5 h-3.5 inline-block mr-1"></i>
                Stock quantities are calculated from verified SF2 transfers.
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <div class="text-xs">
                    <span class="text-slate-600">Total Received:</span>
                    <span class="ml-2 font-semibold text-slate-900">{{ number_format(collect($stocks)->sum('total_accepted'), 0) }}</span>
                </div>
                <div class="text-xs">
                    <span class="text-slate-600">In Stock:</span>
                    <span class="ml-2 font-semibold text-amber-700">{{ number_format(collect($stocks)->sum('pending_quantity'), 0) }}</span>
                </div>
                <div class="text-xs">
                    <span class="text-slate-600">Assigned:</span>
                    <span class="ml-2 font-semibold text-blue-700">{{ number_format(collect($stocks)->sum('total_transferred'), 0) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
