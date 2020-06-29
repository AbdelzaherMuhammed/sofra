<?php

namespace App\Http\Controllers\Api\Client;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function contact(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'name'    => 'required',
                'phone'   => 'required|min:11',
                'email'   => 'required|email',
                'message'   => 'required',
                'subject' => 'required'
            ]);
        if ($validator->fails())
        {
            $data = $validator->errors();
            return responseJson(0,$validator->errors() , $data);
        }

        $contact = $request->user()->contacts()->create($request->all());
        if ($contact)
        {
            return responseJson(1,'success' , $contact);

        }
        return responseJson(0,'failed' );
    }

    public function addReview(Request $request)
    {
        $validator = validator()->make($request->all(),
            [
                'comment'   => 'required|max:200',
                'review'   => 'required|in:1,2,3,4,5',
                'resturant_id'   => 'required',
            ]);

        if ($validator->fails())
        {
            $data = $validator->errors();
            return responseJson(0,$validator->errors() , $data);
        }

       $review = $request->user()->reviews()->create($request->all());


        if ($review) {
            return responseJson(1 , 'تم اضافة التعليق بنجاح' , $review);
        }else {
            return responseJson(0,'حدث خطأ ، برجاء المحاوله مره أخري');
        }
    }
}
