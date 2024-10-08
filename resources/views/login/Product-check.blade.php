<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/icon.png">
    <title>聯大二手書交易平台</title>
    <link rel="stylesheet" href="css/tailwind.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" integrity="sha512-7x3zila4t2qNycrtZ31HO0NnJr8kg2VI67YLoRSyi9hGhRN66FHYWr7Axa9Y1J9tGYHVBPqIjSE1ogHrJTz51g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-body">

    <!-- home section -->
    <section class="bg-white py-10 md:mb-10">

        <div class="container max-w-screen-xl mx-auto px-4">

            <nav class="flex-wrap lg:flex items-center" x-data="{navbarOpen:false}">
                <div class="flex items-center mb-10 lg:mb-0">
                    <img src="images/book-4-fix.png" alt="Logo">

                    <button class="lg:hidden w-10 h-10 ml-auto flex items-center justify-center border border-blue-500 text-blue-500 rounded-md" @click="navbarOpen = !navbarOpen">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </button>
                </div>

                <ul class="lg:flex flex-col lg:flex-row lg:items-center lg:mx-auto lg:space-x-8 xl:space-x-14" :class="{'hidden':!navbarOpen,'flex':navbarOpen}">
                    <li class="font-semibold text-gray-900 hover:text-gray-400 transition ease-in-out duration-300 mb-5 lg:mb-0 text-2xl">
                        <a href="/">首頁</a>
                    </li>
                    <li class="font-semibold text-gray-900 hover:text-gray-400 transition ease-in-out duration-300 mb-5 lg:mb-0 text-2xl">
                        <a href="/products">商品</a>
                    </li>
                    <li class="font-semibold text-gray-900 hover:text-gray-400 transition ease-in-out duration-300 mb-5 lg:mb-0 text-2xl">
                        <a href="/products-create">刊登</a>
                    </li>
                    <li class="font-semibold text-gray-900 hover:text-gray-400 transition ease-in-out duration-300 mb-5 lg:mb-0 text-2xl">
                        <a href="{{route('products.check')}}">我的商品</a>
                    </li>
                </ul>

                <div class="lg:flex flex-col md:flex-row md:items-center text-center md:space-x-6" :class="{'hidden':!navbarOpen,'flex':navbarOpen}">
                    @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-3xl leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <img width="65" height="65" src="images/account.png" alt="">
                                    <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 111.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                    @else
                    <a href="/login" class="px-6 py-4 border-2 border-blue-500 text-blue-500 font-semibold text-lg rounded-xl hover:bg-blue-700 hover:text-white transition ease-linear duration-500">登入</a>
                    @endauth
                </div>
            </nav>
            <!-- </div> -->
            <!-- </section> -->


            <div class="flex flex-col w-full min-h-screen">
                <main class="flex min-h-[calc(100vh_-_theme(spacing.16))] flex-1 flex-col gap-4 p-4 md:gap-8 md:p-10">
                    @if($message)
                        <div class="alert alert-info text-lg font-semibold text-center text-blue-500 p-4">
                            {{ $message }}
                        </div>
                    @endif    
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($userProducts as $product) 
                            <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                                <div class="space-y-1.5 p-6">
                                    <h4 class="font-semibold text-2xl mb-2">商品名稱:{{$product->name}}</h4>
                                    <div><h1 class="font-semibold">用戶名稱:{{ $product->user->name }}</h1></div>
                                    @if($product->status == 100)
                                        <div><h1 class="font-semibold">目前狀態:上架中</h1></div>
                                    @elseif($product->status == 200)   
                                        <div><h1 class="font-semibold">目前狀態:已下架</h1></div>
                                    @else
                                        <div><h1 class="font-semibold">目前狀態:未知</h1></div>
                                    @endif
                                </div>
                                <div class="p-6">
                                    <div class="text-2xl font-bold">${{$product->price}}</div>
                                    <h1 class="font-semibold">上架時間: {{$product->updated_at->format('Y/m/d')}}</h1>
                                    <p class="font-semibold text-sm mt-2">{{$product->description}}</p>
                                    <div class="mt-4">
                                        @if($product->media->isNotEmpty())
                                            @php
                                                $media = $product->getFirstMedia('images');
                                            @endphp
                                            @if($media)
                                                <img src="{{ $media->getUrl() }}" alt="這是圖片" width="1200" height="900" style="aspect-ratio: 900 / 1200; object-fit: cover;" class="w-full rounded-md object-cover" />
                                            @else
                                            <div>沒圖片</div>
                                            @endif
                                        @else
                                            <div>沒有圖片</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex justify-center space-x-4">
                                    <!-- <button class="px-3 py-2 bg-info font-semibold text-white text-lg rounded-xl hover:bg-blue-700 transition ease-in-out duration-500">
                                        上架
                                    </button> -->
                                    <form action="{{ route('products.demoteData', ['product' => $product->id])  }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="status" value="200"> 
                                        <button type="submit" class="px-3 py-2 bg-info font-semibold text-white text-lg rounded-xl hover:bg-blue-700 transition ease-in-out duration-500">
                                            下架
                                        </button>
                                    </form>
                                    <a href="{{ route('products.edit', ['product' => $product->id])  }}" class="px-3 py-2 bg-info font-semibold text-white text-lg rounded-xl hover:bg-blue-700 transition ease-in-out duration-500">
                                        編輯
                                    </a>
                                    <form action="{{ route('products.update', ['product' => $product->id])  }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('delete')
                                    <!-- <button class="px-3 py-2 bg-info font-semibold text-white text-lg rounded-xl hover:bg-blue-700 transition ease-in-out duration-500">
                                        刪除
                                    </button> -->
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        </div>

                    <div class="mt-6">
                        {{ $userProducts->links() }}
                    </div>
                </main>
            </div>
        </body>
</html>