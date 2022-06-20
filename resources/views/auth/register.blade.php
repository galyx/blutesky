<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @isset($job_name)
        <title>JOB - {{$job_name}}</title>
    @else
        <title>Sistema TESCJ</title>
    @endisset

    <link rel="stylesheet" href="{{asset('plugins/bootstrap-4.6.1/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/bootstrap-select-1.13.14/css/bootstrap-select.min.css')}}">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('css/custom.min.css')}}">
</head>
<body>
    <div class="container" style="height: 100vh">
        <div class="row h-100 align-items-center justify-content-center">
            <div class="col-12 col-sm-4" style="background-color: #1A242D;border-radius: 1rem;padding: 10px 20px;">
                <form id="form-login" action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-12 text-center text-white mt-3">
                            <h4>Bem vindo a TESCJ</h4>
                            <p>Crie uma conta para utilizar os serviços</p>
                        </div>

                        <div class="form-group col-12">
                            <input type="text" class="form-control" name="name" placeholder="Seu Nome Completo">
                            <span style="font-size: .8rem; color: #9C9C9C;">Informe seu Nome Completo</span>
                        </div>
                        <div class="form-group col-12">
                            <input type="email" class="form-control" name="email" placeholder="Email">
                            <span style="font-size: .8rem; color: #9C9C9C;">Informe seu melhor E-mail</span>
                        </div>
                        <div class="form-group col-12">
                            <input type="password" class="form-control" name="password" placeholder="Senha">
                            <span style="font-size: .8rem; color: #9C9C9C;">Informe uma Senha</span>
                        </div>
                        <div class="form-group col-12">
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Confirmar Senha">
                            <span style="font-size: .8rem; color: #9C9C9C;">Repita sua Senha</span>
                        </div>

                        <div class="form-group col-12">
                            <input name="terms" id="terms" type="checkbox">
                            <label for="terms"><a target="_blank" href="#">ACEITO OS TERMOS E CONDIÇÕES</a></label>
                        </div>

                        <div class="form-group col-12 text-center">
                            <button type="button" id="btn-login" class="btn btn-block btn-primary">Criar conta</button>
                        </div>
                        <div class="form-group col-12 text-center">
                            <a href="{{ route('login') }}" class="btn btn-outline-primary"> Já possuo registro </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{asset('plugins/jquery-3.6.0.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).on('click', '#btn-login', function () {
            var btn = $(this);
            var form = $('#form-login').serialize();
            var url = $('#form-login').attr('action');
            btn.html('<div class="spinner-border text-light" role="status"></div>');
            btn.prop('disabled', true);
            $('#form-login').find('input').removeClass('is-invalid').prop('disabled', true);
            $('#form-login').find('.invalid-feedback').remove();

            $.ajax({
                url: url,
                type: 'POST',
                data: form,
                success: (data) => {
                    // console.log(data);

                    window.location.href = data;
                },
                error: (err) => {
                    // console.log(err);
                    var errors = err.responseJSON.errors;

                    btn.html('Criar conta');
                    btn.prop('disabled', false);
                    $('#form-login').find('input').prop('disabled', false);

                    if (errors) {
                        // console.log(errors);
                        $.each(errors, (key, value) => {
                            $('#form-login').find('[name="' + key + '"]').addClass('is-invalid').parent().append('<span class="invalid-feedback">' + value[0] + '</span>');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: err.responseJSON.invalid
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>