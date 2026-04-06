<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Addresse;
use App\Models\Order;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    use ApiResponse;

    // =========================
    // Buyer Dashboard
    // =========================
    public function buyerOrders()
    {
        $orders = Order::where('buyer_id', auth()->id())
            ->with(['product', 'seller', 'addresse'])
            ->latest()
            ->paginate(10);
        //->get();

        return $this->success(
            OrderResource::collection($orders),
            'Buyer orders retrieved successfully'
        );
    }

    // =========================
    // Seller Dashboard
    // =========================
    public function sellerOrders()
    {
        $orders = Order::where('seller_id', auth()->id())
            ->with(['product', 'buyer', 'addresse'])
            ->latest()
            ->paginate(10);
        //->get();

        return $this->success(
            OrderResource::collection($orders),
            'Seller orders retrieved successfully'
        );
    }

    // =========================
    // Create Order
    // =========================
    public function store(StoreOrderRequest $request)
    {
        $product = Product::select('id', 'seller_id', 'price', 'status')
            ->findOrFail($request->product_id);

        $this->authorize('create', [Order::class, $product]);

        // 🔐 تحقق من العنوان
        $validAddress = Addresse::where('id', $request->addresse_id)
            ->where('buyer_id', auth()->id())
            ->exists();

        if (!$validAddress) {
            return $this->error('Invalid address');
        }

        // ❌ منع التكرار
        $exists = Order::where([
            'product_id' => $product->id,
            'buyer_id' => auth()->id()
        ])
            ->whereIn('status', ['pending', 'accepted', 'shipped'])
            ->exists();

        if ($exists) {
            return $this->error('You already requested this product');
        }

        $order = Order::create([
            'product_id' => $product->id,
            'buyer_id' => auth()->id(),
            'seller_id' => $product->seller_id,
            'addresse_id' => $request->addresse_id,
            'price' => $product->price,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);

        return $this->success(
            new OrderResource(
                $order->load(['product', 'buyer', 'seller', 'addresse'])
            ),
            'Order created successfully',
            201
        );
    }

    // =========================
    // Show Order
    // =========================
    public function show($id)
    {
        $order = Order::with(['product', 'buyer', 'seller', 'addresse'])->findOrFail($id);

        $this->authorize('view', $order);

        return $this->success(new OrderResource($order));
    }

    // =========================
    // Update Order addresse
    // =========================
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('update', $order);

        $request->validate([
            'addresse_id' => 'required|exists:addresses,id',
        ]);

        // 🔐 تحقق من العنوان
        $validAddress = Addresse::where('id', $request->addresse_id)
            ->where('buyer_id', auth()->id())
            ->exists();

        if (!$validAddress) {
            return $this->error('Invalid address');
        }

        $order->update([
            'addresse_id' => $request->addresse_id,
        ]);

        return $this->success(
            new OrderResource($order->load('addresse')),
            'Order address updated successfully'
        );
    }

    // =========================
    // Delete Order
    // =========================
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('delete', $order);

        $order->delete();

        return $this->success(null, 'Order deleted successfully');
    }

    // =========================
    // Accept Order
    // =========================
    public function accept($id)
    {
        return DB::transaction(function () use ($id) {

            $order = Order::lockForUpdate()->findOrFail($id);
            $product = Product::lockForUpdate()->findOrFail($order->product_id);

            $this->authorize('accept', $order);

            if ($product->status !== 'available') {
                return $this->error('Already reserved or sold');
            }

            $order->update(['status' => 'accepted']);
            $product->update(['status' => 'reserved']);

            return $this->success(
                new OrderResource($order->load(['product', 'buyer', 'seller'])),
                'Order accepted successfully'
            );
        });
    }

    // =========================
    // Ship Order
    // =========================
    public function ship($id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('ship', $order);

        $order->update(['status' => 'shipped']);

        return $this->success(
            new OrderResource($order->load(['product', 'buyer', 'seller'])),
            'Order shipped successfully'
        );
    }

    // =========================
    // Deliver Order
    // =========================
    public function deliver($id)
    {
        $order = Order::findOrFail($id);
        $product = Product::findOrFail($order->product_id);

        $this->authorize('deliver', $order);

        $order->update(['status' => 'delivered']);
        $order->product->update(['status' => 'sold']);

        // إلغاء باقي الطلبات
        Order::where('product_id', $product->id)
            ->where('id', '!=', $order->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected'

            ]);

        return $this->success(
            new OrderResource(
                $order->load(['product', 'buyer', 'seller'])
            ),
            'Order delivered successfully'
        );
    }

    // =========================
    // Cancel Order
    // =========================
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('cancel', $order);

        $order->update(['status' => 'cancelled']);
        $order->product->update(['status' => 'available']);

        return $this->success(
            new OrderResource(
                $order->load(['product', 'buyer'])
            ),
            'Order cancelled successfully'
        );
    }

    // =========================
    // Reject Order
    // =========================
    public function reject($id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('reject', $order);

        //$order->update(['status' => 'rejected']);
        //$order->product->update(['status' => 'available']);

        if ($order->status === 'accepted') {
            $order->update(['status' => 'pending']);
            $order->product->update(['status' => 'available']);
        } else {
            $order->update(['status' => 'rejected']);
        }

        return $this->success(
            new OrderResource(
                $order->load(['product', 'buyer'])
            ),
            'Order rejected'
        );
    }

    // =========================
    // Failed Delivery Order
    // =========================
    public function failedDelivery($id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('failDelivery', $order);

        $order->update(['status' => 'failed_delivery']);
        $order->product->update(['status' => 'available']);

        return $this->success(
            new OrderResource(
                $order->load(['product', 'buyer'])
            ),
            'Delivery failed'
        );
    }
}
