<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SI Anjar | Log in</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary mb-2">
        <div class="card-header text-center">
            <b class="text-center" style="font-size: 25px;color: #007bff;">SI ANJAR</b>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Masukan akun anda untuk memulai</p>

            <form action="" method="post" id="form-login">
                {{ csrf_field() }}
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Nama Pengguna">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Kata Sandi">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="social-auth-links text-center mt-2 mb-3">
                    <button type="submit" class="btn btn-block btn-primary">
                        <i class="fa fa-sign-in-alt"></i> Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{--    <div class="text-left">--}}
        {{--        <div class="text-bold">Catatan:</div>--}}
        {{--        <div>Jika ada kendala mengenai system bisa konsultasikan ke tim terkait</div>--}}
        {{--        <div>WA: 085853640186</div>--}}
        {{--    </div>--}}
</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('dist/js/load.modal.js') }}"></script>
<script src="{{ asset('dist/js/app.js') }}"></script>
<script type="text/javascript">
    $('#form-login').formSubmit({
        beforeSubmit: function(form) {
            form.setDisabled(true);
        },
        successCallback: function(res, form) {
            form.setDisabled(false);
            if(res.result)
                window.location.reload();

            AlertNotif.toastr.response(res);
        },
        errorCallback: function(xhr, form) {
            form.setDisabled(false);
            AlertNotif.adminlte.error(DBMessage.ERROR_SYSTEM_MESSAGE, {
                title: DBMessage.ERROR_SYSTEM_TITLE
            })
        }
    });
</script>
</body>
</html>
