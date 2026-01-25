@extends('welcome')

@section('title', 'Quản lý tài khoản')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">DANH SÁCH USER</h5>

                    <button class="btn btn-light btn-sm" onclick="openCreateUser()">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </button>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive p-3 overflow-visible">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>USERNAME</th>
                                <th class="d-none d-md-table-cell">SĐT</th>
                                <th class="d-none d-md-table-cell">ROLE</th>
                                <th width="5%" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $user->username }}</td>
                                    <td class="d-none d-md-table-cell">{{ $user->phone ?? '—' }}</td>
                                    <td class="d-none d-md-table-cell">
                                        @if ($user->roles->isNotEmpty())
                                            <span class="badge bg-primary">
                                                {{ $user->roles->first()->name }}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">Chưa có</span>
                                        @endif
                                    </td>

                                    <td class="text-end position-static">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-2" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <button class="dropdown-item d-flex align-items-center gap-2"
                                                    onclick="openShowUser({{ $user->id }})">
                                                    <i class="bi bi-eye text-info"></i>
                                                    <span>Xem chi tiết</span>
                                                </button>


                                                <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                    onclick='openEditUser(
                                                            @json($user),
                                                            @json($user->roles->first()?->name ?? null)
                                                        )'>
                                                    <i class="bi bi-pencil-square"></i>
                                                    Chỉnh sửa
                                                </button>

                                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                    onsubmit="return confirm('Bạn chắc chắn muốn xoá user này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <i class="bi bi-trash"></i>
                                                        Xoá
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Chưa có user
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL CREATE / EDIT ================= --}}
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="fw-bold" id="userModalTitle"></h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="userForm" method="POST">
                    @csrf
                    <input type="hidden" id="methodField">

                    <div class="modal-body row g-3 p-4">

                        <div class="col-md-6">
                            <label class="fw-bold">Username *</label>
                            <input name="username" id="username" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Tên</label>
                            <input name="name" id="name" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Email</label>
                            <input name="email" id="email" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Số điện thoại</label>
                            <input name="phone" id="phone" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="fw-bold">Mật khẩu</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="fw-bold">Role *</label>
                            <select name="role" id="roleSelect" class="form-select" required>
                                <option value="">-- Chọn role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                        <button class="btn btn-primary">Lưu</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- ================= MODAL SHOW ================= --}}
    <div class="modal fade" id="showUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-info text-white">
                    <h5 class="fw-bold">Chi tiết User</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p><b>Username:</b> <span id="s_username"></span></p>
                    <p><b>Tên:</b> <span id="s_name"></span></p>
                    <p><b>Email:</b> <span id="s_email"></span></p>
                    <p><b>SĐT:</b> <span id="s_phone"></span></p>
                    <p><b>Role:</b> <span class="badge bg-primary" id="s_role"></span></p>
                    <p><b>Ngày tạo:</b> <span id="s_created"></span></p>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        const userModal = new bootstrap.Modal(document.getElementById('userModal'));
        const showModal = new bootstrap.Modal(document.getElementById('showUserModal'));
        const form = document.getElementById('userForm');

        function openCreateUser() {
            form.reset();
            form.action = "{{ route('users.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('userModalTitle').innerText = 'Thêm User';
            userModal.show();
        }

        function openEditUser(user, currentRole) {
            form.reset();
            form.action = `/settings/users/${user.id}`;

            document.getElementById('userModalTitle').innerText = 'Sửa tài khoản';
            document.getElementById('methodField').innerHTML =
                '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('username').value = user.username;
            document.getElementById('name').value = user.name || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('phone').value = user.phone || '';

            document.getElementById('roleSelect').value = currentRole || '';

            userModal.show();
        }


        function openShowUser(id) {
            fetch(`/settings/users/${id}`)
                .then(r => r.json())
                .then(u => {
                    s_username.innerText = u.username;
                    s_name.innerText = u.name ?? '-';
                    s_email.innerText = u.email ?? '-';
                    s_phone.innerText = u.phone ?? '-';
                    s_role.innerText = u.role ?? '-';
                    s_created.innerText = u.created;
                    showModal.show();
                });
        }
    </script>
@endpush
