@inject('resturant','App\Models\Resturant')
<div class="form-group">
    <label for="name">الاسم</label>
    {!! Form::text('name',null,[
        'class' =>'form-control'
    ]) !!}

    <br>
    <label for="name">المطعم</label>
    {!! Form::select('resturant_id', $resturant->pluck('name', 'id')->toArray(), null,[
        'class' =>'form-control',
        'placeholder' => 'اختر مطعم'
    ]) !!}

</div>

<div class="form-group">
    <button class="btn btn-primary" type="submit">تأكيد</button>
</div>



