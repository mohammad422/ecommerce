<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function PendingOrder()
    {
        $orders = Order::where('status', 'pending')->orderBy('id', 'DESC')->get();
        return view('backend.orders.pending_orders', compact('orders'));
    }

    public function AdminOrderDetails($order_id)
    {
        $order = Order::with('division', 'district', 'state', 'user')->where('id', $order_id)->first();
        $orderItem = OrderItem::with('product')->where('order_id', $order_id)->orderBy('id', 'DESC')->get();

        return view('backend.orders.admin_order_details', compact('order','orderItem'));
    }

    public function AdminConfirmedOrder()
    {
        $orders = Order::where('status', 'confirm')->orderBy('id', 'DESC')->get();
        return view('backend.orders.confirmed_orders', compact('orders'));
    }
    public function AdminProcessingOrder()
    {
        $orders = Order::where('status', 'processing')->orderBy('id', 'DESC')->get();
        return view('backend.orders.processing_orders', compact('orders'));
    }
    public function AdminDeliveredOrder()
    {
        $orders = Order::where('status', 'delivered')->orderBy('id', 'DESC')->get();
        return view('backend.orders.delivered_orders', compact('orders'));
    }

    public function PendingToConfirm($order_id)
    {
        Order::findOrFail($order_id)->update(['status'=>'confirm']);

        $notification = [
            'message' => 'Order Confirm Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.confirmed.order')->with($notification);
    }

    public function ConfirmToProcessing($order_id)
    {
        Order::findOrFail($order_id)->update(['status'=>'processing']);

        $notification = [
            'message' => 'Order Processing Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.processing.order')->with($notification);
    }

    public function ProcessingToDelivered($order_id)
    {
        $products = OrderItem::where('order_id',$order_id)->get();
        foreach ($products as $item){
            Product::where('id',$item->product_id)
                ->update(['product_qty'=> DB::raw('product_qty-'.$item->qty)]);
        }
        Order::findOrFail($order_id)->update(['status'=>'delivered']);

        $notification = [
            'message' => 'Order Delivered Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.delivered.order')->with($notification);
    }

    public function AdminInvoiceDownload($order_id)
    {
        $order = Order::with('division', 'district', 'state', 'user')->where('id', $order_id)->first();
        $orderItem = OrderItem::with('product')->where('order_id', $order_id)->orderBy('id', 'DESC')->get();

        $pdf = Pdf::loadView('backend.orders.admin_order_invoice', compact('order','orderItem'))
            ->setPaper('a4','landscape')->setOption([
                'tempDir'=>public_path(),
                'chroot'=> public_path()
            ]);
        return $pdf->download('invoice.pdf');
    }
}
