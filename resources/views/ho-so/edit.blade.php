@extends('welcome')
@section('content')
    @include('ho-so._form', [
        'title' => 'CHỈNH SỬA HỒ SƠ',
        'action' => route('ho-so.update', $hoSo),
        'method' => 'PUT',
        'submitText' => 'Cập nhật hồ sơ',
        'hoSo' => $hoSo,
    ])
@endsection
