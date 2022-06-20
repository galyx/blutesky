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
    <link rel="stylesheet" href="{{asset('plugins/colpick-master/css/colpick.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/bootstrap-select-1.13.14/css/bootstrap-select.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/jquery-ui-1.13.1/jquery-ui.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/jquery-ui-1.13.1/jquery-ui.theme.min.css')}}">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('css/custom.min.css')}}">
</head>
<body>
    <header>
        <div class="container py-2 header">
            <div class="row">
                <div class="col-12 col-sm-2"><a href="{{route('dash')}}"><h3 class="text-white">TESCJ</h3></a></div>
                <div class="col-12 col-sm-3">
                    @if (Request::is('job/*'))
                        <select class="selectpicker show-tick select-job" data-live-search="true" data-size="5">
                            @foreach (jobsUser() as $job)
                                <option value="{{$job->job->id}}" data-url="{{route('job', $job->job->id)}}" @if($job->job->id == $job_select->id) selected @endif>JOB: {{$job->job->job_name}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-12 col-sm-7">
                    <ul class="nav justify-content-end">
                        @if (Request::is('job/*'))
                            <li class="nav-item">
                                <button type="button" class="btn text-white" data-toggle="tooltip" data-placement="bottom" title="Criar Tarefa" data-toogle-scjt="modal" data-scjt="create-task-modal"><i class="ri-task-line" style="font-size: 1.2rem;"></i></button>
                            </li>
                            @if(jobsUserAccess($job_select->id))
                                <li class="nav-item">
                                    <button type="button" class="btn text-white" data-toggle="tooltip" data-placement="bottom" title="Criar Lista" data-toogle-scjt="modal" data-scjt="create-list-user-modal"><i class="ri-list-check" style="font-size: 1.2rem;"></i></button>
                                </li>
                            @endif
                            <li class="nav-item">
                                <button type="button" class="btn text-white" data-toggle="tooltip" data-placement="bottom" title="Usuarios Associados" data-toogle-scjt="modal" data-scjt="associates-modal"><i class="ri-user-add-line"></i></button>
                            </li>
                        @endif
                        <li class="nav-item">
                            <button type="button" class="btn text-white" data-toggle="tooltip" data-placement="bottom" title="Configuração do Usuario"><i class="ri-user-settings-line"></i></button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="btn text-white" data-toggle="tooltip" data-placement="bottom" title="Filtros"><i class="ri-filter-2-line" style="font-size: 1.2rem;"></i></button>
                        </li>
                        {{-- <li class="nav-item">
                            <div class="dropdown">
                                <button class="btn text-white dropdown-toggle" type="button" id="dropdownConfigHeader" data-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-settings-2-line" style="font-size: 1.2rem;"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownConfigHeader">
                                    <a class="dropdown-item" href="#">Conta</a>
                                    <a class="dropdown-item" href="#">Usuarios</a>
                                    <a class="dropdown-item" href="#">Automações</a>
                                </div>
                            </div>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#" style="font-size: 1.2rem;font-weight: bold;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">SAIR</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Modal -->
    <div class="modal fade" id="modalGeral" tabindex="-1" aria-labelledby="modalGeralLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGeralLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="formModalGeral" method="post"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary btn-save" data-target="#formModalGeral">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container py-2 footer-direitos">TESCJ  {{date('Y')}} - todos os direitos reservados</div>
    </footer>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="{{asset('plugins/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('plugins/mask.jquery.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{asset('plugins/bootstrap-4.6.1/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('plugins/colpick-master/js/colpick.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-select-1.13.14/js/bootstrap-select.min.js')}}"></script>
    <script src="{{asset('plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('plugins/jquery-ui-1.13.1/jquery-ui.min.js')}}"></script>

    <script src="{{asset('js/custom.min.js')}}"></script>

    @yield('js')
</body>
</html>