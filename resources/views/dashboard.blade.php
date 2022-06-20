@extends('layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="row pt-2">
            <div class="col">
                <button type="button" class="btn btn-primary" id="newJob">Criar Novo Trabalho <i class="ri-add-circle-fill"></i></button>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 d-flex">
                <div class="w-100 border border-white"></div>
            </div>
            <div class="col-12"><h4 class="text-white">Meus Trabalhos</h4></div>

            @foreach ($user_create_jobs as $job)
                <div class="col-12 col-sm-4 col-md-3 my-2" id="job-{{$job->job->id}}">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{route('job', $job->job->id)}}" class="btn btn-block btn-job-link"><h3>{{$job->job->job_name}}</h3></a>

                            <div class="btn-group btn-block">
                                <button type="button" class="btn btn-edit-job" data-url="{{route('editJob')}}" data-id="{{$job->job->id}}" data-name="{{$job->job->job_name}}"><i class="ri-edit-2-line"></i></button>
                                <button type="button" class="btn btn-destroy" data-url="{{route('deleteJob')}}" data-info="{{json_encode(['id' => $job->job->id])}}"><i class="ri-delete-bin-5-line"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-4">
            <div class="col-12 d-flex">
                <div class="w-100 border border-white"></div>
            </div>
            <div class="col-12"><h4 class="text-white">Trabalhos Associados</h4></div>

            @foreach ($user_jobs_associates as $job)
                <div class="col-12 col-sm-4 col-md-3 my-2" id="job-{{$job->job->id}}">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{route('job', $job->job->id)}}" class="btn btn-block btn-job-link"><h3>{{$job->job->job_name}}</h3></a>

                            {{-- <div class="btn-group btn-block">
                                <button type="button" class="btn btn-edit-job" data-url="{{route('editJob')}}" data-id="{{$job->job->id}}" data-name="{{$job->job->job_name}}"><i class="ri-edit-2-line"></i></button>
                                <button type="button" class="btn btn-destroy" data-url="{{route('deleteJob')}}" data-info="{{json_encode(['id' => $job->job->id])}}"><i class="ri-delete-bin-5-line"></i></button>
                            </div> --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection