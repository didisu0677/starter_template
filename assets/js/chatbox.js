var chat_xhr = null;
var chat_input_height = 0;
var load_cData = false;
var is_load_chat = false;
var id_last_chat = 0;
var id_max_chat = 0;
var chat_key = '';
var chat_group = '';
var xhr_new_data = null;
var intervalAjaxRealtime = null;
function chatLinkify() {
    $('#chat-message li').linkify();
}
function clearChatXHR() {
    if( chat_xhr != null ) {
        chat_xhr.abort();
        chat_xhr = null;
        readonly_ajax = true;
    }
}
function getUserOnline() {
    localStorage.removeItem('chat-id');
    localStorage.removeItem('chat-nama');
    localStorage.removeItem('chat-foto');
    saveStorage({
        type : 'user_online'
    });
    var have_content = false;
    $('#chat-online').children().each(function(){
        if(!$(this).hasClass('ps__rail-x') && !$(this).hasClass('ps__rail-y')) have_content = true;
    });
    if(!have_content) {
        $('#chat-online').html(ellipsisLoader());
    }
    if(websocket_active) {
        var request = {
            id : id_user_login,
            keyword : $('#chat-search-user').val(),
            type : 'user_online'
        };
        conn.send(rand()+'@,'+Base64.encode(JSON.stringify(request)));
    } else {
        readonly_ajax = false;
        is_autocomplete = true;
        chat_xhr = $.ajax({
            url : base_url + 'ajax/json/online_user',
            data : {
                keyword : $('#chat-search-user').val()
            },
            type : 'post',
            dataType : 'json',
            success : function(response){
                var cData = JSON.stringify(response);
                localStorage.setItem('cData',cData);
                parseUserOnline(cData);
                readonly_ajax = true;
                is_autocomplete = false;
            }
        });
    }
}
function parseUserOnline(e) {
    var response = JSON.parse(e);
    var konten = '';
    if(response.length == 0) {
        if($('#chat-search-user').val()) {
            konten += '<div class="text-center p-4"><i class="chat-icon-content fa-search"></i><div class="mt-2 pl-2 pr-2">'+lang.pencarian_pengguna_yang_online_tidak_ditemukan+'</div></div>';
        } else {
            konten += '<div class="text-center p-4"><i class="chat-icon-content fa-user-circle"></i><div class="mt-2 pl-2 pr-2">'+lang.tidak_ada_pengguna_yang_online+'</div></div>';
        }
    } else {
        $.each(response,function(k,v){
            if(typeof v.id != 'undefined') {
                konten += '<a href="#" class="chat-list-item" data-id="'+v.id+'" data-group="0">';
                if(v.foto) {
                    konten += '<img src="'+base_url+'assets/upload/user/'+v.foto+'" alt="" />';
                } else {
                    konten += '<img src="'+base_url+'assets/uploads/user/default.png" alt="" />';
                }
                if(v.is_login == '1') {
                    konten += '<span class="is-online"></span>';
                }
                konten += '<span class="single-line">'+v.nama+'</span>';
                konten += '</a>';
            }
        });
    }
    $('#chat-online').html(konten);
    load_cData = false;
}
function getChatList() {
    localStorage.removeItem('chat-id');
    localStorage.removeItem('chat-nama');
    localStorage.removeItem('chat-foto');
    saveStorage({
        type : 'message'
    });
    var have_content = false;
    $('#chat-obrolan').children().each(function(){
        if(!$(this).hasClass('ps__rail-x') && !$(this).hasClass('ps__rail-y')) have_content = true;
    });
    if(!have_content) {
        $('#chat-obrolan').html(ellipsisLoader());
    }
    if(websocket_active) {
        var request = {
            id : id_user_login,
            type : 'list_chat'
        };
        conn.send(rand()+'@,'+Base64.encode(JSON.stringify(request)));
    } else {
        readonly_ajax = false;
        is_autocomplete = true;
        chat_xhr = $.ajax({
            url : base_url + 'ajax/json/list_chat',
            data : {},
            type : 'post',
            dataType : 'json',
            success : function(response){
                var cData = JSON.stringify(response);
                localStorage.setItem('cData',cData);
                parseChatList(cData);
                readonly_ajax = true;
                is_autocomplete = false;
            }
        });
    }
}
function parseChatList(e) {
    var konten = '';
    var not_read = 0;
    var response = JSON.parse(e);
    if(response.length == 0) {
        konten += '<div class="text-center p-4"><i class="chat-icon-content fa-comment-alt"></i><div class="mt-2 pl-2 pr-2">'+lang.belum_ada_obrolan+'</div></div>';
    } else {
        $.each(response,function(k,v){
            if(typeof v.id != 'undefined') {
                if(v.is_group == '1') {
                    var user_pengirim = v.id_pengirim == id_user_login ? lang.saya : v.nama_pengirim;
                    konten += '<a href="#" class="chat-list-item" data-id="0" data-group="'+v.key_id+'">';
                    konten += '<img src="'+base_url+'assets/uploads/user/group.png" alt="" />';
                    konten += '<span class="chat-item">'+v.nama_group+'</span>';
                    if(v.is_read2 == '0') {
                        konten += '<span class="chat-subitem"><strong>'+user_pengirim+' : ' + v.pesan.split('<br>').join(' ') + '</strong></span>';
                        not_read++;
                    } else {
                        konten += '<span class="chat-subitem">'+user_pengirim+' : ' + v.pesan.split('<br>').join(' ') + '</span>';
                    }
                    konten += '</a>';
                } else {
                    if(v.id_penerima == id_user_login) {
                        konten += '<a href="#" class="chat-list-item" data-id="'+v.id_pengirim+'" data-group="0">';
                        if(v.foto) {
                            konten += '<img src="'+base_url+'assets/upload/user/'+v.foto+'" alt="" />';
                        } else {
                            konten += '<img src="'+base_url+'assets/uploads/user/default.png" alt="" />';
                        }
                        konten += '<span class="chat-item">'+v.nama+'</span>';
                        if(v.is_read == '0') {
                            konten += '<span class="chat-subitem"><i class="fa-comment-alt"></i> <strong>' + v.pesan.split('<br>').join(' ') + '</strong></span>';
                            not_read++;
                        } else {
                            konten += '<span class="chat-subitem"><i class="fa-comment-alt text-success"></i> ' + v.pesan.split('<br>').join(' ') + '</span>';
                        }
                        konten += '</a>';
                    } else {
                        konten += '<a href="#" class="chat-list-item" data-id="'+v.id_penerima+'" data-group="0">';
                        if(v.foto) {
                            konten += '<img src="'+base_url+'assets/upload/user/'+v.foto+'" alt="" />';
                        } else {
                            konten += '<img src="'+base_url+'assets/uploads/user/default.png" alt="" />';
                        }
                        konten += '<span class="chat-item">'+v.nama+'</span>';
                        if(v.is_read == '0') {
                            konten += '<span class="chat-subitem"><i class="fa-check"></i> ' + v.pesan.split('<br>').join(' ') + '</span>';
                        } else {
                            konten += '<span class="chat-subitem"><i class="fa-check text-success"></i> ' + v.pesan.split('<br>').join(' ') + '</span>';
                        }
                        konten += '</a>';                            
                    }
                }
            }
        });
    }
    var jml_not_read = not_read < 10 ? not_read : '9+';
    $('.navbar-nav .btn-nav-chat span').remove();
    if(not_read > 0) {
        $('.navbar-nav .btn-nav-chat').append('<span class="tag tag-pill tag-up">'+jml_not_read+'</span>');
    }
    $('#chat-obrolan').html(konten);
    load_cData = false;
}
function getGroupList() {
    localStorage.removeItem('chat-id');
    localStorage.removeItem('chat-nama');
    localStorage.removeItem('chat-foto');
    saveStorage({
        type : 'grup'
    });
    var have_content = false;
    $('#chat-grup').children().each(function(){
        if(!$(this).hasClass('ps__rail-x') && !$(this).hasClass('ps__rail-y')) have_content = true;
    });
    if(!have_content) {
        $('#chat-grup').html(ellipsisLoader());
    }
    if(websocket_active) {
        var request = {
            id : id_user_login,
            type : 'grup'
        };
        conn.send(rand()+'@,'+Base64.encode(JSON.stringify(request)));
    } else {
        readonly_ajax = false;
        is_autocomplete = true;
        chat_xhr = $.ajax({
            url : base_url + 'ajax/json/list_group',
            data : {},
            type : 'post',
            dataType : 'json',
            success : function(response){
                var cData = JSON.stringify(response);
                localStorage.setItem('cData',cData);
                parseGroupList(cData);
                readonly_ajax = true;
                is_autocomplete = false;
            }
        });
    }
}
function parseGroupList(e) {
    var response = JSON.parse(e);
    var konten = '';
    if(response.length == 0) {
        konten += '<div class="text-center p-4"><i class="chat-icon-content fa-users"></i><div class="mt-2 pl-2 pr-2">'+lang.anda_belum_masuk_grup_manapun+'</div></div>';
    } else {
        $.each(response,function(k,v){
            if(typeof v.id != 'undefined') {
                konten += '<a href="#" class="chat-list-item" data-group="'+v.id+'" data-id="0">';
                konten += '<img src="'+base_url+'assets/uploads/user/group.png" alt="" />';
                konten += '<span class="single-line" title="'+v.nama+'">'+v.nama+'</span>';
                konten += '</a>';
            }
        });
    }
    $('#chat-grup').html(konten);
    load_cData = false;
}
function chatBack() {
    if( xhr_new_data != null ) {
        xhr_new_data.abort();
        xhr_new_data = null;
    }
    if( intervalAjaxRealtime != null) {
        clearInterval(intervalAjaxRealtime);
    }
    $('.chat-message').html('');
    $('#chat-list-title,#chatMenu').removeClass('hidden');
    $('#chat-active-title,#chatContent').addClass('hidden');
    if($('#chat-online-tab').hasClass('active')) {
        getUserOnline();
    } else {
        getChatList();
    }
}
function saveStorage(e) {
    if(typeof e.type != 'undefined') localStorage.setItem('rt-type',e.type);
    if(typeof e.user_id != 'undefined') localStorage.setItem('chat-id',e.user_id);
    if(typeof e.user_nama != 'undefined') localStorage.setItem('chat-nama',e.user_nama);
    if(typeof e.user_foto != 'undefined') localStorage.setItem('chat-foto',e.user_foto);
}
function removeStorage() {
    localStorage.removeItem('rt-type');
}
function chatSend(e) {
    if(websocket_active) {
        e['type'] = 'chat';
        var message = JSON.stringify(e);
        conn.send(rand()+'@,'+Base64.encode(message));
        localStorage.removeItem('cData');
    } else {
        $.ajax({
            url : base_url + 'ajax/json/send_chat',
            data : e,
            type : 'post',
            success : function(response) {
                loadNewChat();
            }
        });
    }
}
function openChatCallback(e) {
    var d = e.split('||');
    $('#chat-active-title img').attr('src',d[3]);
    nama_user = d[1];
    $('#chat-active-title .chat-header-title span').text(nama_user);
    $('#chat-active-title img').attr('src',$(this).find('img').attr('src'));
    $('#chat-list-title,#chatMenu').addClass('hidden');
    $('#chat-active-title,#chatContent').removeClass('hidden');
    if(!websocket_active && $('#chatContent .alert').length == 0) {
        $('#chatContent').prepend('<div class="alert alert-warning alert-chat">'+lang.obrolan_tidak_realtime+'</div>');
        $('.chat-message').css({'padding-top':'2.5rem'});
    }
    saveStorage({
        type : 'chat',
        user_id : d[0],
        user_nama : nama_user,
        user_foto : d[3]
    });
    chat_group = d[2];
    if(!$('.chat-box').hasClass('chat-show')) {
        $('.chat-box').addClass('chat-show');
    }
    id_last_chat = 0;
    $('.chat-message').html('');
    loadChat();
}
function loadChat() {
    if(!is_load_chat && id_last_chat >= 0) {
        $('.chat-message').prepend(ellipsisLoader());
        if(id_last_chat == 0) chat_key = '';
        if(chat_group != '' && chat_group != '0') chat_key = chat_group;
        is_load_chat = true;
        if(websocket_active) {
            var request = {
                last_id : id_last_chat,
                chat_key : chat_key,
                id_user1 : id_user_login,
                id_user2 : localStorage.getItem('chat-id'),
                type : 'chat_content'
            };
            conn.send(rand()+'@,'+Base64.encode(JSON.stringify(request)));
        } else {
            readonly_ajax = false;
            is_autocomplete = true;
            $.ajax({
                url : base_url + 'ajax/json/get_chat',
                data : {
                    last_id : id_last_chat,
                    chat_key : chat_key,
                    id_user1 : id_user_login,
                    id_user2 : localStorage.getItem('chat-id')
                },
                type : 'post',
                dataType : 'json',
                success : function(response) {
                    var cData = JSON.stringify(response);
                    if(id_last_chat == 0) {
                        id_max_chat = response.first_id;
                        localStorage.setItem('cData',cData);
                        intervalAjaxRealtime = setInterval(loadNewChat,5000);
                    }
                    parseChat(cData);
                    readonly_ajax = true;
                    is_autocomplete = false;
                }
            });
        }
    }
    load_cData = false;
}
function loadNewChat() {
    readonly_ajax = false;
    is_autocomplete = true;
    if( xhr_new_data != null ) {
        xhr_new_data.abort();
        xhr_new_data = null;
    }
    if(intervalAjaxRealtime != null && websocket_active) {
        clearInterval(intervalAjaxRealtime);
    }
    xhr_new_data = $.ajax({
        url : base_url + 'ajax/json/get_chat/new',
        data : {
            last_id : id_max_chat,
            chat_key : chat_key,
            id_user1 : id_user_login,
            id_user2 : localStorage.getItem('chat-id')
        },
        type : 'post',
        dataType : 'json',
        success : function(response) {
            if(response.data.length > 0) {
                id_max_chat = response.last_id;

                var must_scroll = false;
                if($('#chat-message').scrollTop() + $('#chat-message').height() > $('#chat-message').prop("scrollHeight") - 100) {
                    must_scroll = true;
                }

                $.each(response.data,function(k,v) {
                    var bubble  = $('.chat-message .chat-bubble').last();
                    if(v.id_pengirim == id_user_login) {
                        if(bubble.hasClass('me')) {
                            bubble.append('<li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="left">'+v.pesan+'</li>');
                        } else {
                            $('#chat-message').append('<ul class="chat-bubble me"><li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="left">'+v.pesan+'</li></ul>');
                        }
                    } else {
                        if(localStorage.getItem('chat-id') == '0') {
                            if(bubble.hasClass('him') && bubble.attr('data-id') == v.id_pengirim) {
                                bubble.append('<li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="right">'+v.pesan+'</li>');
                            } else {
                                $('#chat-message').append('<ul class="chat-bubble him" data-id="'+v.id_pengirim+'"><li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="right"><div class="chat-username text-primary">'+v.nama_pengirim+'</div>'+v.pesan+'</li></ul>');
                            }
                        } else {
                            if(bubble.hasClass('him')) {
                                bubble.append('<li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="right">'+v.pesan+'</li>');
                            } else {
                                $('#chat-message').append('<ul class="chat-bubble him"><li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="right">'+v.pesan+'</li></ul>');
                            }
                        }
                    }
                    $('[data-toggle="tooltip"]').tooltip();
                });

                if(must_scroll) {
                    $('#chat-message').animate({ scrollTop: $('#chat-message').prop("scrollHeight") });
                }

            }
            readonly_ajax = true;
            is_autocomplete = false;
        }
    });
}
function parseChat(e) {
    is_load_chat = false;
    $('.chat-message .lds-ellipsis').remove();
    var response = JSON.parse(e);
    var first_parse = id_last_chat == 0 ? true : false;
    if(typeof response.chat_key != 'undefined') chat_key = response.chat_key;
    id_last_chat = parseInt(response.last_id);
    var f_height = $('#chat-message').prop("scrollHeight");
    $.each(response.data,function(k,v) {
        var bubble  = $('.chat-message .chat-bubble').first();
        if(v.id_pengirim == id_user_login) {
            if(bubble.hasClass('me')) {
                bubble.prepend('<li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="left">'+v.pesan+'</li>');
            } else {
                $('#chat-message').prepend('<ul class="chat-bubble me"><li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="left">'+v.pesan+'</li></ul>');
            }
        } else {
            if(localStorage.getItem('chat-id') == '0') {
                if(bubble.hasClass('him') && bubble.attr('data-id') == v.id_pengirim) {
                    bubble.children('li').each(function(){
                        $(this).children('.chat-username').remove();
                    });
                    bubble.prepend('<li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="right"><div class="chat-username text-primary">'+v.nama_pengirim+'</div>'+v.pesan+'</li>');
                } else {
                    $('#chat-message').prepend('<ul class="chat-bubble him" data-id="'+v.id_pengirim+'"><li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="right"><div class="chat-username text-primary">'+v.nama_pengirim+'</div>'+v.pesan+'</li></ul>');
                }
            } else {
                if(bubble.hasClass('him')) {
                    bubble.prepend('<li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="right">'+v.pesan+'</li>');
                } else {
                    $('#chat-message').prepend('<ul class="chat-bubble him"><li title="'+cDate(v.tanggal,true)+'" data-toggle="tooltip" data-placement="right">'+v.pesan+'</li></ul>');
                }
            }
        }
        $('[data-toggle="tooltip"]').tooltip();
    });
    var l_height = $('#chat-message').prop("scrollHeight");
    var _top = l_height - f_height;
    if(!first_parse) {
        $('#chat-message').scrollTop(_top);
    } else {
        $('#chat-message').scrollTop($('#chat-message').prop("scrollHeight"));
        if(localStorage.getItem('chat-id') == '0') {
            $('.chat-header-title span').addClass('chat-group-title').attr('data-id',chat_key);
        } else {
            $('.chat-header-title span').removeClass('chat-group-title').removeAttr('data-id');
        }    
    }
    chatLinkify();
}
$('#chat-message').scroll(function(){
    if($(this).scrollTop() == 0) {
        loadChat();
    }
});
$(document).ready(function(){
    const ps_chat_list1 = new PerfectScrollbar('#chat-online');
    const ps_chat_list2 = new PerfectScrollbar('#chat-obrolan');
    const ps_chat_list3 = new PerfectScrollbar('#chat-grup');
    const ps_chat_list4 = new PerfectScrollbar('#chat-message');
    new EmojiPicker();
    var chat_type = localStorage.getItem('rt-type');
    if(chat_type != null) {
        load_cData = true;
        if(chat_type == 'user_online') {
            $('.chat-box').addClass('chat-show');
            $('#chat-online-tab').trigger('click');
        } else if(chat_type == 'message') {
            $('.chat-box').addClass('chat-show');
            $('#chat-obrolan-tab').trigger('click');
        } else if(chat_type == 'grup') {
            $('.chat-box').addClass('chat-show');
            $('#chat-grup-tab').trigger('click');
        } else if(chat_type == 'chat') {
            $('#chat-active-title .chat-header-title span').text(localStorage.getItem('chat-nama'));
            $('#chat-active-title img').attr('src',localStorage.getItem('chat-foto'));
            $('#chat-list-title,#chatMenu').addClass('hidden');
            $('#chat-active-title,#chatContent').removeClass('hidden');        
            $('.chat-box').addClass('chat-show');
            id_last_chat = 0;
            if(load_cData && localStorage.getItem('cData') != null && websocket_active) {
                parseChat(localStorage.getItem('cData'));
            } else {
                loadChat();
            }
        }
    }
});
$('#chat-input').textareaAutoSize();
$('#chat-input').keyup(function(){
    var x_height = 0;
    if($('body').data('size') == 'normal') {
        x_height = $(this).height() - 20;
        $('#chat-message').css({'height':'calc(100% - 6.25rem - ' + x_height + 'px)'});
    } else {
        x_height = $(this).height() - 17;
        $('#chat-message').css({'height':'calc(100% - 5.25rem - ' + x_height + 'px)'});
    }
    var emojiTop = 210;
    $('.emoji-picker').css({'top':'-'+emojiTop+'px'});
});
$('#chat-input').keydown(function(e){
    if (e.keyCode == 13) {
        if (!e.shiftKey) {
            var pesan   = $(this).val().replace(/(?:\r\n|\r|\n)/g, '<br>');            
            chatSend({
                pesan : pesan,
                id_pengirim : id_user_login,
                id_penerima : localStorage.getItem('chat-id'),
                chat_key : chat_key
            });
            $(this).val('');
            if(websocket_active) {
                var bubble  = $('.chat-message .chat-bubble').last();
                if(bubble.hasClass('me')) {
                    bubble.append('<li title="'+cDate(curDate(),true)+'" data-toggle="tooltip" data-placement="left">'+pesan+'</li>');
                } else {
                    $('#chat-message').append('<ul class="chat-bubble me"><li title="'+cDate(curDate(),true)+'" data-toggle="tooltip" data-placement="left">'+pesan+'</li></ul>');
                }
                $('[data-toggle="tooltip"]').tooltip();
                $('#chat-message').animate({ scrollTop: $('#chat-message').prop("scrollHeight") });
                chatLinkify();
            }
            return false;
        }
    }
});
$('.btn-nav-chat').click(function(e){
    e.preventDefault();
    if($('.chat-box').hasClass('chat-show')) {
        $('.chat-box').removeClass('chat-show');
        chatBack();
        removeStorage();
    } else {
        $('.chat-box').addClass('chat-show');
        if($('#chat-online-tab').hasClass('active')) {
            getUserOnline();
        } else if($('#chat-obrolan-tab').hasClass('active')) {
            getChatList();
        } else {
            getGroupList();
        }
    }
});
$('.ic-close').click(function(){
    $('.chat-box').removeClass('chat-show');
    removeStorage();
});
$('.ic-back').click(function(){
    chatBack();
});
$('#chat-online-tab').click(function(){
    $('.chat-search-user').removeClass('hidden');
    if(load_cData) {
        parseUserOnline(localStorage.getItem('cData'));
    } else {
        getUserOnline();
    }
});
$('#chat-search-user').keyup(function(){
    getUserOnline();
});
$('#chat-obrolan-tab').click(function(){
    $('.chat-search-user').addClass('hidden');
    if(load_cData) {
        parseChatList(localStorage.getItem('cData'));
    } else {
        getChatList();
    }
});
$('#chat-grup-tab').click(function(){
    $('.chat-search-user').addClass('hidden');
    if(load_cData) {
        parseGroupList(localStorage.getItem('cData'));
    } else {
        getGroupList();
    }
});
$(document).on('click','.chat-list-item',function(e){
    e.preventDefault();
    if(typeof $(this).attr('data-id') != 'undefined') {
        var nama_user = '';
        $('#chat-active-title img').attr('src',$(this).find('img').attr('src'));
        if($(this).find('.chat-item').length == 1) {
            nama_user = $(this).find('.chat-item').text();
        } else {
            nama_user = $(this).find('.single-line').text();
        }
        $('#chat-active-title .chat-header-title span').text(nama_user);
        $('#chat-active-title img').attr('src',$(this).find('img').attr('src'));
        $('#chat-list-title,#chatMenu').addClass('hidden');
        $('#chat-active-title,#chatContent').removeClass('hidden');
        saveStorage({
            type : 'chat',
            user_id : $(this).attr('data-id'),
            user_nama : nama_user,
            user_foto : $(this).find('img').attr('src')
        });
        chat_group = $(this).attr('data-group');
        id_last_chat = 0;
        loadChat();
    }
});
$(document).on('click','.chat-group-title',function(){
    var title = $(this).text();
    $.get(base_url + 'ajax/html/chat_anggota/' + $(this).attr('data-id'),function(res){
        cInfo.open(title,res,{'modal_lg':false});
    });
});