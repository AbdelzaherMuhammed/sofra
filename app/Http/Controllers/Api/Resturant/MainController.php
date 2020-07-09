<?php

namespace App\Http\Controllers\Api\Resturant;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;

use App\Models\Product;
use App\Models\Resturant;
use App\Models\Review;
use App\Models\Client;
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
            return responseJson(1, 'تم تلقي رسالتك بنجاح ', $contact);
        } else {
            return responseJson(0, 'حدث خطأ برجاء المحاوله مره اخري');
        }
    }

    public function categories(Request $request)
    {
//        $categories =  $categories = Category::where('resturant_id' , $request->user()->id);
        $categories = $request->user()->categories()->get();

        if (count($categories)) {
            return responseJson(1, 'success', $categories);
        } else {
            return responseJson(0, 'لا يوجد تصنيفات لهذا المطعم');
        }
    }

    public function addCategory(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'name' => 'required|unique:categories,name',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $category = $request->user()->categories()->create($request->all());

        if ($request->hasFile('image')) {
            $path = public_path();
            $destinationPath = $path . '/images/resturant/categories'; // upload path
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $name = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
            $image->move($destinationPath, $name); // uploading file to given path
            $category->image = 'images/' . $name;
            $category->save();
        }
        if ($category) {
            return responseJson(1, 'تم اضافة التصنيف بنجاح', $category);

        } else {
            return responseJson(0, 'حدث خطأ ، برجاء امحاوله مره أخري');
        }
    }

    public function editCategory(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'name' => 'unique:categories,name,' . $request->category_id,
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'category_id' => 'required|exists:categories,id',
            ]);
        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $category = Category::find($request->category_id);

        $category->update($request->all());

        if ($request->hasFile('image')) {
            $path = public_path();
            $destinationPath = $path . '/images/resturant/categories'; // upload path
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $name = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
            $image->move($destinationPath, $name); // uploading file to given path
            $category->image = 'images/' . $name;
            $category->save();
        }

        if ($category) {
            return responseJson(1, 'تم تعديل التصنيف بنجاح', $category);

        } else {
            return responseJson(0, 'حدث خطأ ، برجاء المحاوله مره اخري');
        }
    }

    public function deleteCategory(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'category_id' => 'required|exists:categories,id',
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $category = Category::find($request->category_id);
        $category->delete();
        if ($category) {
            return responseJson(1, 'تم الحذف بنجاح');
        } else {
            return responseJson(0, 'حدث خطأ ، برجاء المحاوله مره أخري');
        }
    }


    public function products(Request $request)
    {
//      $products = Product::where('resturant_id' , $request->user()->id);
        $products = $request->user()->products()->get();


        if (count($products)) {
            return responseJson(1, 'success', $products);
        } else {
            return responseJson(0, 'لا يوجد منتجات خاصه بهذا المطعم');
        }
    }


    public function addProduct(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'name' => 'required|unique:products,name',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|max:100',
                'price' => 'required|numeric',
                'price_in_offer' => 'numeric',
                'category_id' => 'required|exists:categories,id',
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $product = $request->user()->products()->create($request->all());

        if ($request->hasFile('image')) {
            $path = public_path();
            $destinationPath = $path . '/images/resturant/products'; // upload path
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $name = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
            $image->move($destinationPath, $name); // uploading file to given path
            $product->image = 'images/' . $name;
            $product->save();
        }

        if ($product) {
            return responseJson(1, 'تم اضافة المنتج بنجاح', $product);
        } else {
            return responseJson(0, 'حدث خطأ ، برجاء المحاوله مره أخري');
        }

    }

    public function editProduct(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'name' => 'unique:products,name,' . $request->product_id,
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'max:100',
                'price' => '|numeric',
                'price_in_offer' => 'numeric',
                'category_id' => 'exists:categories,id',
                'product_id' => 'required|exists:products,id',
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $product = Product::find($request->product_id);

        $product->update($request->all());

        if ($request->hasFile('image')) {
            $path = public_path();
            $destinationPath = $path . '/images/resturant/products'; // upload path
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $name = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
            $image->move($destinationPath, $name); // uploading file to given path
            $product->image = 'images/' . $name;
            $product->save();
        }


//        if ($update) {
//            return responseJson(1, 'تم التعديل بنجاح' , $product);
//        } else {
//            return responseJson(1 , 'success' , $product);
//        }
        if ($product) {
            return responseJson(1, 'تم التعديل بنجاح', $product);
        } else {
            return responseJson(0, 'حدث خطأ برجاء المحاوله مره أخري');
        }

    }

    public function deleteProduct(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'product_id' => 'required|exists:products,id',
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $product = Product::find($request->product_id);
        $product->delete();

        if ($product) {
            return responseJson(1, 'تم الحذف بنجاح');
        } else {
            return responseJson(0, 'حدث خطأ ، برجاء المحاوله مره أخري');
        }
    }


    public function offers(Request $request)
    {
//      $offers =  Offer::where('resturant_id' , $request->user()->id);
        $offer = $request->user()->offers()->get();

        if (count($offer)) {
            return responseJson(1, 'success', $offer);
        } else {
            return responseJson(0, 'عفوا ، لا يوجد عروض  لهذا المطعم');
        }

    }


    public function addOffer(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'offer_title' => 'required|unique:products,name',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'offer_description' => 'required|max:100',
                'offer_start_date' => 'required|date',
                'offer_expire_date' => 'required|date',
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $offer = $request->user()->offers()->create($request->all());

        if ($request->hasFile('image')) {
            $path = public_path();
            $destinationPath = $path . '/images/resturant/offers'; // upload path
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $name = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
            $image->move($destinationPath, $name); // uploading file to given path
            $offer->image = 'images/' . $name;
            $offer->save();
        }

        if ($offer) {
            return responseJson(1, 'تم اضافة العرض بنجاح', $offer);
        } else {
            return responseJson(0, 'حدث خطأ برجاء المحازوله مره أخري');
        }

    }


    public function editOffer(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'offer_title' => 'unique:products,name,' . $request->offer_id,
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'offer_description' => 'max:100',
                'offer_start_date' => 'date',
                'offer_expire_date' => 'date',
                'offer_id' => 'required|exists:offers,id',
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $offer = Offer::find($request->offer_id);

        $offer->update($request->all());

        if ($request->hasFile('image')) {
            $path = public_path();
            $destinationPath = $path . '/images/resturant/offers'; // upload path
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $name = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
            $image->move($destinationPath, $name); // uploading file to given path
            $offer->image = 'images/' . $name;
            $offer->save();
        }

        if ($offer) {
            return responseJson(1, 'تم تعديل العرض بنجاح', $offer);
        } else {
            return responseJson(0, 'حدث خطأ ،برجاء المحاوله مره أخري');
        }

    }

    public function deleteOffer(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'offer_id' => 'required|exists:offers,id',
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $offer = Offer::find($request->offer_id);

        $offer->delete();

        if ($offer) {
            return responseJson(1, 'تم حذف العرض بنجاح');
        } else {
            return responseJson(0, 'حدث خطأ برجاء المحاوله مرة أخري');
        }
    }

    public function reviews(Request $request)
    {
//        $review = Review::where('resturant_id' , $request->resturant_id)->with('client')->get();
////        $review = Review::all();
//        return responseJson(1,'success' , $review);

        $reviews = $request->user()->reviews()->with('client')->get();

        if (count($reviews)) {
            return responseJson(1, 'success', $reviews);
        } else {
            return responseJson(0, 'لا يوجد تعليقات عن المطعم');
        }
    }

    public function newOrder(Request $request)
    {
        $orders = $request->user()->orders()->where('state', 'pending')->with('client')->get();


        if (count($orders)) {
            return responseJson(1, 'success', $orders);
        } else {
            return responseJson(0, 'لا يوجد طلبات');
        }
    }

    public function currentOrder(Request $request)
    {
        $orders = $request->user()->orders()->where('state', 'accepted')->with('resturant')->get();

        if (count($orders)) {
            return responseJson(1, 'success', $orders);
        } else {
            return responseJson(0, 'لا يوجد طلبات');
        }
    }

    public function lastOrder(Request $request)
    {
        $orders = $request->user()->orders()->where('state', 'delivered')
            ->orWhere('state', 'rejected')
            ->orWhere('state', 'declined')
            ->with('resturant')->get();

        if (count($orders)) {
            return responseJson(1, 'success', $orders);
        } else {
            return responseJson(0, 'لا يوجد طلبات');
        }
    }

    public function acceptOrder(Request $request)
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
            $client = Client::find($order->client_id);
//                dd($client);

            $order->update([
                'state' => 'accepted',
                'reason_of_rejection'=>''
            ]);

            $notification = $client->notifications()->create([
                'title' => 'تمت الموافقه علي طلبك',
                'content' => $order->resturant->name . ' تمت الموافقه علي طلبك من المطعم ',
                'order_id' => $order->id
            ]);

//            dd($notification);

            $token = $client->tokens()->where('token', '!=', '')->pluck('token')->toArray();

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


    public function rejectOrder(Request $request)
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

        if ($order) {
            $client = Client::find($order->client_id);
//                dd($client);
            $order->update([
                'state' => 'rejected',
                'reason_of_rejection'=> ' المطعم ليس لديه طلبك حاليا'
            ]);

            $notification = $client->notifications()->create([
                'title' => 'تم رفض طلبك',
                'content' => $order->resturant->name . ' تمت رفض طلبك من المطعم ',
                'order_id' => $order->id
            ]);

//            dd($notification);

            $token = $client->tokens()->where('token', '!=', '')->pluck('token')->toArray();

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


    public function commission(Request $request)
    {
        $commission = Setting::get(['commission']);

        $commission_text =  Setting::get(['commission_text']);

        $moneyPaid = Payment::get(['money_paid']);

        $dayOfPayments = Payment::get(['day_of_payment']);

        $notes = Payment::get(['notes']);

        $resturantSales = Payment::get(['resturant_sales']);
//        return $sum;

        $collection = array_collapse(['resturant_sales' => $resturantSales , 'commission'=>$commission , 'commission_text' =>$commission_text ,
            'money_paid' =>$moneyPaid , 'day_of_payment' => $dayOfPayments , 'notes' => $notes
            ]);

        return responseJson(1,'success', $collection);
    }

}
