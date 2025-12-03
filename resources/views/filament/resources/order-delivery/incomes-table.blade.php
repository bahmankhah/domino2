<div class="w-full">
    @if($incomes->isEmpty())
        <div class="text-center py-8">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('rental.no_delivery_incomes_yet') }}</p>
        </div>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('rental.credit') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('rental.received_by') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($incomes as $income)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                                {{ number_format($income->credit) }} IRT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $income->receivedBy?->name ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('rental.total') }}:</span>
                <span class="text-lg font-bold text-green-600 dark:text-green-400">
                    {{ number_format($incomes->sum('credit')) }} IRT
                </span>
            </div>
        </div>
    @endif
</div>
