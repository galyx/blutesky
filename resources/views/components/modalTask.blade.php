<form action="{{route('taskUpdate')}}" method="post" id="form-task-modal">
    <input type="hidden" name="job_id" value="{{$job->id}}">
    <input type="hidden" name="list_id" value="{{$job_list->id}}">
    <input type="hidden" name="task_id" value="{{$job_task->id}}">
    <div class="row modal-task justify-content-between">
        <div class="col-12 mb-2 task_title">
            <h3>{{$job_task->task_title}}</h3>
            <input type="text" name="task_title" value="{{$job_task->task_title}}" class="form-control d-none">
        </div>
        <div class="col-12 col-sm-6 task-card">
            <div class="row px-2">
                <div class="form-group col-12">
                    <button type="button" class="btn btn-sm border mb-2" data-toggle="modal" data-target="#modalTags">Etiquetas <i class="ri-add-line"></i></button>
                    <div class="div-tags container">
                        <div class="row row-cols-4 tags edit-task-TU">
                            @foreach ($job_task->jobTaskTag as $tag)
                                <div class="col mb-1 px-1" id="tag_id-{{$tag->tag_id}}">
                                    <input type="hidden" name="tag_id[]" value="{{$tag->tag_id}}">
                                    <div class="w-100 py-2 px-1 rounded text-truncate" title="{{$tag->tag->tag_name ?? ''}}" data-toggle="tooltip" style="background-color: {{$tag->tag->tag_color}}; min-height: 40px;cursor: pointer;">{{$tag->tag->tag_name ?? ''}}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-group col-12">
                    <button type="button" class="btn btn-sm border mb-2" data-toggle="modal" data-target="#modalUserAssoc" data-users="{{$job->jobUsers->map(function($query){return $query->user;})->toJson()}}">Usuarios <i class="ri-add-line"></i></button>
                    <div class="div-user-assoc container">
                        <div class="d-flex user-assoc edit-task-TU">
                            @foreach ($job_task->jobTaskAssociate as $user)
                                <div class="mb-1 mx-1" style="border-radius: 50%;width: 10%;" id="user_assoc_id-{{$user->user_id}}">
                                    <input type="hidden" name="user_assoc_id[]" value="{{$user->user_id}}">
                                    <img style="border-radius: 50%;width: 100%;" src="https://ui-avatars.com/api/?name={{str_replace(' ', '+', $user->user->name)}}&color=ffffff&background=5866db" alt="">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 delivery_date_task">
                    <label for="">Data da Entrega</label>
                    <p>{{date('d/m/Y', strtotime($job_task->delivery_date_task))}}</p>
                    <input type="text" name="delivery_date_task" value="{{date('d/m/Y', strtotime($job_task->delivery_date_task))}}" class="form-control d-none date-mask-single">
                </div>
            </div>

            <div class="row px-2 @if(jobsUserAccess($job->id)) not-custom-fields @else not-edit-custom-fields @endif">
                @foreach ($custom_fields->groupBy('list_id') as $custom_fieldsF)
                    @foreach ($custom_fieldsF as $field)
                        @php
                            $field_value = $job_task->jobTaskFields->where('field_label_id', $field->label_field_id)->first();
                            // ----
                        @endphp
                        @if (!$field->list_id)
                            @include('components.customFieldsTask')
                        @endif
                        @if ($field->jobList)
                            @if ($field->jobList->position < $job_list->position)
                                @include('components.customFieldsTask')
                            @endif
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
        <div class="col-12 col-sm-6 task-card">
            <div class="row px-2 @if(jobsUserAccess($job->id)) custom-fields @else not-edit-custom-fields @endif">
                @foreach ($custom_fields as $field)
                    @if ($field->jobList)
                        @if ($field->jobList->id == $job_list->id)
                            @include('components.customFieldsTask')
                        @endif
                    @endif
                @endforeach
            </div>
            @if(jobsUserAccess($job->id))
                <div class="row px-2">
                    <div class="form-group col-12">
                        <button type="button" class="btn btn-sm border mb-2" data-toggle="modal" data-target="#modalField">Novo Campo <i class="ri-add-line"></i></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</form>