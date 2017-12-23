@extends('crud::layouts.master')

@section('crudName', 'Create')

@section('crudPanelName', 'Create new')

@section('crud')
    @include('admin::_partials._messages')

    {!! Form::model($model, ['url' => crud_route('store')]) !!}
    {{ method_field('POST') }}
    @include('invoice::admin._form')
    {!! Form::close() !!}
@endsection
