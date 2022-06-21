$(document).ready(function(){
    $('#select_color').colpick({
        onChange: (response) => {
            var colorHex = $.colpick.hsbToHex(response);
            $('#select_color').css('background-color', '#'+colorHex).attr('data-colorHex', '#'+colorHex);
        },
        onSubmit: (response) => {
            $('#select_color').colpickHide();
        }
    });
    $('#select_color_edit').colpick({
        onChange: (response) => {
            var colorHex = $.colpick.hsbToHex(response);
            $('#select_color_edit').css('background-color', '#'+colorHex).attr('data-colorHex', '#'+colorHex);
        },
        onSubmit: (response) => {
            $('#select_color_edit').colpickHide();
        }
    });
    $(document).on('click', '[data-toggle="collapse"]', function(){
        if($(this).attr('aria-expanded') == 'true'){
            $(this).find('.btn-info-rel').html('<i class="ri-subtract-fill"></i>');
        }else{
            $(this).find('.btn-info-rel').html('<i class="ri-add-fill"></i>');
        }
    });

    $(document).on('click', '[data-target="#modalUserAssoc"]', function(){
        $('#modalUserAssoc').find('.user-assoc-select').empty();
        var users = $(this).data('users');

        for(i in users){
            $('#modalUserAssoc').find('.user-assoc-select').append(`
                <div class="row rounded border py-2 mb-2 user-select" style="cursor: pointer;" id="user_assoc_id-${users[i].id}" data-dados='${JSON.stringify(users[i])}'>
                    <div class="col-4 text-center">
                        <img style="border-radius: 50%;width: 40%;" src="https://ui-avatars.com/api/?name=${users[i].name.replace(' ', '+')}&color=ffffff&background=5866db" alt="">
                    </div>
                    <div class="col-8">
                        ${users[i].name}
                    </div>
                </div>
            `);
        }
    });

    // Adicionando itens a lista conforme a seleção
    $(document).on('keyup', '.add-list-name', function(e){
        e.preventDefault();
        if(e.keyCode == 13){
            if($(this).val().includes(';')){
                var separator = $(this).val().split(';');
                for(var i = 0; separator.length>i; i++){
                    if(separator[i]){
                        $('[aria-labelledby="dropdownlist"]').append('<div class="px-2 py-1" style="position: relative;"><input type="hidden" name="list_name[]" value="'+separator[i]+'">'+separator[i]+' <button type="button" class="btn close btn-remove-list" style="position: absolute; right: 8px; top: 1px">x</button></div>');
                    }
                }
            }else{
                $('[aria-labelledby="dropdownlist"]').append('<div class="px-2 py-1" style="position: relative;"><input type="hidden" name="list_name[]" value="'+$(this).val()+'">'+$(this).val()+' <button type="button" class="btn close btn-remove-list" style="position: absolute; right: 8px; top: 1px">x</button></div>');
            }

            $(this).val('');
        }
    });
    $(document).on('click', '.btn-remove-list', function(){
        $(this).parent().remove();
    });

    // Definições docampo de seleção
    $(document).on('change', '[name="field_type"]', function(){
        $('[name="date_mask"], [name="date_time_mask"], [name="moeda_mask"], .add-list').parent().addClass('d-none');
        switch($(this).val()){
            case 'date':
                $('[name="date_mask"]').parent().removeClass('d-none');
            break;
            case 'date_time':
                $('[name="date_time_mask"]').parent().removeClass('d-none');
            break;
            case 'moeda':
                $('[name="moeda_mask"]').parent().removeClass('d-none');
            break;
            case 'checkbox':
            case 'select':
            case 'radio':
                $('.add-list').parent().removeClass('d-none');
            break;
        }
    });

    // Adicionando usuario ao card
    $(document).on('click', '.user-select', function(){
        var data = $(this).data('dados');
        var div_user = $('.div-user-assoc').find('.user-assoc');
        if(div_user.find(`#user_assoc_id-${data.id}`).length == 0){
            div_user.append(`
                <div class="mb-1 mx-1" style="border-radius: 50%;width: 10%;" id="user_assoc_id-${data.id}">
                    <input type="hidden" name="user_assoc_id[]" value="${data.id}">
                    <img style="border-radius: 50%;width: 100%;" src="https://ui-avatars.com/api/?name=${data.name.replace(' ', '+')}&color=ffffff&background=5866db" alt="">
                </div>
            `);
        }else{
            div_user.find(`#user_assoc_id-${data.id}`).remove();
        }
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Adicionando etiqueta ao card
    $(document).on('click', '.tag-select', function(){
        var data = $(this).data('dados');
        var div_tag = $('.div-tags').find('.tags');
        if(div_tag.find(`#tag_id-${data.id}`).length == 0){
            div_tag.append(`
                <div class="col mb-1 px-1" id="tag_id-${data.id}">
                    <input type="hidden" name="tag_id[]" value="${data.id}">
                    <div class="w-100 py-2 px-1 rounded text-truncate" title="${data.tag_name ? data.tag_name : ''}" data-toggle="tooltip" style="background-color: ${data.tag_color}; min-height: 40px;cursor: pointer;">${data.tag_name ? data.tag_name : ''}</div>
                </div>
            `);
        }else{
            div_tag.find(`#tag_id-${data.id}`).remove();
        }
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Criando Tag
    $(document).on('click', '.btn-save-new-tag', function(){
        $(this).prop('disabled', true);
        $('.btn-cancel-new-tag').prop('disabled', true);

        $.ajax({
            url: newTag,
            type: 'POST',
            data: {tag_name: $('.new-tag-name').val(), tag_color: $('#select_color').attr('data-colorHex'), job_id: $('select.select-job').val()},
            success: (data) => {
                console.log(data);
                $('.m-new-tag').addClass('d-none');
                $('.btn-new-tag').removeClass('d-none').prop('disabled', false);
                $('.btn-cancel-new-tag').prop('disabled', false);
                $('.btn-save-new-tag').prop('disabled', false);

                $('.new-tag-name').val('');
                $('#select_color').attr('data-colorHex', '#ffffff').css('background-color', '#ffffff');

                $('.tags-select').append(`
                    <div class="col-12 d-flex mb-1">
                        <div class="w-75 py-2 px-1 rounded text-truncate tag-select" title="${data.tag_name ? data.tag_name : ''}" data-toggle="tooltip" style="background-color: ${data.tag_color}; min-height: 40px;cursor: pointer;" data-dados='${JSON.stringify(data)}'>${data.tag_name}</div>
                        <div class="col">
                            <button type="button" class="btn btn-block border btn-edit-tag-select" data-select="false" data-dados='${JSON.stringify(data)}'><i class="ri-pencil-fill"></i></button>
                        </div>
                    </div>
                `);
            }
        });
    });
    // Atualizando Tag
    $(document).on('click', '.btn-save-edit-tag', function(){
        $(this).prop('disabled', true);
        $('.btn-cancel-new-tag').prop('disabled', true);
        $('.btn-delete-edit-tag').prop('disabled', true);

        $.ajax({
            url: newTag,
            type: 'POST',
            data: {tag_id: $('[data-select="true"]').data('dados').id, tag_name: $('.edit-tag-name').val(), tag_color: $('#select_color_edit').attr('data-colorHex'), job_id: $('select.select-job').val()},
            success: (data) => {
                console.log(data);
                $('.m-edit-tag').addClass('d-none');
                $('.btn-new-tag').removeClass('d-none').prop('disabled', false);
                $('.btn-save-edit-tag').prop('disabled', false);
                $('.btn-cancel-new-tag').prop('disabled', false);
                $('.btn-delete-edit-tag').prop('disabled', false);

                $('.edit-tag-name').val('');
                $('#select_color_edit').attr('data-colorHex', '#ffffff').css('background-color', '#ffffff');

                $('[data-select="true"]').closest('.col-12').remove();
                $('.tags-select').append(`
                    <div class="col-12 d-flex mb-1">
                        <div class="w-75 py-2 px-1 rounded text-truncate tag-select" title="${data.tag_name ? data.tag_name : ''}" data-toggle="tooltip" style="background-color: ${data.tag_color}; min-height: 40px; cursor: pointer;">${data.tag_name}</div>
                        <div class="col">
                            <button type="button" class="btn btn-block border btn-edit-tag-select" data-select="false" data-dados='${JSON.stringify(data)}'><i class="ri-pencil-fill"></i></button>
                        </div>
                    </div>
                `);

                $('.btn-edit-tag-select').attr('data-select', 'false');
            }
        });
    });
    // Apagando Tag
    $(document).on('click', '.btn-delete-edit-tag', function(){
        $(this).prop('disabled', true);
        $('.btn-cancel-new-tag').prop('disabled', true);
        $('.btn-save-edit-tag').prop('disabled', true);

        Swal.fire({
            icon: 'error',
            title: 'Você está preste a apagar essa etiqueta, tem certeza?',
            text: 'Todos registro vinculados a essa etiqueta serão desvinculados permanentemente.',
            showCancelButton: true,
            confirmButtonText: 'Sim Apagar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: deleteTag,
                    type: 'POST',
                    data: {tag_id: $('[data-select="true"]').data('dados').id, job_id: $('select.select-job').val()},
                    success: (data) => {
                        console.log(data);
                        $('.m-edit-tag').addClass('d-none');
                        $('.btn-new-tag').removeClass('d-none').prop('disabled', false);
                        $('.btn-save-edit-tag').prop('disabled', false);
                        $('.btn-cancel-new-tag').prop('disabled', false);
                        $('.btn-delete-edit-tag').prop('disabled', false);

                        $('.edit-tag-name').val('');
                        $('#select_color_edit').attr('data-colorHex', '#ffffff').css('background-color', '#ffffff');

                        $('[data-select="true"]').closest('.col-12').remove();

                        $('.btn-edit-tag-select').attr('data-select', 'false');
                    }
                });
            }
        });
    });

    // Nova Tag
    $(document).on('click', '.btn-new-tag', function(){
        $(this).addClass('d-none');
        $('.m-new-tag').removeClass('d-none');
    });
    // Cancelar tag
    $(document).on('click', '.btn-cancel-new-tag', function(){
        $('.btn-edit-tag-select').attr('data-select', 'false');
        $('.m-new-tag').addClass('d-none');
        $('.m-edit-tag').addClass('d-none');
        $('.btn-new-tag').removeClass('d-none');
        $('.new-tag-name, .edit-tag-name').val('');
        $('#select_color, #select_color_edit').attr('data-colorHex', '#ffffff').css('background-color', '#ffffff');
    });
    // Editando Tag
    $(document).on('click', '.btn-edit-tag-select', function(){
        var data = $(this).data('dados');
        $(this).attr('data-select', 'true');
        $('.m-new-tag').addClass('d-none');
        $('.m-edit-tag').removeClass('d-none');
        $('.btn-new-tag').addClass('d-none');

        console.log(data);

        $('.edit-tag-name').val(data.tag_name);
        $('#select_color_edit').attr('data-colorHex',data.tag_color).css('background-color', data.tag_color);
    });

    // Editando Campo Customizado
    $(document).on('click', '.btn-edit-custom-field', function(){
        $('#modalField').find('.btn-detroy-field-custom').removeClass('d-none');
        $.each($(this).data('dados'), (key, value) => {
            if(key !== 'job_id'){
                $('#formModalField').find(`[name="${key}"]`).val(value);
            }
            if(key == 'field_required') $('#formModalField').find('[name="field_required"]').prop('checked', (value == 1 ? true : false));
            if(key == 'field_edit_lists') $('#formModalField').find('[name="field_edit_lists"]').prop('checked', (value == 1 ? true : false));
            $('#formModalField').find('select').trigger('change');
        });
    });

    // Apagando Campo
    $(document).on('click', '.btn-detroy-field-custom', function(){
        Swal.fire({
            icon: 'error',
            title: 'Você está preste a apagar esse campo, tem certeza?',
            text: 'Será apagado permanentemente.',
            showCancelButton: true,
            confirmButtonText: 'Sim Apagar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
        }).then((result) => {
            if(result.isConfirmed){
                Swal.fire({
                    title: 'Apagando campo, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: '/delete-field',
                    type: 'POST',
                    data: {job_id: $('#formModalField').find('[name="job_id"]').val(), list_id: $('#formModalField').find('[name="list_id"]').val(), label_field_id: $('#formModalField').find('[name="label_field_id"]').val()},
                    success: (data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Campo apagado com sucesso!'
                        }).then(()=>{
                            $('#formModalField').closest('.modal').modal('hide');
                            $('.custom-fields').find('#custom_field_id-'+$('#formModalField').find('[name="label_field_id"]').val()).remove();
                        });
                    }
                });
            }
        });
    });

    // Buscando tarefas
    $(document).on('click', '.item-rel-btn', function(){
        var url_insert = `/job/${$('select.select-job').val()}/${$(this).closest('.rel-btn').data('list_id')}/${$(this).data('task_id')}`;
        window.history.pushState({url: url_insert}, $('title').text(), url_insert);

        Swal.fire({
            title: 'Carregando informações...',
            allowOutsideClick: false,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/search-controller',
            type: 'POST',
            data: {request_swicth: 'request-task-modal', job_id: $('select.select-job').val(), list_id: $(this).closest('.rel-btn').data('list_id'), task_id: $(this).data('task_id')},
            success: (data) => {
                Swal.close();
                $('#modalTask').find('.modal-task').html(data.html);
                $('#modalTask').modal('show');
                $('.selectpicker').selectpicker();
                $('.date-mask-single').daterangepicker(dateMaskSingle);
                fieldMask();
                sortableFieldCustom();

                setTimeout(() => {
                    var modal_body = $('#modalTask').find('.modal-body').height();
                    var task_titleH = $('#modalTask').find('.task_title').innerHeight();
                    var task_titleM = parseFloat($('#modalTask').find('.task_title').css('margin-bottom')) || 0;
                    $('#modalTask').find('.task-card').css('max-height', (modal_body-task_titleH-task_titleM-8)+'px');
                }, 10);
            },
            error: (err) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Houve um erro na consulta, contate o administrador!'
                });
            }
        });
    });

    // Buscanco id da lista para o custom field
    $(document).on('click', '[data-target="#modalField"]', function(){
        $('#formModalField').find('[name="list_id"]').val($(this).closest('form').find('[name="list_id"]').val());
    });

    // Editando dados de titulo da lista
    $(document).on('click', '#form-task-modal .task_title', function(){
        $(this).find('h3').addClass('d-none');
        $(this).find('input').removeClass('d-none').focus();
    });
    $(document).on('blur', '#form-task-modal [name="task_title"]', function(){
        $(this).parent().find('h3').text($(this).val()).removeClass('d-none');
        $(this).addClass('d-none');
    });

    // Sortables das listas
    $('.rel').sortable({
        axis: 'y',
        placeholder: "ui-state-highlight",
        cursor: "move",
        // forcePlaceholderSize: true,
        beforeStop: function( event, ui ) {
            // var tag = $(ui.item[0]);
            var positions = [];
            $('.rel .rel-btn').each(function(index){
                positions.push({
                    list_id: $(this).attr('data-list_id'),
                    position: index+1,
                });
            });

            $.ajax({
                url: listUpdate,
                type: 'POST',
                data: {positions},
                success: (data) => {}
            });
        },
        activate: function( event, ui ) {
            $(".rel").sortable( "refresh" );
            $(".rel").find('.header-rel').addClass('collapsed');
            $(".rel").find('.collapse').removeClass('show');
            $(".rel").find('.btn-info-rel').html('<i class="ri-add-fill"></i>');
        },
        containment: "document",
    }).disableSelection();

    activeSortableRelList();
    // Quando fecha modal
    $('#modalTags').on('hidden.bs.modal', function(){
        $('.btn-edit-tag-select').attr('data-select', 'false');
        $('.m-new-tag').addClass('d-none');
        $('.m-edit-tag').addClass('d-none');
        $('.btn-new-tag').removeClass('d-none');
        $('.new-tag-name, .edit-tag-name').val('');
        $('#select_color, #select_color_edit').attr('data-colorHex', '#ffffff').css('background-color', '#ffffff');
    });
    $('#modalField').on('hidden.bs.modal', function(){
        $('#formModalField').find('[name="date_mask"], [name="date_time_mask"], [name="moeda_mask"], .add-list').parent().addClass('d-none');
        $('#formModalField').find('input[type="text"], select').val('');
        $('#formModalField').find('[aria-labelledby="dropdownlist"]').empty();
        $('#formModalField').find('[name="field_type"]').val('text');
        $('#formModalField').find('[name="list_id"]').val('');
        $('#formModalField').find('[name="label_field_id"]').val('');
        $('#formModalField').find('[name="field_required"]').prop('checked', false);
        $('#formModalField').find('[name="field_edit_lists"]').prop('checked', true);
        $('#modalField').find('.btn-detroy-field-custom').addClass('d-none');
    });
    $('#modalTask').on('hidden.bs.modal', function(){
        var url_insert = `/job/${$('select.select-job').val()}`;
        window.history.pushState({url: url_insert}, $('title').text(), url_insert);
        // window.history.back();
        // window.history.go(0);
    });
});

function activeSortableRelList(){
    $('.rel-list').each(function(){
        var list_id = $(this).closest('.rel-btn').data('list_id');
        $('.rel-list-'+list_id).sortable({
            axis: 'y',
            cursor: "move",
            placeholder: "ui-state-highlight",
            connectWith: '.rel-list',
            beforeStop: function( event, ui ) {
                // $(".rel-list").sortable( "refresh" );
                var tag = $(ui.item[0]);

                var positions = [];
                tag.closest('.rel-list').find('.item-rel-btn').each(function(index){
                    positions.push({
                        task_id: $(this).data('task_id'),
                        list_id: $(this).closest('.rel-btn').data('list_id'),
                        position: index+1,
                    });
                });

                $.ajax({
                    url: taskUpdate,
                    type: 'POST',
                    data: {positions, task_update_position: true},
                    success: (data) => {}
                });
            },
            // containment: "document",
        }).disableSelection();
    });
}