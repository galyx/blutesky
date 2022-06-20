<input type="hidden" name="job_id" value="{{$job->id}}">
@if (jobsUserAccess($job->id))
    <div class="mb-5">
        <label for="">Informe o email do usuario</label>
        <div class="input-group input-group-sm">
            <input type="email" class="form-control user-mail">
            <div class="input-group-append">
                <button type="button" class="btn btn-primary btn-busca-user">Buscar</button>
            </div>
        </div>
    </div>
@endif
<div class="user-associates">
    @foreach ($job->jobUsers as $job_user)
        @php
            $isValidAccess = false;
            if(jobsUserAccess($job->id)) $isValidAccess = true;
            if(auth()->user()->id == $job_user->user_id) $isValidAccess = true;
        @endphp
        <div class="row">
            <div class="form-group col-4 col-sm-3 text-center">
                <img style="border-radius: 50%;width: 40%;" src="https://ui-avatars.com/api/?name={{str_replace(' ', '+', $job_user->user->name)}}&color=ffffff&background=5866db" alt="">
            </div>
            <div class="form-group col-6 col-sm-5">
                <b>{{$job_user->user->name}}</b>
            </div>
            <div class="form-group col-12 col-sm-4">
                @if(auth()->user()->id == $job_user->user_id) 
                    <button type="button" class="btn btn-sm btn-block btn-danger btn-job-leave">Sair</button>
                @else
                    <select name="job_access[{{$job_user->user_id}}]" class="form-control form-control-sm" @if(!$isValidAccess) disabled @endif @if($job_user->access == 1) disabled @endif>
                        <option value="1" @if($job_user->access == '1') selected @endif @if(!in_array(auth()->user()->jobs->where('job_id', $job->id)->first()->access, [1])) disabled @endif>Adiministrador</option>
                        <option value="2" @if($job_user->access == '2') selected @endif @if(!jobsUserAccess($job->id)) disabled @endif>Gerente</option>
                        <option value="3" @if($job_user->access == '3') selected @endif @if(!jobsUserAccess($job->id)) disabled @endif>Colaborador</option>
                        <option value="4" @if($job_user->access == '4') selected @endif @if(!jobsUserAccess($job->id)) disabled @endif>Observador</option>
                        <option value="100">Remover</option>
                    </select>
                @endif
            </div>
        </div>
    @endforeach
</div>