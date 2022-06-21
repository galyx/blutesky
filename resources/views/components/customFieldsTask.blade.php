<div class="form-group col-12 py-2 mouse-over-field bg-white custom-field" data-dados="{{$field->toJson()}}" id="custom_field_id-{{$field->label_field_id}}">
    @if ($field->field_required)
        <input type="hidden" class="custom_field_required" data-json="{{$field->toJson()}}" name="custom_field_required[{{$field->label_field_id}}][label_field_id]" value="{{$field->label_field_id}}">
        <input type="hidden" name="custom_field_required[{{$field->label_field_id}}][label_field]" value="{{$field->label_field}}">
        <input type="hidden" name="custom_field_required[{{$field->label_field_id}}][field_type]" value="{{$field->field_type}}">
    @endif
    <button type="button" class="btn btn-sm border btn-edit-custom-field" data-dados="{{$field->toJson()}}" data-toggle="modal" data-target="#modalField"><i class="ri-edit-2-line"></i></button>
    <label for="{{$field->label_field_id}}">{{$field->label_field}}</label>
    @switch($field->field_type)
        @case('number')
        @case('text')
        @case('time')
        @case('email')
            <input type="{{$field->field_type}}" value="{{$field_value->field_value ?? ''}}" class="form-control form-control-sm" name="custom_fields[{{$field->label_field_id}}]">
            @break
        {{-- @case('attachment')
            <br>
            <input type="file" name="custom_fields[{{$field->label_field_id}}]" class="@if($field->field_required) required @endif">
            @break --}}
        @case('checkbox')
            <div class="px-2 py-1 border rounded">
                @foreach ($field->list_name as $key => $list_name)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="{{\Str::slug($list_name, '_')}}-{{$key}}" name="custom_fields[{{$field->label_field_id}}][]" {{in_array($list_name,($field_value->field_array ?? [])) ? 'checked' : ''}} value="{{$list_name}}">
                        <label for="{{\Str::slug($list_name, '_')}}-{{$key}}">{{$list_name}}</label>
                    </div>
                @endforeach
            </div>
            @break
        @case('radio')
            <div class="px-2 py-1 border rounded">
                @foreach ($field->list_name as $key => $list_name)
                    <div class="form-check">
                        <input type="radio" class="form-check-input" id="{{\Str::slug($list_name, '_')}}-{{$key}}" name="custom_fields[{{$field->label_field_id}}]" {{$list_name == ($field_value->field_value ?? null) ? 'checked' : ''}} value="{{$list_name}}">
                        <label for="{{\Str::slug($list_name, '_')}}-{{$key}}">{{$list_name}}</label>
                    </div>
                @endforeach
            </div>
            @break
        @case('date')
        @case('moeda')
        @case('date_time')
            <input type="text" class="form-control form-control-sm field_mask" value="{{$field_value->field_value ?? ''}}" data-field_type="{{$field->field_type}}" data-field_mask="{{$field->field_mask}}" name="custom_fields[{{$field->label_field_id}}]">
            @break
        @case('select')
            <select class="form-control form-control-sm" name="custom_fields[{{$field->label_field_id}}]">
                <option value="">Escolha um Opção</option>
                {!!collect($field->list_name)->map(function($query) use($field_value){return '<option value="'.$query.'" '.(($field_value->field_value ?? null) == $query ? 'selected' : '').'>'.$query.'</option>';})->join('')!!}
            </select>
            @break
    @endswitch
</div>