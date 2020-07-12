<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = City::all();

        return view('admin.cities.index' , compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name'  => 'required|unique:cities,name'
        ];

        $messages = [
            'name.required' => 'اسم المدينه مطلوب',
            'name.unique'   => 'قيمة الاسم مستخدمه من قبل'
        ];

        $this->validate($request , $rules , $messages);

        $record = City::create($request->all());
        flash()->success('تمت الاضافه بنجاح');

        return redirect(route('cities.index'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = City::findOrFail($id);

        return view('admin.cities.edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'  => 'unique:cities,name,'.$id
        ];

        $messages = [
            'name.required' => 'اسم المدينه مطلوب',
            'name.unique'   => 'قيمة الاسم مستخدمه من قبل'
        ];

        $this->validate($request , $rules , $messages);

        $record = City::findOrFail($id);

        $record->update($request->all());

        flash()->success('تم التعديل بنجاح');

        return redirect(route('cities.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = City::findOrFail($id);

        if ($record->neighborhoods()->count())
        {
            flash()->error('عفوا لا يمكن الحذف ، توجد احياء مرتبطه بهذه المدينه');
            return back();
        }else
        {
            $record->delete();
            flash()->success('تم الحذف بنجاح');
            return redirect(route('cities.index'));
        }
    }
}
