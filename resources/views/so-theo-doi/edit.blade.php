@extends('welcome')

@section('content')
    <h3 class="mb-4">Sửa sổ theo dõi: {{ $group->book_name }}</h3>

    <form method="POST" action="{{ route('so-theo-doi.update', $group) }}">
        @csrf
        @method('PUT')

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên sổ <span class="text-danger">*</span></label>
                    <input type="text" name="book_name" class="form-control" value="{{ old('book_name', $group->book_name) }}"
                        required>
                    @error('book_name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $group->description) }}</textarea>
                </div>
            </div>
            <div class="card-footer bg-light text-end">
                <a href="{{ route('so-theo-doi.index') }}" class="btn btn-outline-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật
                </button>
            </div>
        </div>
    </form>
@endsection
