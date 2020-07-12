<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $records = Neighborhood::all();

        return view('admin.neighborhoods.index' , compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $model = Neighborhood::all();

        return view('admin.neighborhoods.create' , compact('model'));
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
            'name' => 'required|unique:neighborhoods,name',
            'city_id' => 'required|exists:cities,id'
        ];

        $messages = [
            'name.required' => 'اسم المدينه مطلوب',
            'name.unique'   => 'قيمة الاسم مستخدمه من قبل',
            'city.id'       => 'المدينه مطلوبه',
            'city.exists'   => 'المدينه يجب ان تكون ضمن قائمة المدن'
        ];

        $this->validate($request,$rules,$messages);

        $record = Neighborhood::create($request->all());
        flash()->success('تم اضافة الحي بنجاح');

        return redirect(route('neighborhoods.index'));
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
        $model = Neighborhood::findOrFail($id);
        return view('admin.neighborhoods.edit' , compact('model'));
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
            'name' => 'unique:neighborhoods,name,'.$id,
            'city_id' => 'exists:cities,id'
        ];

        $messages = [
            'name.required' => 'اسم المدينه مطلوب',
            'name.unique'   => 'قيمة الاسم مستخدمه من قبل',
            'city_id.required'       => 'المدينه مطلوبه',
            'city_id.exists'   => 'المدينه يجب ان تكون ضمن قائمة المدن'
        ];

        $this->validate($request,$rules,$messages);

        $record = Neighborhood::findOrFail($id);

        $record->update($request->all());

        flash()->success('تم التعديل بنجاح');
        return redirect(route('neighborhoods.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = Neighborhood::findOrFail($id);

        $record->delete();
        flash()->success('تم الحذف بنجاح');

        return redirect(route('neighborhoods.index'));
    }
}
