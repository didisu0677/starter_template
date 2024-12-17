
    $(document).on('click','.btn-edit-lang',function(e){
        e.preventDefault();
        cLoader.open(lang.memuat_data);
        var file = $(this).attr('data-id');
        $.ajax({
            url : base_url + 'settings/bahasa/get_directory',
            data : {file : file, bahasa : $('#bahasa').val()},
            type : 'post',
            dataType : 'json',
            success : function(response) {
                konten = '';
                $.each(response,function(k,v){
                    konten += '<div class="form-group row">' +
                                '<label class="col-form-label col-sm-3" for="bahasa">'+k.replace(/\_/g,' ')+'</label>' +
                                '<div class="col-sm-9">' +
                                    '<input type="hidden" name="key[]" value="'+k+'">' +
                                    '<input type="text" name="value[]" autocomplete="off" class="form-control" value="'+v+'">' +
                                '</div>' +
                            '</div>';
                });
                $('#file').val(file);
                $('#edit-lang').html(konten);
                $('#modal-form').modal();
                cLoader.close();
            }
        });
    });
