<?php

namespace App\Http\Controllers\Api\Resturant;

use App\Mail\ResturantResetPassword;
use App\Models\Resturant;
use App\Models\Token;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => 'required|unique:resturants|max:255',
            'email' => 'required|email|unique:resturants',
            'phone' => 'required|numeric|min:11|unique:resturants',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|confirmed',
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'minimum_charge' => 'required|numeric',
            'delivery_fees' => 'required|numeric',
            'status' => 'required|in:open,closed',
            'delivery_time' => 'required|numeric',

        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors()->first(), $data);
        }

        $request->merge(['password' => bcrypt($request->password)]);

        $resturant = Resturant::create($request->all());

        if ($request->hasFile('image')) {
            $path = public_path();
            $destinationPath = $path . '/images/resturant/'; // upload path
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $name = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
            $image->move($destinationPath, $name); // uploading file to given path
            $resturant->image = 'images/' . $name;
            $resturant->save();
        }

        $resturant->api_token = str_random(60);

        $resturant->save();
        if ($resturant) {
            return responseJson(1, 'تم التسجيل بنجاح', [
                'api_token' => $resturant->api_token,
                'resturant' => $resturant
            ]);
        } else {
            return responseJson(0, 'حدث خطأ ، برجاء المحاوله مره أخري');
        }

    }

    public function login(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'email' => 'required|email|exists:resturants',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors()->first(), $data);
        }

        $resturant = Resturant::where('email', $request->email)->first();

        if ($resturant) {
            if (Hash::check($request->password, $resturant->password)) {

                return responseJson(1, 'تم تسجيل الدخول بنجاح', [
                    'api_token' => $resturant->api_token,
                    'resturant' => $resturant
                ]);
            } else {
                return responseJson(0, 'حدث خطأ برجاء المحاوله مره أخري');
            }

        } else {
            return responseJson(0, 'بيانات الدخول غير صحيحه');
        }
    }


    public function resetPassword(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();

            return responseJson(0, $validator->errors()->first(), $data);
        }

        $resturant = Resturant::where('email', $request->email)->first();

        if ($resturant) {
            $code = rand(111111, 999999);

            $update = $resturant->update(['pin_code' => $code]);

            if ($update) {

                //send email
                Mail::to($resturant->email)
                    ->bcc("abdo.muhammed1122@gmail.com")
                    ->send(new ResturantResetPassword($code));

                return responseJson(1, 'برجاء فحص بريدك الالكتروني', [
                    'pin_code_for_test' => $code
                ]);

            } else {
                return responseJson(0, 'حدث خطأ برجاء المحاوله مره أخري');
            }
        } else {
            return responseJson(0, 'البريد الذي ادخلته غير صحيح');
        }

    }

    public function newPassword(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'pin_code' => 'required',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();

            return responseJson(0, $validator->errors()->first(), $data);
        }

        // finding resturant
        $resturant = Resturant::where('pin_code', $request->pin_code)->where('pin_code', '!=', 0)->first();

        if ($resturant) {
            $resturant->password = bcrypt($request->password);

            $resturant->pin_code = null;

            if ($resturant->save()) {
                return responseJson(1, 'تم تغيير كلمة المرور بنجاح');
            } else {
                return responseJson(0, 'حدث خطأ برجاء المحاوله مره أخري');
            }

        } else {
            return responseJson(0, 'هذا الكود غير صالح');
        }
    }

    public function profile(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'name' => 'unique:resturants,name,' . $request->user()->id,
            'email' => 'unique:resturants,email,' . $request->user()->id,
            'phone' => 'min:11|unique:resturants,phone,' . $request->user()->id,
            'image' => 'ؤشimage|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'confirmed',
            'neighborhood_id' => 'exists:neighborhoods,id',
            'minimum_charge' => 'numeric',
            'delivery_fees' => 'numeric',
            'status' => 'in:open,closed',
            'delivery_time' => 'numeric',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors()->first(), $data);
        }

        //return object from user
        $loginUser = $request->user();
        $loginUser->update($request->all());

        // update image
        if ($request->hasFile('image')) {
            $path = public_path();
            $destinationPath = $path . '/images/resturant/'; // upload path
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $name = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
            $image->move($destinationPath, $name); // uploading file to given path
            $loginUser->image = 'images/' . $name;
            $loginUser->save();
        }

        //get all data of resturant
        $data = $request->user()->fresh()->load('neighborhood.city');

        return responseJson(1, 'تم التعديل بنجاح ', $data);
    }

    public function changePassword(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors()->first(), $data);
        }

        $user = $request->user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = bcrypt($request->password);
            if ($user->save()) {
                return responseJson(1, 'تم تغيير كلمة المرور بنجاح');
            } else {
                return responseJson(0, 'حدث خطأ ما برجاء المحاوله مره أخري');
            }
        } else {
            return responseJson(0, 'كلمة المرور التي ادخلتها غير صحيحه');
        }
    }

    public function registerToken(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'token' => 'required',
            'type' => 'required|in:android,ios'
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors()->first(), $data);
        }
        Token::where('token' , $request->token)->delete();

        $token = $request->user()->tokens()->create($request->all());

        return responseJson(1 , 'تم التسجيل بنجاح');
    }


    public function removeToken(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            $data = $validator->errors();
            return responseJson(0, $validator->errors()->first(), $data);
        }
        Token::where('token' , $request->token)->delete();

        return responseJson(1 , 'تم الحذف بنجاح');
    }



}
