@extends('welcome')

@section('title', 'Cài đặt chung')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">CÀI ĐẶT BACKGROUND TRANG ĐĂNG NHẬP</h5>

                    <!-- Nếu sau này có thêm nút khác (ví dụ reset về mặc định) thì để ở đây -->
                    <!-- <button class="btn btn-light btn-sm">...</button> -->
                </div>

                {{-- BODY --}}
                <div class="card-body p-4">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Preview --}}
                    <div class="mb-4">
                        <label class="fw-semibold mb-2 d-block">Background hiện tại</label>

                        @if ($loginBg)
                            <img src="{{ asset('storage/' . $loginBg) }}" class="img-fluid rounded border shadow-sm"
                                style="max-height: 300px; object-fit: cover;">
                        @else
                            <div class="text-muted fst-italic border rounded p-3 bg-light">
                                Chưa thiết lập background tùy chỉnh
                            </div>
                        @endif
                    </div>

                    {{-- Form upload --}}
                    <form method="POST" action="{{ route('settings.login-bg.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="fw-semibold d-block mb-2">Chọn ảnh background mới</label>
                            <input type="file" name="login_bg"
                                class="form-control @error('login_bg') is-invalid @enderror" accept="image/*" required>
                            @error('login_bg')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text text-muted mt-1">
                                Đề xuất kích thước: 1920×1080 hoặc tỷ lệ tương tự (ảnh sẽ được cover)
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Lưu background
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
