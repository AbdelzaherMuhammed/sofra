<?php

namespace App\Http\Controllers\Api\Client;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use App\Models\Resturant;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function contact(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'name' => 'required',
                'phone' => 'required|min:11',
                'email' => 'required|email',
                'message' => 'required',
                'subject' => 'required'
            ]);
        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $contact = $request->user()->contacts()->create($request->all());
        if ($contact) {
            return responseJson(1, 'success', $contact);

        }
        return responseJson(0, 'failed');
    }

    public function addReview(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'comment' => 'required|max:200',
                'review' => 'required|in:1,2,3,4,5',
                'resturant_id' => 'required|exists:resturants,id',
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $review = $request->user()->reviews()->create($request->all());


        if ($review) {
            return responseJson(1, 'تم اضافة التعليق بنجاح', $review);
        } else {
            return responseJson(0, 'حدث خطأ ، برجاء المحاوله مره أخري');
        }
    }


    public function CreateOrder(Request $request)
    {
//        dd($request->all());
        $validator = validator()->make($request->all(),
            [
                'resturant_id' => 'required|exists:resturants,id',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required',
                'address' => 'required',
                'payment_method_id' => 'required|exists:payment_methods,id'
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $resturant = Resturant::find($request->resturant_id);

//        dd($resturant);
        //resturant closed
        if ($resturant->status == 'closed') {
            return responseJson(0, 'عفوا المطعم غير متاح في الوقت الحالي');
        }


        $order = $request->user()->orders()->create([
            'resturant_id' => $request->resturant_id,
            'note' => $request->note,
            'address' => $request->address,
            'state' => 'pending',
            'payment_method_id' => $request->payment_method_id
        ]);
//        dd($order);

        $cost = 0;
        $deliveryCost = $resturant->delivery_fees;

        foreach ($request->products as $p) {
            //$p = ['product_id' => 1 , 'quantity' => 2 , 'note' => 'no tomato'];
            $product = Product::find($p['product_id']);
            // product validation // no logic

            $readyProduct = [
                $p['product_id'] => [
                    'quantity' => $p['quantity'],
                    'price' => $product->price,
                    'note' => (isset($p['note'])) ? $p['note'] : ''
                ]
            ];

            $order->products()->attach($readyProduct);


            $cost += ($product->price * $p['quantity']);

            //minimum charge

            if ($cost >= $resturant->minimum_charge) {
                $settings = Setting::find(1);
                $total = $cost + $deliveryCost;
                $commission = $settings->commission * $cost; // commission = 0.1

                $net = $total - $commission;

                $update = $order->update([
                    'cost' => $cost,
                    'delivery_cost' => $deliveryCost,
                    'total' => $total,
                    'commission' => $commission,
                    'net' => $net
                ]);

                //create notification for resturant
               $notification= $resturant->notifications()->create([
                    'title'     =>'لديك طلب جديد',
                    'content'   => $request->user()->name.' لديك طلب جديد من العميل',
                    'order_id'  => $order->id
                ]);


                //find token of a resturant to push notifications via FCM
                $token = $resturant->tokens()->where('token' , '!=', '')->pluck('token')->toArray();

                if (count($token)) {
                    $title = $notification->title;
                    $body = $notification->content;
                    $data = [
                        'order_id' => $order->id
                    ];

                    $send = notifyByFirebase($title, $body, $token, $data);
                    info("firebase result :" . $send);
//                    dd($send);
                }

                $data = [
                    'order' => $order->fresh()->load('products')
                ];

                return responseJson(1, 'تم تأكيد طلبك بنجاح', $data);

            } else {
                $order->products()->delete();
                $order->delete();
                return responseJson(0, 'الطلب لا يجب ان يكون اقل من' . $resturant->minimum_charge . 'جنيه');
            }
        }
    }

    public function orderDetails(Request $request)
    {

        $validator = validator()->make($request->all(),
            [
                'order_id' => 'required|exists:orders,id'
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $order = $request->user()->orders()->with('resturant')->where('id', $request->order_id)->get();

        return responseJson(1, 'success', $order);
    }

    public function newOrder(Request $request)
    {
        $orders = $request->user()->orders()->where('state', 'pending')->with('resturant')->get();

        if (count($orders)) {
            return responseJson(1, 'success', $orders);
        } else {
            return responseJson(0, 'لا يوجد طلبات');
        }
    }

    public function currentOrder(Request $request)
    {
        $orders = $request->user()->orders()->where('state' , 'accepted')->with('resturant')->get();

        if (count($orders)) {
            return responseJson(1, 'success', $orders);
        } else {
            return responseJson(0, 'لا يوجد طلبات');
        }
    }

    public function lastOrder(Request $request)
    {
        $orders = $request->user()->orders()->where('state', 'delivered')
            ->orWhere('state' , 'rejected')
            ->orWhere('state' , 'declined')
            ->with('resturant')->get();

        if (count($orders)) {
            return responseJson(1, 'success', $orders);
        } else {
            return responseJson(0, 'لا يوجد طلبات');
        }
    }

    public function deliverOrder(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'order_id' => 'required|exists:orders,id'
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $order = $request->user()->orders()->where('id', $request->order_id)->where('state', 'pending')->first();
//        dd($order);

        if ($order) {
            $resturant = Resturant::find($order->client_id);
//                dd($resturant);

            $order->update([
                'state' => 'delivered',
                'reason_of_rejection'=>''
            ]);

            $notification = $resturant->notifications()->create([
                'title' => 'تم توصيل الطلب الي العميل بنجاح',
                'content' => $order->client->name.'تم توصيل الطلب بنجاح الي العميل ',
                'order_id' => $order->id
            ]);

//            dd($notification);

            $token = $resturant->tokens()->where('token', '!=', '')->pluck('token')->toArray();

            if (count($token)) {
                $title = $notification->title;
                $body = $notification->content;
                $data = [
                    'order_id' => $order->id
                ];

                $send = notifyByFirebase($title, $body, $token, $data);
                info("firebase result :" . $send);
//                    dd($send);
            }

            return responseJson(1, 'تم التعديل بنجاح', $order);

        } else {
            return responseJson(0, 'عفوا حالة الطلب ليست معلقه');
        }
    }

    public function declineOrder(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'order_id' => 'required|exists:orders,id'
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $order = $request->user()->orders()->where('id', $request->order_id)->where('state', 'pending')->first();
//        dd($order);

        if ($order) {
            $resturant = Resturant::find($order->client_id);
//                dd($resturant);

            $order->update([
                'state' => 'declined',
                'reason_of_rejection'=>''
            ]);

            $notification = $resturant->notifications()->create([
                'title' => 'تم توصيل الطلب الي العميل بنجاح',
                'content' => $order->client->name.'تم توصيل الطلب بنجاح الي العميل ',
                'order_id' => $order->id
            ]);

//            dd($notification);

            $token = $resturant->tokens()->where('token', '!=', '')->pluck('token')->toArray();

            if (count($token)) {
                $title = $notification->title;
                $body = $notification->content;
                $data = [
                    'order_id' => $order->id
                ];

                $send = notifyByFirebase($title, $body, $token, $data);
                info("firebase result :" . $send);
//                    dd($send);
            }

            return responseJson(1, 'تم التعديل بنجاح', $order);

        } else {
            return responseJson(0, 'عفوا حالة الطلب ليست معلقه');
        }
    }

    public function notificationList(Request $request)
    {
        $notifications = $request->user()->notifications()->get();
        if (count($notifications))
        {
            return responseJson(1 , 'success' , $notifications);
        }else{
            return responseJson(0 ,' لا يوجد اي اشعارات خاصه بك حاليا');
        }
    }

    public function notificationUpdate(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'notification_id' => 'required|exists:notifications,id'
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $notification = $request->user()->notifications()->find($request->notification_id);

        if (!isset($notification))
        {
            return responseJson(0,'حدث خطأ ما');
        }

        $notification->is_read = 1;
        $notification->save();

        return responseJson(1,'تم التحديث ');
    }


}
