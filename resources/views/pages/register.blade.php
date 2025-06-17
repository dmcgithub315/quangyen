@extends('layout.blank')
@section('title', 'Đăng ký tài khoản')
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
              <img src="../assets/images/logos/dark-logo.svg" width="180" alt="">
            </a>
            <p class="text-center">Đăng ký tài khoản</p>
            <form id="registerForm">
              <div class="mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
              </div>
              <div class="mb-4">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="mb-4">
                <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
              </div>
              <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Đăng ký</button>
              <div class="d-flex align-items-center justify-content-center">
                <p class="fs-4 mb-0 fw-bold">Đã có tài khoản?</p>
                <a class="text-primary fw-bold ms-2" href="{{route('login')}}">Đăng nhập</a>
              </div>
            </form>
            <div id="registerMessage"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

  @section('script')
	<script>
	document.getElementById('registerForm').addEventListener('submit', async function(e) {
		e.preventDefault();
		const form = e.target;
		const data = {
			name: form.name.value,
			password: form.password.value,
			password_confirmation: form.password_confirmation.value,
			phone: form.phone.value,
			address: '', // Nếu có trường địa chỉ thì thêm vào form
		};
		try {
			const res = await fetch('/api/register', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'Accept': 'application/json',
				},
				body: JSON.stringify(data)
			});
			const result = await res.json();
			if (res.ok) {
				document.getElementById('registerMessage').innerHTML = '<div class="alert alert-success" role="alert">Đăng ký thành công!</div>';
				// Chuyển hướng sang trang đăng nhập sau 5 giây
				setTimeout(() => {
					window.location.href = '{{route("login")}}';
				}, 3000);
			} else {
				let msg = '';
				if(result.errors) {
					for (const key in result.errors) {
						msg += result.errors[key].join('<br>');
					}
				} else if(result.message) {
					msg = result.message;
				}
				document.getElementById('registerMessage').innerHTML = '<div class=\"alert alert-danger\">' + msg + '</div>';
			}
		} catch (err) {
			document.getElementById('registerMessage').innerHTML = '<div class=\"alert alert-danger\">Lỗi kết nối máy chủ!</div>';
		}
	});
	</script>


@endsection
