$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('hidden.bs.modal', '.modal', () => $('.modal:visible').length && $(document.body).addClass('modal-open'));

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('.date-mask').daterangepicker({
        singleDatePicker: false,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY',
            daysOfWeek: ['dom','seg','ter','qua','qui','sex','sab'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','outubro','Novembro','Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    });
    $('.date-mask-single').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY',
            daysOfWeek: ['dom','seg','ter','qua','qui','sex','sab'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','outubro','Novembro','Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    });

    $('#modalGeral').on('hidden.bs.modal', function(){
        $(this).find('.modal-dialog').removeClass('modal-sm modal-lg modal-xl');
        $(this).find('.modal-title').empty();
        $(this).find('.modal-body').find('form').empty();
        $(this).find('#formModalGeral').attr('action', '');
    });

    $(function(){
        var body_height = $(window).height();
        var header_height = $('header').innerHeight();
        var footer_height = $('footer').first().innerHeight();

        $('main').css('min-height', (body_height - (header_height + footer_height)));
    });

    $(document).on('change', 'select.select-job', function(){
        window.location.href = $(this).find('option:selected').data('url');
    });

    $(document).on('click', '.btn-destroy', function(){
        Swal.fire({
            icon: 'error',
            title: 'Você está preste a apagar esse regitro, tem certeza?',
            text: 'Todos registro vinculados a esse serão apagados permanentemente.',
            showCancelButton: true,
            confirmButtonText: 'Sim Apagar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
        }).then((result) => {
            if(result.isConfirmed){
                Swal.fire({
                    title: 'Apagando registro, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: $(this).data('url'),
                    type: 'POST',
                    data: $(this).data('info'),
                    success: (data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registro apagado com sucesso!'
                        }).then(()=>{
                            window.location.reload();
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-save', function(){
        Swal.fire({
            title: 'Salvando informações, aguarde...',
            allowOutsideClick: false,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var form = $(this).closest('form');
        if(form.length == 0) form = $($(this).data('target'));
        form.find('.is-valid').removeClass('is-valid');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: new FormData(form[0]),
            cache: false,
            contentType: false,
            processData: false,
            success: (data) => {
                Swal.close();

                if(data[0] == 'success') {
                    switch(data[1]){
                        case 'local':
                            Swal.fire({
                                icon: typeof(data[2].icon) !== "undefined" ? data[2].icon : 'success',
                                title: typeof(data[2].msg) !== "undefined" ? data[2].msg :'Dados atualizados com sucesso!'
                            }).then(()=>{
                                $(this).closest('.modal').modal('hide');
                            });
                        break;
                        case 'refresh':
                            Swal.fire({
                                icon: typeof(data[2].icon) !== "undefined" ? data[2].icon : 'success',
                                title: typeof(data[2].msg) !== "undefined" ? data[2].msg :'Dados atualizados com sucesso!'
                            }).then(()=>{
                                window.location.reload();
                            });
                        break;
                        case 'redirect':
                            Swal.fire({
                                icon: 'success',
                                title: 'Dados atualizados com sucesso!'
                            });

                            setTimeout(() => {window.location.href = data[2]}, 2000);
                        break;
                    }

                    if(data.custom_field){
                        if($('.custom-fields').find('#custom_field_id-'+data.custom_field.field_id).length > 0){
                            $('.custom-fields').find('#custom_field_id-'+data.custom_field.field_id).replaceWith(data.custom_field.html);
                        }else{
                            $('.custom-fields').append(data.custom_field.html);
                        }
                        fieldMask();
                        sortableFieldCustom();
                    }
                }
            },
            error: (err) => {
                Swal.close();

                var errors = err.responseJSON.errors;

                if (errors) {
                    // console.log(errors);
                    var first_focus = true;
                    $.each(errors, (key, value) => {
                        form.find('[name="' + key + '"]').addClass('is-invalid').parent().append('<span class="invalid-feedback">' + value[0] + '</span>');
                        form.find('[name="custom_fields[' + key + ']"]').addClass('is-invalid').parent().append('<span class="invalid-feedback">' + value[0] + '</span>');
                        form.find('#custom_field_id-'+key).find('[type="checkbox"], [type="radio"]').parent().parent().append('<span class="invalid-feedback" style="display: block;">' + value[0] + '</span>');
                        if(first_focus){
                            form.find('[name="' + key + '"]').focus();
                            form.find('[name="custom_fields[' + key + ']"]').focus();
                            first_focus = false;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Houve um erro na gravação, contate o administrador!'
                    });
                }
            }
        });
    });

    $(document).on('keyup blur', '.is-invalid, .is-valid', function(){
        var form = $(this).parent();
        if($(this).val().length > 0){
            form.find('.is-invalid').removeClass('is-invalid').addClass('is-valid');
            form.find('.invalid-feedback').remove();
        }else{
            form.find('.is-valid').removeClass('is-valid').addClass('is-invalid');
        }
    });

    // Seleção Geral de dados
    $(document).on('click', '[data-toogle-scjt="modal"]', function(){
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
            data: {request_swicth: $(this).data('scjt'), job_id: $('select.select-job').val(), request_data: ($(this).data('request_data') || null)},
            success: (data) => {
                Swal.close();
                $('#formModalGeral').attr('action', data.url);
                $('#modalGeralLabel').html(data.title);
                $('#modalGeral').find('.modal-body').find('form').html(data.html);
                $('#modalGeral').modal('show');
                $('.selectpicker').selectpicker();
                $('.date-mask-single').daterangepicker(dateMaskSingle);
                fieldMask();
                sortableFieldCustom();
            },
            error: (err) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Houve um erro na consulta, contate o administrador!'
                });
            }
        });
    });

    // Buscamos o usuario e adicionamos ao modal
    $(document).on('click', '.btn-busca-user', function(){
        Swal.fire({
            title: 'Carregando informações...',
            allowOutsideClick: false,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/search-user-job',
            type: 'POST',
            data: {user_email: $('.user-mail').val(), job_id: $('select.select-job').val()},
            success: (data) => {
                Swal.close();
                $('#modalGeral').find('.user-associates').append(data);
            },
            error: (err) => {
                Swal.fire({
                    icon: 'error',
                    title: err.responseJSON
                });
            }
        });
    });
    // Caso o usuario queira sair do trabalho
    $(document).on('click', '.btn-job-leave', function(){
        Swal.fire({
            icon: 'warning',
            title: 'Você está preste a sair desse trabalho, tem certeza?',
            text: 'Ao Sair não tem mais como voltar, somente por convite.',
            showCancelButton: true,
            confirmButtonText: 'Sim, Sair',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
        }).then((result) => {
            if(result.isConfirmed){
                Swal.fire({
                    title: 'Saindo do trabalho, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: '/disassociate-user-job',
                    type: 'POST',
                    data: {job_id: $('select.select-job').val()},
                    success: (data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Voce foi removido de todos os registro da pagina.'
                        }).then(()=>{
                            window.location.reload();
                        });
                    }
                });
            }
        });
    });
    //Novo Trabalho
    $(document).on('click', '#newJob', function(){
        Swal.fire({
            title: 'De um nome ao seu trabalho!',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Criar Trabalho',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
        }).then((result) => {
            if(result.isConfirmed){
                // console.log(result);
                Swal.fire({
                    title: 'Criando Trabalho, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: `/new-job`,
                    type: 'POST',
                    data: {new_job_name: result.value},
                    success: (data) => {
                        // console.log(data);
                        Swal.fire({
                            icon: 'success',
                            title: 'Trabalho criado com sucesso!'
                        }).then(()=>{
                            // window.location.reload();
                            window.location.href = data.url_redirect;
                        });
                    }
                });
            }
        });
    });
    //Editar trabalho
    $(document).on('click', '.btn-edit-job', function(){
        Swal.fire({
            title: 'Gostaria de alterar o nome do seu trabalho?',
            text: `Nome atual: ${$(this).data('name')}`,
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Alterar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
        }).then((result) => {
            if(result.isConfirmed){
                // console.log(result);
                Swal.fire({
                    title: 'Fazendo alterações, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: `/edit-job`,
                    type: 'POST',
                    data: {id: $(this).data('id'),new_job_name: result.value},
                    success: (data) => {
                        // console.log(data);
                        Swal.fire({
                            icon: 'success',
                            title: 'Nome alterado com sucesso!'
                        }).then(()=>{
                            $(`#job-${$(this).data('id')}`).find('.btn-job-link').html(`<h3>${result.value}</h3>`);
                        });
                    }
                });
            }
        });
    });
});

function fieldMask(){
    $('.field_mask').each(function(){
        switch($(this).data('field_type')){
            case 'moeda':
                switch($(this).data('field_mask')){
                    case ',':
                        $(this).mask('000000000000,00', {reverse: true});
                        break;
                    case '.,':
                        $(this).mask('000.000.000.000,00', {reverse: true});
                        break;
                    case '.':
                        $(this).mask('000000000000.00', {reverse: true});
                        break;
                    case ',.':
                        $(this).mask('000,000,000,000.00', {reverse: true});
                        break;
                }
                break;
            case 'date':
                var dateMaskEdit = dateMaskSingle;
                dateMaskEdit.locale.format = $(this).data('field_mask');
                $(this).daterangepicker(dateMaskEdit);
                break;
            case 'date_time':
                var dateMaskEdit = dateMaskSingleTime;
                dateMaskEdit.locale.format = $(this).data('field_mask');
                $(this).daterangepicker(dateMaskEdit);
                break;
        }
    })
}

function sortableFieldCustom(){
    $('.custom-fields, .not-custom-fields').sortable({
        axis: 'y',
        placeholder: "ui-state-highlight",
        cursor: "move",
        forcePlaceholderSize: true,
        opacity: 0.5,
        beforeStop: function( event, ui ) {
            // var tag = $(ui.item[0]);
            var positions = [];
            $('.custom-field').each(function(index){
                var dados = $(this).data('dados');
                positions.push({
                    label_field_id: dados.label_field_id,
                    job_id: dados.job_id,
                    list_id: dados.list_id,
                    position: index+1,
                });
            });

            $.ajax({
                url: taskFieldUpdate,
                type: 'POST',
                data: {positions},
                success: (data) => {}
            });
        },
        containment: "document",
    }).disableSelection();
}

const dateMask = {
        singleDatePicker: false,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY',
            daysOfWeek: ['dom','seg','ter','qua','qui','sex','sab'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','outubro','Novembro','Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    };

const dateMaskTime = {
        singleDatePicker: false,
        showDropdowns: true,
        timePicker24Hour: true,
        timePicker: true,
        timePickerSeconds: true,
        locale: {
            format: 'DD/MM/YYYY HH:mm:ss',
            daysOfWeek: ['dom','seg','ter','qua','qui','sex','sab'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','outubro','Novembro','Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    };

const dateMaskSingle = {
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY',
            daysOfWeek: ['dom','seg','ter','qua','qui','sex','sab'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','outubro','Novembro','Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    };

const dateMaskSingleTime = {
        singleDatePicker: true,
        showDropdowns: true,
        timePicker24Hour: true,
        timePicker: true,
        timePickerSeconds: true,
        locale: {
            format: 'DD/MM/YYYY HH:mm:ss',
            daysOfWeek: ['dom','seg','ter','qua','qui','sex','sab'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','outubro','Novembro','Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    };