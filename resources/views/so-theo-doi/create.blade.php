@extends('welcome')

@section('content')
    <h3 class="mb-4">Tạo sổ theo dõi mới</h3>

    <form method="POST" action="{{ route('so-theo-doi.store') }}">
        @csrf

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên sổ <span class="text-danger">*</span></label>
                    <input type="text" name="ten_so" class="form-control" value="{{ old('ten_so') }}" required>
                    @error('ten_so')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea name="mo_ta" class="form-control" rows="4">{{ old('mo_ta') }}</textarea>
                </div>
            </div>
            <div class="card-footer bg-light text-end">
                <a href="{{ route('so-theo-doi.index') }}" class="btn btn-outline-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Tạo sổ
                </button>
            </div>
        </div>
    </form>
@endsection
