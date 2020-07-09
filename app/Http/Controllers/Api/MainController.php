<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Resturant;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{

    public function resturants(Request $request)
    {
        $resturant = Resturant::where(function ($query) use ($request){
            if ($request->has('name'))
            {
                $query->where('name' , $request->name);
            }
            if ($request->input('city_id'))
            {
                $query->whereHas('neighborhood',function ($query) use($request)
                {
                    $query->where('city_id' , $request->city_id);
                });
            }
        })->get();
        return responseJson(1 , 'success' , $resturant);
    }

    public function products(Request $request)
    {
        $products = Product::where(function ($query) use($request){
            if($request->has('category_id')){
                $query->where('category_id' , $request->category_id);
            }
        })->get();
        return responseJson(1 , 'success' , $products);
    }

    public function offers(Request $request)
    {
        $offers = Offer::where(function ($query) use ($request){
            //details of offer
            if ($request->has('offer_id'))
            {
                $query->where('id' , $request->offer_id);
            }
        })->get();
        return responseJson(1 , 'success' , $offers);
    }

    public function about()
    {
        $about = Setting::pluck('about_app');
        return responseJson(1,'success' , $about);
    }


    public function resturant(Request $request)
    {
        $information = Resturant::where('id' , $request->resturant_id)->with('neighborhood')->get();
        return responseJson(1,'success' , $information);
    }

    public function categories()
    {
        $categories = Category::all();
        return responseJson(1,'success' , $categories);
    }


    public function paymentMethods()
    {
        $paymentMethods = PaymentMethod::all();

        return responseJson(1 , 'success' , $paymentMethods);
    }

    public function createPayment(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'resturant_id' => 'required|exists:resturants,id',
                'money_paid'   => 'required|numeric',
                'day_of_payment'=>'required|date'
            ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors(), $data);
        }

        $payments = Payment::create($request->all());
        $resturant = Resturant::find($request->resturant_id);
        $sum = $resturant->orders()->sum('net');
        $payments->update(['resturant_sales' => $sum]);
        return $payments;
    }



}
