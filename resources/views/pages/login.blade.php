@extends('layout.blank')
@section('title', 'Đăng nhập')
@section('style')
	<style>

	</style>
@endsection
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="./index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="{{ asset('assets/images/logos/dark-logo.svg') }}" width="180" alt="">
                </a>
                <p class="text-center">Đăng nhập tài khoản</p>
                <form id="loginForm" method="POST" action="{{ route('login.post') }}">
                  @csrf
                  <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                  </div>
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                      <label class="form-check-label text-dark" for="flexCheckChecked">
                        Ghi nhớ tài khoản
                      </label>
                    </div>
                    <a class="text-primary fw-bold" href="./index.html">Quên mật khẩu ?</a>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Đăng nhập</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">Chưa có tài khoản?</p>
                    <a class="text-primary fw-bold ms-2" href="{{ route('register')}}">Tạo tài khoản</a>
                  </div>
                </form>
                <div id="loginMessage"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @section('script')
	<script>
	document.getElementById('loginForm').addEventListener('submit', async function(e) {
		e.preventDefault();
		const form = e.target;
		const data = {
			phone: form.phone.value,
			password: form.password.value,
		};
		try {
			const res = await fetch('{{ route("login.post") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'Accept': 'application/json',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				},
				body: JSON.stringify(data)
			});
			const result = await res.json();
			if (res.ok) {
              if (result.user.role === 'admin') {
              window.location.href = '{{route("dashboard")}}';
        } else {
            window.location.href = '{{route("home")}}';
        }
    } else {
        let msg = '';
        if(result.errors) {
            for (const key in result.errors) {
                msg += result.errors[key].join('<br>');
            }
        } else if(result.message) {
            msg = result.message;
        }
        document.getElementById('loginMessage').innerHTML = '<div class="alert alert-danger">' + msg + '</div>';
    }
		} catch (err) {
			document.getElementById('loginMessage').innerHTML = '<div class="alert alert-danger">Lỗi kết nối máy chủ!</div>';
		}
	});
	</script>
@endsection