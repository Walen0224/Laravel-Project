<x-template-admin-layout>

    <!-- 主要內容 -->
    <x-flex-container>
        <x-div.container>
            <x-h.h3>檢舉詳情</x-h.h3>

            <div class="mb-8">
                <form action="#" method="GET">
                    <x-div.flex-container>
                        <x-h.h2 id="reviews-title">Reviews</x-h.h2>
                        <div>
                            <x-input.search type="text" name="filter[name]" placeholder="搜尋商品名稱..."
                                value="">
                            </x-input.search>
                            <x-button.search>
                                搜尋
                            </x-button.search>
                        </div>
                    </x-div.flex-container>
                </form>

                <x-div.bg-white>
                    <div class="overflow-x-auto">
                        <x-table.gray-200>
                            <x-thead.reportable />
                            <x-gray-200>
                                <tr>
                                    <x-gray-900>商品名稱</x-gray-900>
                                    <x-gray-900>檢舉原因</x-gray-900>
                                    <x-gray-900>自訂義原因</x-gray-900>
                                    <x-gray-900>檢舉人</x-gray-900>
                                    <x-gray-900>檢舉日期</x-gray-900>
                                </tr>
                            </x-gray-200>
                        </x-table.gray-200>
                    </div>

                    <!-- 分頁導航 -->
                    <x-div.gray-200>
                    </x-div.gray-200>
                </x-div.bg-white>
            </div>
        </x-div.container>
    </x-flex-container>
</x-template-admin-layout>
