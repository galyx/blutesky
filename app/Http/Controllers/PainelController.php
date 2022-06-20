<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobTag;
use App\Models\JobList;
use App\Models\JobTask;
use App\Models\JobUser;
use App\Models\CustomField;
use Illuminate\Http\Request;

class PainelController extends Controller
{
    public function dashboard()
    {
        $user_create_jobs = JobUser::where('user_id', auth()->user()->id)->where('access', 1)->get();
        $user_jobs_associates = JobUser::where('user_id', auth()->user()->id)->whereIn('access', [2,3,4])->get();

        return view('dashboard', get_defined_vars());
    }

    public function indexJob($job, $list = null, $task = null)
    {
        $job_select = Job::with('jobUsers', 'jobLists')->where('id',$job)->whereHas('jobUsers', function($query) {
            return $query->where('user_id', auth()->user()->id)->where('status', 1);
        })->first();
        if(empty($job_select)) return redirect()->route('dash');
        $job_name = $job_select->job_name;
        $tags = JobTag::where('job_id', $job)->get();

        return view('job.indexJobRelatorio', get_defined_vars());
        // return view('job.indexJobKanban', get_defined_vars());
    }

    //---------------------------
    public function searchController(Request $request)
    {
        switch($request->request_swicth){
            case 'associates-modal':
                $job = Job::with('jobUsers.user')->find($request->job_id);
                $html = view('components.modalAssociates', get_defined_vars())->render();
                return response()->json(['html' => $html, 'title' => 'Usuarios Associados', 'url' => route('associateuserJob')],200);
            break;
            case 'create-list-user-modal':
                $job = Job::with('jobUsers.user')->find($request->job_id);
                $html = view('components.modalCreateListaUser', get_defined_vars())->render();
                return response()->json(['html' => $html, 'title' => 'Nova Lista', 'url' => route('newList')],200);
            break;
            case 'edit-list-user-modal':
                $job = Job::with('jobUsers.user')->find($request->job_id);
                $list = JobList::find($request->request_data['list_id']);
                $html = view('components.modalCreateListaUser', get_defined_vars())->render();
                return response()->json(['html' => $html, 'title' => 'Editar Lista '.$list->list_name, 'url' => route('newList')],200);
            break;
            case 'create-task-modal':
                $field_value = null;
                $job = Job::with('jobUsers.user')->find($request->job_id);
                $custom_fields = CustomField::where('job_id', $request->job_id)->whereNull('list_id')->orderBy('position')->get();
                $html = view('components.modalNewTask', get_defined_vars())->render();
                return response()->json(['html' => $html, 'title' => 'Nova Tarefa', 'url' => route('newTask')],200);
            break;
            case 'request-task-modal':
                $field_value = null;
                $job = Job::with('jobUsers.user')->find($request->job_id);
                $job_list = JobList::find($request->list_id);
                $job_task = JobTask::with('jobTaskAssociate.user', 'jobTaskFields')->find($request->task_id);
                $custom_fields = CustomField::with('jobList')->where('job_id', $request->job_id)->orderBy('position')->get();
                $html = view('components.modalTask', get_defined_vars())->render();
                return response()->json(['html' => $html],200);
            break;
        }

        return response()->json('erro',412);
    }
}
