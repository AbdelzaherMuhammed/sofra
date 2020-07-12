@extends('admin.layouts.app')
@inject('model' , 'App\Models\City')
@section('title')
    اضافة حي جديد
@stop


@section('content')

    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                        <i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">

                {!! Form::model($model , [
                    'action' => 'NeighborhoodController@store'
                ]) !!}

                @include('admin.partials.validation_errors')
                @include('admin.neighborhoods.form')



                {!! Form::close() !!}
            </div>
            <!-- /.box-body -->

        </div>
        <!-- /.box -->

    </section>

@endsection


