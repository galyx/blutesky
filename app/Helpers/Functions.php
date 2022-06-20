<?php

use App\Models\JobUser;

if(!function_exists('jobsUser')){
    function jobsUser(){
        return JobUser::where('user_id', auth()->user()->id)->get();
    }
}

if(!function_exists('jobsUserAccess')){
    function jobsUserAccess($job_id){
        return (in_array(auth()->user()->jobs->where('job_id', $job_id)->first()->access, [1,2]));
    }
}