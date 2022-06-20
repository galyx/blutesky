<input type="hidden" name="job_id" value="{{$job->id}}">
@if (!empty($list))
    <input type="hidden" name="list_id" value="{{$list->id}}">
@endif
<div class="row">
    <div class="col-12 form-group">
        <label>Nome da Lista</label>
        <input type="text" name="new_list_name" class="form-control form-control-sm" value="{{$list->list_name ?? ''}}">
    </div>
    <div class="col-12 form-group">
        <label>Quem pode ver a Lista</label>
        <select name="visibility[]" class="selectpicker form-control form-control-sm" data-live-search="true" data-size="5" multiple data-selected-text-format="count" data-actions-box="true">
            @foreach ($job->jobUsers as $user)
                <option readonly value="{{$user->user_id}}" @if($user->access == 1) selected @endif @if(in_array($user->user_id, ($list->visibility ?? []))) selected @endif>{{$user->user->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 form-group">
        <label>Quem pode acessar a Lista</label>
        <select name="access[]" class="selectpicker form-control form-control-sm" data-live-search="true" data-size="5" multiple data-selected-text-format="count" data-actions-box="true">
            @foreach ($job->jobUsers as $user)
                <option value="{{$user->user_id}}" @if($user->access == 1) selected @endif @if(in_array($user->user_id, ($list->access ?? []))) selected @endif>{{$user->user->name}}</option>
            @endforeach
        </select>
    </div>
</div>