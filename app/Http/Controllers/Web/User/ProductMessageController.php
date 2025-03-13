<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductMessageController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->messages()->create([
            'message' => $validated['message'],
            'product_id' => $product->id,
        ]);

        return redirect()->route('products.show', ['product' => $product]);
    }

    public function edit(Product $product, Message $message): View
    {
        Gate::authorize('update', $message);

        return view('messages.edit', [
            'message' => $message,
            'productId' => $product,
        ]);
    }

    public function update(Request $request, Product $product, Message $message): RedirectResponse
    {
        Gate::authorize('update', $message);
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:255'],
        ]);
        $message->update($validated);

        return redirect()->route('products.show', ['product' => $product]);
    }

    public function destroy(Product $product, Message $message): RedirectResponse
    {
        $this->authorize('delete', $message);
        $message->delete();

        return redirect()->route('products.show', ['product' => $product])
            ->with('success', 'Review deleted successfully.');
    }

    public function reply(Request $request, Product $product, Message $message): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:255'],
        ]);

        // 建立回覆留言，並關聯到原始留言
        $request->user()->messages()->create([
            'message' => $validated['message'],
            'product_id' => $product->id,
            'reply_to_id' => $message->id, // 設定回覆的留言 ID
        ]);

        return redirect()->route('products.show', ['product' => $product]);
    }
}
