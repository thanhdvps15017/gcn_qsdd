@extends('welcome')
@section('content')
    @include('ho-so._form', [
        'title' => 'THÊM MỚI HỒ SƠ',
        'action' => route('ho-so.store'),
        'method' => 'POST',
        'submitText' => 'Lưu hồ sơ'
    ])
@endsection