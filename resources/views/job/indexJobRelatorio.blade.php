@extends('layouts.main')
@section('content')
    <div class="container-fluid">
        {{-- <div class="row justify-content-center">
            <div class="col-12 col-sm-4 col-md-3 mt-4 mb-3">
                <select class="selectpicker show-tick" data-live-search="true" data-size="5">
                    <option value="#">Todas as Listas</option>
                    <option value="#">Triagem</option>
                    <option value="#">Andamento</option>
                    <option value="#">Concluido</option>
                </select>
            </div>
        </div> --}}

        <div class="row pt-4 rel">
            @foreach ($job_select->jobLists->sortBy('position') as $list)
                <div class="col-12 mt-3 rel-btn" data-list_id="{{$list->id}}">
                    <button type="button" data-toggle="tooltip" title="Editar Lista" data-request_data="{{collect(['list_id' => $list->id])->toJson()}}" data-toogle-scjt="modal" data-scjt="edit-list-user-modal" class="btn btn-sm border text-white" style="position: absolute;top: 10px;right: 120px;z-index: 10;"><i class="ri-list-settings-line"></i></button>
                    <button type="button" data-toggle="tooltip" title="Apagar Lista" data-url="{{route('deleteList')}}" data-info="{{collect(['job_id' => $list->job_id,'list_id' => $list->id])->toJson()}}" class="btn btn-sm border border-danger text-danger btn-destroy" style="position: absolute;top: 10px;right: 80px;z-index: 10;"><i class="ri-delete-bin-5-line"></i></button>
                    <div class="header-rel" data-toggle="collapse" data-target="#{{\Str::slug($list->list_name)}}-{{$list->id}}" aria-expanded="true" aria-controls="{{\Str::slug($list->list_name)}}-{{$list->id}}">
                        <h4 class="text-white">{{$list->list_name}}</h4>
                        <span class="btn-info-rel text-white"><i class="ri-subtract-fill"></i></span>
                    </div>
                    <div class="collapse show" id="{{\Str::slug($list->list_name)}}-{{$list->id}}">
                        <div class="row rel-list rel-list-{{$list->id}}" style="min-height: 10px;">
                            @foreach ($list->jobTasks->sortBy('position') as $task)
                                <div data-task_id="{{$task->id}}" class="col-12 my-2 item-rel-btn rel-p-0 position-relative">
                                    <div class="row py-2">
                                        <div class="col-12 col-sm-3"><h5 class="text-white">{{$task->task_title}}</h5></div>
                                        <div class="col-12 col-sm-2"><h5 class="text-white">{{$task->total_time_task}}</h5></div>
                                        <div class="col-12 col-sm-3">
                                            <div class="rel-users d-flex">
                                                @php $avatar_left_px = 0; @endphp
                                                @foreach ($task->jobTaskAssociate as $user)
                                                    <img class="user border" style="left: -{{$avatar_left_px}}px" src="https://ui-avatars.com/api/?name={{str_replace(' ', '+', $user->user->name)}}&color=ffffff&background=5866db">
                                                    @php $avatar_left_px += 6; @endphp
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-2 tags">
                                            <div class="d-flex align-items-center h-100">
                                                @foreach ($task->jobTaskTag as $tag)
                                                    <div class="tag mr-1" style="background-color: {{$tag->tag->tag_color}}"></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn text-danger position-absolute btn-destroy" data-url="{{route('deleteTask')}}" data-info="{{collect(['task_id' => $task->id])->toJson()}}" style="right: 10px; top: 5px; z-index: 10;"><i class="ri-delete-bin-5-line"></i></button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalTask" tabindex="-1" aria-labelledby="modalTaskLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content h-100">
                <div class="modal-body position-relative">
                    <button type="button" class="close position-absolute" style="right: 20px;top: 15px; z-index: 10" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="modal-task"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalTags" tabindex="-1" aria-labelledby="modalTagsLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTagsLabel">Etiquetas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- <div class="row">
                        <div class="col-12 form-group">
                            <input type="text" class="form-control form-control-sm" placeholder="Buscar etiquetas...">
                        </div>
                    </div> --}}

                    <div class="row tags-select">
                        @foreach ($tags as $tag)
                            <div class="col-12 d-flex mb-1">
                                <div class="w-75 py-2 px-1 rounded tag-select text-truncate" title="{{$tag->tag_name ?? ''}}" data-toggle="tooltip" style="background-color: {{$tag->tag_color}}; min-height: 40px;cursor: pointer;" data-dados="{{$tag->toJson()}}">{{$tag->tag_name ?? ''}}</div>
                                <div class="col">
                                    <button type="button" class="btn btn-block border btn-edit-tag-select" data-select="false" data-dados="{{$tag->toJson()}}"><i class="ri-pencil-fill"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <div class="col-12 mt-2">
                            <button type="button" class="btn btn-sm btn-block btn-success btn-new-tag">Criar Nova Etiqueta</button>
                        </div>
                    </div>

                    {{-- Modal Nova Etiqueta --}}
                    <div class="card mt-2 py-3 px-2 m-new-tag d-none">
                        <div class="row">
                            <div class="col-12 mb-2 text-center"><b>Nova etiqueta.</b></div>
                            <div class="col-12 mb-2">
                                <input type="text" class="form-control form-control-sm new-tag-name" placeholder="Nome da Etiqutea">
                            </div>
                            <div class="col-12 mb-2">
                                <button type="button" class="btn btn-block btn-sm border" id="select_color" data-colorHex="#ffffff">Escolha uma Cor</button>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="row justify-content-between">
                                    <div class="col-5"><button type="button" class="btn btn-block btn-sm btn-dark btn-cancel-new-tag">Cancelar</button></div>
                                    <div class="col-5"><button type="button" class="btn btn-block btn-sm btn-success btn-save-new-tag">Salvar</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Modal Edtar Etiqueta --}}
                    <div class="card mt-2 py-3 px-2 m-edit-tag d-none">
                        <div class="row">
                            <button type="button" class="btn btn-cancel-new-tag" style="position: absolute;right: 5px;top: 10px;z-index: 2;"><i class="ri-close-line"></i></button>
                            <div class="col-12 mb-2 text-center"><b>Editar etiqueta.</b></div>
                            <div class="col-12 mb-2">
                                <input type="text" class="form-control form-control-sm edit-tag-name" placeholder="Nome da Etiqutea">
                            </div>
                            <div class="col-12 mb-2">
                                <button type="button" class="btn btn-block btn-sm border" id="select_color_edit" data-colorHex="#ffffff">Escolha uma Cor</button>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="row justify-content-between">
                                    <div class="col-5"><button type="button" class="btn btn-block btn-sm btn-success btn-save-edit-tag">Salvar</button></div>
                                    <div class="col-5"><button type="button" class="btn btn-block btn-sm btn-danger btn-delete-edit-tag">Excluir</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalUserAssoc" tabindex="-1" aria-labelledby="modalUserAssocLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUserAssocLabel">Usuarios</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="user-assoc-select container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalField" tabindex="-1" aria-labelledby="modalFieldLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFieldLabel">Configurar Campo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('taskField')}}" id="formModalField">
                        <input type="hidden" name="job_id" value="{{$job_select->id}}">
                        <input type="hidden" name="list_id">
                        <input type="hidden" name="label_field_id">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="">Tipo de Campo</label>
                                <select name="field_type" class="form-control form-control-sm">
                                    <option value="text">Campo de Texto</option>
                                    <option value="number">Campo de Numérico</option>
                                    {{-- <option value="attachment">Campo de Anexo</option> --}}
                                    <option value="checkbox">Campo de Checkbox</option>
                                    <option value="date">Campo de Data</option>
                                    <option value="date_time">Campo de Data e Hora</option>
                                    <option value="time">Campo de Hora</option>
                                    <option value="email">Campo de E-mail</option>
                                    <option value="select">Campo de Seleção</option>
                                    <option value="radio">Campo de Seleção em Lista</option>
                                    <option value="moeda">Campo de Moeda</option>
                                </select>
                            </div>

                            {{-- Checkbox, Radio & Select --}}
                            <div class="form-group col-12 d-none">
                                <label for="">Adcionar Nome na Lista</label>
                                <div class="input-group input-group-sm add-list">
                                    <input type="text" class="form-control add-list-name">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownlist" data-toggle="dropdown" aria-expanded="false"></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownlist"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- Data --}}
                            <div class="form-group col-12 d-none">
                                <label for="">Formato da Data</label>
                                <select name="date_mask" class="form-control form-control-sm">
                                    <option value="MM/DD/YYYY">MM/DD/AAAA</option>
                                    <option value="MM/DD/YY">MM/DD/AA</option>
                                    <option value="DD/MM/YYYY">DD/MM/AAAA</option>
                                    <option value="DD/MM/YY">DD/MM/AA</option>
                                    <option value="DD-MM-YYYY">DD-MM-AAAA</option>
                                    <option value="DD-MM-YY">DD-MM-AA</option>
                                    <option value="MM-DD-YYYY">MM-DD-AAAA</option>
                                    <option value="MM-DD-YY">MM-DD-AA</option>
                                    <option value="YYYY-MM-DD">AAAA-MM-DD</option>
                                </select>
                            </div>
                            {{-- Data e Hora --}}
                            <div class="form-group col-12 d-none">
                                <label for="">Formato de Data e Hora</label>
                                <select name="date_time_mask" class="form-control form-control-sm">
                                    <option value="MM/DD/YYYY HH:mm:ss">MM/DD/AAAA HH:mm:ss</option>
                                    <option value="MM/DD/YY HH:mm:ss">MM/DD/AA HH:mm:ss</option>
                                    <option value="DD/MM/YYYY HH:mm:ss">DD/MM/AAAA HH:mm:ss</option>
                                    <option value="DD/MM/YY HH:mm:ss">DD/MM/AA HH:mm:ss</option>
                                    <option value="DD-MM-YYYY HH:mm:ss">DD-MM-AAAA HH:mm:ss</option>
                                    <option value="DD-MM-YY HH:mm:ss">DD-MM-AA HH:mm:ss</option>
                                    <option value="MM-DD-YYYY HH:mm:ss">MM-DD-AAAA HH:mm:ss</option>
                                    <option value="MM-DD-YY HH:mm:ss">MM-DD-AA HH:mm:ss</option>
                                    <option value="YYYY-MM-DD HH:mm:ss">AAAA-MM-DD HH:mm:ss</option>
                                </select>
                            </div>
                            {{-- Moeda --}}
                            <div class="form-group col-12 d-none">
                                <label for="">Formato de Moeda</label>
                                <select name="moeda_mask" class="form-control form-control-sm">
                                    <option value=",">ex: 1999,99</option>
                                    <option value=".,">ex: 1.999,99</option>
                                    <option value=".">ex: 1999.99</option>
                                    <option value=",.">ex: 1,999.99</option>
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <label for="">Titulo do Campo</label>
                                <input type="text" name="label_field" class="form-control form-control-sm">
                            </div>
                            <div class="form-group col-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="field_required" id="field_required_check" value="true">
                                    <label for="field_required_check" class="form-check-label">Campo é obrigatorio?</label>
                                </div>
                            </div>
                            <div class="form-group col-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="field_edit_lists" id="field_edit_lists_check" value="true" checked>
                                    <label for="field_edit_lists_check" class="form-check-label">Campo é editavel em outras listas?</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-detroy-field-custom d-none">Excluir</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary btn-save" data-target="#formModalField">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const newTag = `{{route('newTag')}}`;
        const deleteTag = `{{route('deleteTag')}}`;
        const taskFieldUpdate = `{{route('taskFieldUpdate')}}`;
        const listUpdate = `{{route('listUpdate')}}`;
        const taskUpdate = `{{route('taskUpdate')}}`;
    </script>
    <script src="{{asset('js/indexJobRelatorio.min.js')}}"></script>
@endsection