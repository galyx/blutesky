<input type="hidden" name="job_id" value="{{$job->id}}">
<div class="row px-2">
    <div class="form-group col-12">
        <label for="">Titulo da tarefa</label>
        <input type="text" name="task_title" class="form-control">
    </div>
    <div class="form-group col-12">
        <button type="button" class="btn btn-sm border mb-2" data-toggle="modal" data-target="#modalTags">Etiquetas <i class="ri-add-line"></i></button>
        <div class="div-tags container">
            <div class="row row-cols-4 tags"></div>
        </div>
    </div>
    <div class="form-group col-12">
        <button type="button" class="btn btn-sm border mb-2" data-toggle="modal" data-target="#modalUserAssoc" data-users="{{$job->jobUsers->map(function($query){return $query->user;})->toJson()}}">Usuarios <i class="ri-add-line"></i></button>
        <div class="div-user-assoc container">
            <div class="d-flex user-assoc"></div>
        </div>
    </div>
    <div class="form-group col-12">
        <label for="">Data da Entrega</label>
        <input type="text" name="delivery_date_task" class="form-control date-mask-single">
    </div>
</div>

<div class="row px-2 @if(jobsUserAccess($job->id)) custom-fields @endif">
    @each('components.customFieldsTask', $custom_fields, 'field')
</div>

@if(jobsUserAccess($job->id))
    <div class="row px-2">
        <div class="form-group col-12">
            <button type="button" class="btn btn-sm border mb-2" data-toggle="modal" data-target="#modalField">Novo Campo <i class="ri-add-line"></i></button>
        </div>
    </div>
@endif