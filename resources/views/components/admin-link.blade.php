<!-- 左側邊欄 -->
<div class="w-full md:w-64 bg-white shadow-md">
    <div class="p-4 text-2xl font-bold flex items-center">
        <a href="/"> <class="flex items-center">
            <img src="{{ asset('images/book-4-fix.png') }}" alt="Logo" class="w-8 h-8 mr-2">
        </a>
        管理者後台
    </div>
    <nav class="mt-4" x-data="{ open: false }">
        <div @click="open = !open" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 cursor-pointer">
            <div class="flex justify-between items-center">
                <span>商品管理</span>
                <svg :class="{ 'transform rotate-180': open }" class="w-4 h-4 transition-transform duration-200"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        <div x-show="open" class="pl-4">
            <a href="{{ route('admin.products.index') }}"
                class="block py-2 px-4 text-gray-700 hover:bg-gray-200">商品管理</a>
            <a href="{{ route('admin.user.index') }}" class="block py-2 px-4 text-gray-700 hover:bg-gray-200">用戶管理</a>
            <a href="{{ route('admin.message.index') }}"
                class="block py-2 px-4 text-gray-700 hover:bg-gray-200">留言管理</a>
            <a href="{{ route('admin.role.index') }}" class="block py-2 px-4 text-gray-700 hover:bg-gray-200">權限管理</a>
            <a href="{{ route('admin.tags.index') }}"
                class="block py-2 px-4 text-gray-700 hover:bg-gray-200">新增標籤與刪除標籤</a>
            <a href="{{ route('admin.report.index') }}" class="block py-2 px-4 text-gray-700 hover:bg-gray-200">檢舉詳情</a>
        </div>
    </nav>
</div>