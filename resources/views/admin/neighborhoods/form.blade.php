@inject('city' , 'App\Models\City')
<div class="form-group">

    <label for="name">الاسم</label>
    {!! Form::text('name',null,[
        'class' =>'form-control'
    ]) !!}

    <br>
    <label for="name">المدينه</label>
    {!! Form::select('city_id',$city->pluck('name', 'id')->toArray(), null,[
        'class' =>'form-control',
        'placeholder' => 'اختر مدينه'
    ]) !!}

</div>

<div class="form-group">
    <button class="btn btn-primary" type="submit">تأكيد</button>
</div>



