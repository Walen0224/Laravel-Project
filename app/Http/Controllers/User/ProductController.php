<?php

namespace App\Http\Controllers\User;

use App\Enums\ProductStatus;
use App\Enums\Tagtype;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        $userProducts = QueryBuilder::for(Product::class)
            ->where('user_id', $userId)
            ->allowedFilters([
                'name',
            ])
            ->with(['media', 'user', 'tags'])
            ->orderBy('updated_at', 'desc')
            ->paginate(3)
            ->withQueryString();

        return view('user.products.index', compact('userProducts'));
    }

    public function create()
    {
        $tags = Tag::whereIn('type', [Tagtype::Grade, Tagtype::Semester, Tagtype::Subject, Tagtype::Category])->get();

        return view('user.products.create', ['tags' => $tags]);
    }

    public function store(Request $request)
    {
        // 基本驗證規則
        $rules = [
            'name' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999'],
            'description' => ['required', 'string', 'max:50'],
            'grade' => ['required', Rule::exists('tags', 'id')->where('type', Tagtype::Grade)],
            'semester' => ['required', Rule::exists('tags', 'id')->where('type', Tagtype::Semester)],
            'subject' => ['required', Rule::exists('tags', 'id')->where('type', Tagtype::Subject)],
            'category' => ['required', Rule::exists('tags', 'id')->where('type', Tagtype::Category)],
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg,gif',
                'max:2048',
                'dimensions:max_width=3200,max_height=3200',
            ],
        ];

        // 驗證
        $validated = $request->validate($rules, trans('product'));

        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'user_id' => auth()->id(),
        ]);

        // 處理圖片上傳
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($index >= 5) {
                    break;
                }

                $compressedImage = (new \App\Services\CompressedImage)->uploadCompressedImage($image);

                Storage::put($compressedImagePath = 'images/compressed_'.uniqid().'.jpg', $compressedImage->toJpeg(80));

                $product->addMedia(Storage::path($compressedImagePath))->toMediaCollection('images');
            }
        }

        // 獲取並附加新的標籤
        $tagIds = [
            $request->input('grade'),
            $request->input('semester'),
            $request->input('subject'),
            $request->input('category'),
        ];

        // 同步標籤到產品
        $product->tags()->sync($tagIds);

        return redirect()->route('user.products.create')->with('success', '產品已成功創建！');
    }

    public function edit(Request $request, Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            return redirect()->route('user.products.index')->with('error', '您無權編輯此商品。');
        }

        $productTags = $product->tags;
        $gradeTag = $productTags->where('type', Tagtype::Grade)->first();
        $semesterTag = $productTags->where('type', Tagtype::Semester)->first();
        $subjectTag = $productTags->where('type', Tagtype::Subject)->first();
        $categoryTag = $productTags->where('type', Tagtype::Category)->first();
        $tags = Tag::whereNull('deleted_at')->get();

        return view('user.products.edit', compact('product', 'tags', 'gradeTag', 'semesterTag', 'categoryTag', 'subjectTag'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            return redirect()->route('user.products.index')->with('error', '您無權編輯此商品。');
        }

        // 基本驗證規則
        $rules = [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:50'],
            'grade' => ['required', Rule::exists('tags', 'id')->where('type', Tagtype::Grade)],
            'semester' => ['required', Rule::exists('tags', 'id')->where('type', Tagtype::Semester)],
            'subject' => ['required', Rule::exists('tags', 'id')->where('type', Tagtype::Subject)],
            'category' => ['required', Rule::exists('tags', 'id')->where('type', Tagtype::Category)],
            'images' => ['nullable', 'array', 'min:1', 'max:5'],
            'images.*' => [
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,gif',
                'max:2048',
                'dimensions:max_width=3200,max_height=3200',
            ],
            'image_ids' => ['nullable', 'array', 'max:5'],
            'deleted_image_ids' => ['nullable', 'string'],
        ];

        // 獲取要刪除的圖片 ID
        $deletedImageIds = json_decode($request->input('deleted_image_ids', '[]'), true);

        // 修改檢查圖片邏輯，更精確地判斷圖片狀態
        $existingImages = $product->getMedia('images');
        $remainingImages = $existingImages->whereNotIn('id', $deletedImageIds);

        // 檢查是否有新上傳的圖片
        $newImages = collect($request->file('images', []));
        $validNewImages = $newImages->filter()->isNotEmpty();

        // 計算最終的圖片數量（保留的現有圖片 + 新上傳的有效圖片）
        $totalImagesAfterUpdate = $remainingImages->count() + ($validNewImages ? $newImages->count() : 0);

        // 如果沒有任何圖片（包括現有的和新上傳的），則添加必填驗證
        if ($totalImagesAfterUpdate === 0) {
            $rules['images'] = ['required', 'array', 'min:1'];
            $messages['images.required'] = '請至少上傳一張商品圖片';
            $messages['images.min'] = '請至少上傳一張商品圖片';
        }

        // 驗證
        $request->validate($rules, trans('product'));

        // 更新產品資料
        $product->update($request->only(['name', 'description']));

        if ($request->hasFile('images') || $request->has('image_ids')) {
            $existingMedia = $product->getMedia('images')->keyBy('id');

            foreach ($deletedImageIds as $imageId) {
                if ($existingMedia->has($imageId)) {
                    $existingMedia[$imageId]->delete();
                }
            }

            // 2. 處理圖片順序和新圖片
            foreach ($request->input('image_ids', []) as $index => $imageId) {
                // 如果圖片被標記為刪除，跳過
                if (in_array($imageId, $deletedImageIds)) {
                    continue;
                }

                if ($request->hasFile("images.$index")) {
                    // 上傳並壓縮新圖片
                    try {
                        $newImageFile = $request->file("images.$index");

                        // 壓縮圖片
                        $compressedImage = (new \App\Services\CompressedImage)->uploadCompressedImage($newImageFile);

                        // 生成壓縮圖片的路徑並存儲到文件系統
                        Storage::put(
                            $compressedImagePath = 'images/compressed_'.uniqid().'.jpg',
                            $compressedImage->toJpeg(80)
                        );

                        // 添加壓縮後的圖片到媒體集合
                        $product->addMedia(Storage::path($compressedImagePath))
                            ->withCustomProperties(['order_column' => $index + 1])
                            ->toMediaCollection('images');

                        // 刪除壓縮後的臨時文件
                        unlink($compressedImagePath);
                    } catch (\Exception $e) {
                        \Log::error('圖片壓縮或上傳失敗：'.$e->getMessage());

                        continue;
                    }
                } elseif ($imageId && $existingMedia->has($imageId)) {
                    // 更新現有圖片的順序
                    $existingMedia[$imageId]->order_column = $index + 1;
                    $existingMedia[$imageId]->save();
                }
            }
        }

        // 獲取並附加新的標籤
        $tagIds = [
            $request->input('grade'),
            $request->input('semester'),
            $request->input('subject'),
            $request->input('category'),
        ];

        // 同步標籤到產品
        $product->tags()->sync($tagIds);

        // 保存更新後的產品資料
        $product->save();

        // 重定向並返回成功消息
        return redirect()->route('user.products.index')->with('success', '商品更新成功！');
    }

    public function destroy(Product $product)
    {
        // 軟刪除產品，保留記錄但標記為已刪除
        $product->delete();

        // 重新導向到產品清單頁面，並標註成功訊息
        return redirect()->route('user.products.index')->with('success', '產品已成功刪除');
    }

    public function inactive(Product $product)
    {
        // 根據當前狀態切換到相反的狀態
        $newStatus = $product->status === ProductStatus::Active
            ? ProductStatus::Inactive
            : ProductStatus::Active;

        // 更新商品的狀態
        $product->update([
            'status' => $newStatus,
        ]);

        $message = "商品{$newStatus->name()}！";

        return redirect()->route('user.products.index')->with('success', $message);
    }

    public function ProcessImage(Request $request)
    {
        $image = $request->file('image');

        $originalFilePath = storage::disk('temp')->putFile('', $image);

        $compressedImage = (new \App\Services\CompressedImage)->uploadCompressedImage($image);

        Storage::put($compressedImagePath = 'compressed_'.uniqid().'.jpg', $compressedImage->toJpeg(80));

        Storage::disk('temp')->delete($originalFilePath);

        encrypt($compressedImagePath);

        return response()->json([
            'success' => true,
            'message' => '圖片上傳成功',
            'path' => $compressedImagePath,
        ]);
    }
}
