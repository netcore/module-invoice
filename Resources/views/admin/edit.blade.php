@extends('crud::layouts.master')

@section('crudName', 'Edit')

@section('crudPanelName', $model->getClassName() . ' ' . $model->invoice_nr)

@section('crud')
    @include('admin::_partials._messages')

    {!! Form::model($model, ['url' => crud_route('update', $model->id)]) !!}
        {{ method_field('PUT') }}
        @include('invoice::admin._form')
    {!! Form::close() !!}
@endsection

