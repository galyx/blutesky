<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\JobTag;
use App\Models\JobList;
use App\Models\JobTask;
use App\Models\JobUser;
use App\Models\JobTaskTag;
use App\Models\CustomField;
use App\Models\JobTaskField;
use Illuminate\Http\Request;
use App\Models\JobTaskAssociate;

class JobTaskController extends Controller
{
    ##Trabalho##
    public function newJob(Request $request)
    {
        $jobUser = JobUser::with('job')->where('user_id', auth()->user()->id)->where('access', 1)->get();
        $create_job['job_name'] = $request->new_job_name;
        $create_job['position'] = $jobUser->count()+1;
        $job = Job::create($create_job);

        $create_job_user['job_id'] = $job->id;
        $create_job_user['user_id'] = auth()->user()->id;
        $create_job_user['access'] = 1;
        JobUser::create($create_job_user);

        return response()->json(['url_redirect' => route('job', $job->id)]);
    }

    public function editJob(Request $request)
    {
        Job::find($request->id)->update(['job_name' => $request->new_job_name]);
    }

    public function deleteJob(Request $request)
    {
        $job_task_ids = JobTask::where('job_id', $request->id)->get()->map(function($query){return $query->id;});
        JobUser::where('job_id', $request->id)->delete();
        CustomField::where('job_id', $request->id)->delete();
        JobList::where('job_id', $request->id)->delete();
        JobTag::where('job_id', $request->id)->delete();
        JobTask::where('job_id', $request->id)->delete();
        JobTaskField::where('job_id', $request->id)->delete();

        JobTaskAssociate::whereIn('task_id', $job_task_ids)->delete();
        JobTaskTag::whereIn('task_id', $job_task_ids)->delete();

        Job::where('id', $request->id)->delete();
    }
    ############

    ##Listas####
    public function newList(Request $request)
    {
        if(!isset($request->list_id)){
            $jobList = JobList::where('job_id', $request->job_id)->get();
            $create_job_list['position'] = $jobList->count()+1;
        }

        $create_job_list['job_id'] = $request->job_id;
        $create_job_list['list_name'] = $request->new_list_name;
        $create_job_list['visibility'] = $request->visibility;
        $create_job_list['access'] = $request->access;

        if(isset($request->list_id)){
            $joblist = JobList::find($request->list_id)->update($create_job_list);
        }else{
            $joblist = JobList::create($create_job_list);
        }

        return response()->json(['success', 'refresh', []],200);
    }

    public function listUpdate(Request $request)
    {
        foreach ($request->positions as $list) {
            if(!empty($list['list_id'])) JobList::find($list['list_id'])->update(['position' => $list['position']]);
        }

        return response()->json();
    }

    public function deleteList(Request $request)
    {
        $job_task_ids = JobTask::where('job_id', $request->job_id)->where('list_id', $request->list_id)->get()->map(function($query){return $query->id;});
        JobTask::where('job_id', $request->job_id)->where('list_id', $request->list_id)->delete();
        JobTaskField::where('job_id', $request->job_id)->where('list_id', $request->list_id)->delete();
        JobTaskAssociate::whereIn('task_id', $job_task_ids)->delete();
        JobTaskTag::whereIn('task_id', $job_task_ids)->delete();
        JobList::find($request->list_id)->delete();

        return response()->json('',200);
    }
    ############

    ##Tarefas####
    public function newTask(Request $request)
    {
        // \Log::info($request->all());
        $rules = [
            'task_title' => 'required|string',
            'delivery_date_task' => 'required|string',
        ];

        $customMessages = [
            'task_title.required' => 'O campo Titulo é obrigatório!',
            'delivery_date_task.required' => 'O campo Data é obrigatório!',
        ];

        foreach(($request->custom_field_required ?? []) as $custom_field_required){
            switch($custom_field_required['field_type']){
                case 'number':
                case 'text':
                case 'time':
                case 'email':
                case 'date':
                case 'moeda':
                case 'date_time':
                case 'select':
                case 'attachment':
                    if(empty($request->custom_fields[$custom_field_required['label_field_id']])){
                        $rules[$custom_field_required['label_field_id']] = 'required|string';
                        $customMessages[$custom_field_required['label_field_id'].'.required'] = 'O campo '.$custom_field_required['label_field'].' é obrigatório!';
                    }
                break;
                case 'checkbox':
                case 'radio':
                    if(count($request->custom_fields[$custom_field_required['label_field_id']] ?? []) == 0){
                        $rules[$custom_field_required['label_field_id']] = 'required';
                        $customMessages[$custom_field_required['label_field_id'].'.required'] = 'Um dos campos de seleção "'.$custom_field_required['label_field'].'" precisa estar marcado!';
                    }
                break;
            }
        }

        $this->validate($request, $rules, $customMessages);

        $jobList = JobList::where('job_id', $request->job_id)->get();

        $job_task_create['job_id'] = $request->job_id;
        $job_task_create['list_id'] = $jobList[0]->id;
        $job_task_create['position'] = JobTask::where('job_id', $request->job_id)->where('list_id', $jobList[0]->id)->get()->count()+1;
        $job_task_create['task_title'] = $request->task_title;
        $job_task_create['delivery_date_task'] = date('Y-m-d', strtotime(str_replace('/','-', $request->delivery_date_task)));
        $job_task_create['total_time_task'] = '000:00:00';
        $job_task = JobTask::create($job_task_create);

        foreach (($request->custom_fields ?? []) as $key => $value) {
            if(!empty($value)){
                $job_task_field_create['job_id'] = $request->job_id;
                $job_task_field_create['list_id'] = $jobList[0]->id;
                $job_task_field_create['task_id'] = $job_task->id;
                $job_task_field_create['field_label_id'] = $key;
                if(!is_array($value)) {
                    $job_task_field_create['field_value'] = $value;
                    $job_task_field_create['field_array'] = null;
                }
                if(is_array($value)){
                    $job_task_field_create['field_value'] = null;
                    $job_task_field_create['field_array'] = $value;
                }
                JobTaskField::create($job_task_field_create);
            }
        }

        foreach (($request->user_assoc_id ?? []) as $key => $value) {
            if(!empty($value)){
                $job_task_associate_create['task_id'] = $job_task->id;
                $job_task_associate_create['user_id'] = $value;
                $job_task_associate_create['total_time_task'] = '000:00:00';
                JobTaskAssociate::create($job_task_associate_create);
            }
        }

        foreach (($request->tag_id ?? []) as $key => $value) {
            if(!empty($value)){
                $job_task_tag_create['task_id'] = $job_task->id;
                $job_task_tag_create['tag_id'] = $value;
                JobTaskTag::create($job_task_tag_create);
            }
        }

        return response()->json(['success', 'refresh', ['icon' => 'success', 'msg' => 'Tarefa criada com suceso!']],200);
    }

    public function taskUpdate(Request $request)
    {
        if(isset($request->task_update_position)){
            foreach ($request->positions as $task) {
                if(!empty($task['task_id'])){
                    JobTask::find($task['task_id'])->update(['position' => $task['position'], 'list_id' => $task['list_id']]);
                    JobTaskField::where('task_id', $task['task_id'])->update(['list_id' => $task['list_id']]);
                }
            }
        }

        if(isset($request->job_task_update)){
            JobTask::find($request->task_id)->update([$request->field_update['field_name'] => $request->field_update['field_value']]);
        }

        if(isset($request->job_task_update_tu)){
            if(!empty($request->tag_id)) JobTaskTag::where('task_id', $request->task_id)->delete();
            if(!empty($request->user_assoc_id)) JobTaskAssociate::where('task_id', $request->task_id)->delete();

            foreach (($request->user_assoc_id ?? []) as $key => $value) {
                if(!empty($value)){
                    $job_task_associate_create['task_id'] = $request->task_id;
                    $job_task_associate_create['user_id'] = $value;
                    $job_task_associate_create['total_time_task'] = '000:00:00';
                    JobTaskAssociate::create($job_task_associate_create);
                }
            }

            foreach (($request->tag_id ?? []) as $key => $value) {
                if(!empty($value)){
                    $job_task_tag_create['task_id'] = $request->task_id;
                    $job_task_tag_create['tag_id'] = $value;
                    JobTaskTag::create($job_task_tag_create);
                }
            }
        }

        if(isset($request->task_update_field)){
            JobTaskField::where('task_id',$request->task_id)->where('field_label_id', $request->field_update['field_name'])->update(['field_value' => ($request->field_update['field_value'] ?? null), 'field_array' => ($request->field_update['field_array'] ?? null)]);
            if(JobTaskField::where('task_id',$request->task_id)->where('field_label_id', $request->field_update['field_name'])->get()->count() == 0){
                $job_task_field['job_id'] = $request->job_id;
                $job_task_field['list_id'] = $request->list_id;
                $job_task_field['task_id'] = $request->task_id;
                $job_task_field['field_label_id'] = $request->field_update['field_name'];
                $job_task_field['field_value'] = $request->field_update['field_value'] ?? null;
                $job_task_field['field_array'] = $request->field_update['field_array'] ?? null;
                JobTaskField::create($job_task_field);
            }
        }

        return response()->json();
    }

    public function deleteTask(Request $request)
    {
        JobTask::find($request->task_id)->delete();
        JobTaskAssociate::where('task_id', $request->task_id)->delete();
        JobTaskTag::where('task_id', $request->task_id)->delete();
        JobTaskField::where('task_id', $request->task_id)->delete();

        return response()->json('',200);
    }
    ############

    ##Etiquetas####
    public function newTag(Request $request)
    {
        $create_tag['job_id'] = $request->job_id;
        $create_tag['tag_name'] = $request->tag_name;
        $create_tag['tag_color'] = $request->tag_color;

        if(isset($request->tag_id)){
            $tag = JobTag::find($request->tag_id)->update($create_tag);
        }else{
            $tag = JobTag::create($create_tag);
        }

        return response()->json($tag,200);
    }

    public function deleteTag(Request $request)
    {
        $tag = JobTag::find($request->tag_id)->delete();

        return response()->json('',200);
    }
    ############

    ##Fields######
    public function taskField(Request $request)
    {
        if(empty($request->label_field_id)){
            $custom_field['label_field_id'] = \Str::slug($request->label_field, '_');
            $custom_label_field_id = CustomField::where('job_id', $request->job_id)->where('label_field_id', 'LIKE', '%'.$custom_field['label_field_id'].'%')->get();
            if($custom_label_field_id->count() > 0){
                $custom_field['label_field_id'] = \Str::slug($request->label_field.' '.$custom_label_field_id->count(), '_');
            }
            $custom_field['position'] = CustomField::where('job_id', $request->job_id)->where('list_id', $request->list_id)->get()->count()+1;
        }
        $custom_field['job_id'] = $request->job_id;
        $custom_field['list_id'] = !empty($request->list_id) ? $request->list_id : null;
        $custom_field['field_type'] = $request->field_type;
        switch($request->field_type){
            case 'date':
                $custom_field['field_mask'] = $request->date_mask;
            break;
            case 'date_time':
                $custom_field['field_mask'] = $request->date_time_mask;
            break;
            case 'moeda':
                $custom_field['field_mask'] = $request->moeda_mask;
            break;
            default:
                $custom_field['field_mask'] = null;
            break;
        }
        // $custom_field['field_mask'] = '';
        $custom_field['list_name'] = $request->list_name;
        $custom_field['label_field'] = $request->label_field;
        $custom_field['field_edit_lists'] = isset($request->field_edit_lists) ? 1 : 0;
        $custom_field['field_required'] = isset($request->field_required) ? 1 : 0;

        if(!empty($request->label_field_id)){
            CustomField::where('job_id', $request->job_id)->where(function($query) use($request){
                if($request->list_id) return $query->where('list_id', $request->list_id);
            })->where('label_field_id', $request->label_field_id)->update($custom_field);
            $field = CustomField::where('job_id', $request->job_id)->where(function($query) use($request){
                if($request->list_id) return $query->where('list_id', $request->list_id);
            })->where('label_field_id', $request->label_field_id)->first();
            $msg = 'Campo atualizado!';
        }else{
            $field = CustomField::create($custom_field);
            $msg = 'Campo criado!';
        }

        $campo_html =  view('components.customFieldsTask', get_defined_vars())->render();

        return response()->json(['success', 'local', ['msg' => $msg], 'custom_field' => ['field_id' => $field->label_field_id, 'html' => $campo_html]]);
    }
    public function taskFieldUpdate(Request $request)
    {
        foreach ($request->positions as $field) {
            CustomField::where('job_id', $field['job_id'])->where(function($query) use($field){
                if($field['list_id']) return $query->where('list_id', $field['list_id']);
            })->where('label_field_id', $field['label_field_id'])->update(['position' => $field['position']]);
        }

        return response()->json();
    }
    public function deleteField(Request $request)
    {
        CustomField::where('job_id', $request->job_id)->where(function($query) use($request){
            if($request->list_id) return $query->where('list_id', $request->list_id);
        })->where('label_field_id', $request->label_field_id)->delete();

        ##########################################################################################################
        ##########NÂO ESQUECER DE COLOCAR FUNÇÂO PAAR APAAGRA DADOS DO FILD NAS TASK##############################
        ##########################################################################################################

        return response()->json();
    }
    ##############

    // Usuarios associados
    public function associateuserJob(Request $request)
    {
        foreach(($request->new_job_access ?? []) as $key => $value){
            if($value !== '100'){
                JobUser::create([
                    'job_id' => $request->job_id,
                    'user_id' => $key,
                    'access' => $value,
                ]);
            }
        }
        foreach(($request->job_access ?? []) as $key => $value){
            if($value == '100'){
                JobUser::where('job_id',$request->job_id)->where('user_id', $key)->delete();
            }elseif($value == '200'){
                JobUser::where('job_id',$request->job_id)->where('user_id', $key)->delete();
                return response()->json(['success', 'refresh', []],200);
            }else{
                JobUser::where('job_id',$request->job_id)->where('user_id', $key)->update([
                    'access' => $value,
                ]);
            }
        }

        return response()->json(['success', 'local', []],200);
    }

    public function disassociateuserJob(Request $request)
    {
        JobUser::where('job_id',$request->job_id)->where('user_id', auth()->user()->id)->delete();
        return response()->json('',200);
    }

    public function searchUserJob(Request $request)
    {
        $user = User::with('jobs')->where('email', $request->user_email)->first();
        if(empty($user)) return response()->json('Usuario não econtrado',412);
        if($user->jobs->where('job_id', $request->job_id)->count() > 0) return response()->json('Usuario já associado',412);

        $html = '
            <div class="row">
                <div class="form-group col-4 col-sm-3 text-center">
                    <img style="border-radius: 50%;width: 40%;" src="https://ui-avatars.com/api/?name='.str_replace(' ', '+', $user->name).'&color=ffffff&background=5866db" alt="">
                </div>
                <div class="form-group col-6 col-sm-5">
                    <b>'.$user->name.'</b>
                </div>
                <div class="form-group col-12 col-sm-4">
                    <select name="new_job_access['.$user->id.']" class="form-control form-control-sm">
                        <option value="2">Gerente</option>
                        <option value="3">Colaborador</option>
                        <option value="4">Observador</option>
                        <option value="100">Remover</option>
                    </select>
                </div>
            </div>
        ';

        return response()->json($html);
    }
}
