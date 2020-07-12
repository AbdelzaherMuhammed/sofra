<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Category::all();

        return view('admin.categories.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:categories,name',
            'resturant_id' => 'required|exists:cities,id'

        ];

        $messages = [
            'name.required' => 'اسم المدينه مطلوب',
            'name.unique' => 'قيمة الاسم مستخدمه من قبل',
            'resturant_id.required'       => 'المطعم مطلوب',
            'resturant_id.exists'   => 'المطعم يجب ان تكون ضمن قائمة المدن'
        ];

        $this->validate($request, $rules, $messages);

        $record = Category::create($request->all());
        flash()->success('تمت الاضافه بنجاح');

        return redirect(route('categories.index'));

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Category::findOrFail($id);

        return view('admin.categories.edit', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'unique:categories,name,'.$id,
            'resturant_id' => 'exists:cities,id'

        ];

        $messages = [
            'name.required' => 'اسم المدينه مطلوب',
            'name.unique' => 'قيمة الاسم مستخدمه من قبل',
            'resturant_id.required'       => 'المطعم مطلوب',
            'resturant_id.exists'   => 'المطعم يجب ان تكون ضمن قائمة المدن'
        ];

        $this->validate($request, $rules, $messages);

        $record = Category::findOrFail($id);

        $record->update($request->all());

        flash()->success('تم التعديل بنجاح');

        return redirect(route('categories.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = Category::findOrFail($id);

        if ($record->products()->count()) {
            flash()->error('عفوا لا يمكن الحذف ، توجد منتجات مرتبطه بهذا التصنيف');
            return back();
        } else {
            $record->delete();
            flash()->success('تم الحذف بنجاح');
            return redirect(route('categories.index'));
        }
    }
}
