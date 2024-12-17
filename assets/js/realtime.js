var ws_s = Base64.decode(ws_server);
if(ws_s) {
    var conn = new WebSocket(ws_s);
    conn.onopen = function(e) {
        websocket_active = true;
        conn.onmessage = function(e) {
            var x_data = e.data.split('#,');
            var o_data = Base64.decode(x_data[1]);
            var obj = JSON.parse(o_data);
            if(obj.type == 'chat') {
                var penerima;
                var pengirim;
                var nama_pengirim;
                var nm_pengirim = obj.nama_pengirim;
                var n_pengirim  = nm_pengirim.split(' @ ');
                if(typeof obj.id_penerima == 'string') {
                    penerima = [obj.id_penerima];
                    pengirim = obj.id_pengirim;
                    nama_pengirim = n_pengirim[0];
                } else {
                    penerima = obj.id_penerima;
                    pengirim = '0';
                    nama_pengirim = n_pengirim[1];
                }
                $.each(penerima,function(k,v){
                    if(id_user_login == v) {
                        var cur_chat = false;
                        if($('.chat-box').hasClass('chat-show') && localStorage.getItem('rt-type') == 'chat') {
                            if((pengirim == '0' && obj.chat_key == chat_group) || pengirim == localStorage.getItem('chat-id')) {
                                cur_chat = true;
                            }
                        }
                        if($('.chat-box').hasClass('chat-show') && localStorage.getItem('rt-type') == 'chat' && cur_chat) {
                            var bubble  = $('.chat-message .chat-bubble').last();
                            if(pengirim == '0') {
                                if(obj.id_pengirim == id_user_login) {
                                    if(bubble.hasClass('me')) {
                                        bubble.append('<li title="'+cDate(obj.tanggal,true)+'" data-toggle="tooltip" data-placement="left">'+obj.pesan+'</li>');
                                    } else {
                                        $('#chat-message').append('<ul class="chat-bubble me"><li title="'+cDate(obj.tanggal,true)+'" data-toggle="tooltip" data-placement="left">'+obj.pesan+'</li></ul>');
                                    }
                                } else {
                                    if(bubble.hasClass('him') && obj.id_pengirim == bubble.attr('data-id')) {
                                        bubble.append('<li title="'+cDate(obj.tanggal,true)+'" data-toggle="tooltip" data-placement="right">'+obj.pesan+'</li>');
                                    } else {
                                        $('#chat-message').append('<ul class="chat-bubble him" data-id="'+obj.id_pengirim+'"><li title="'+cDate(obj.tanggal,true)+'" data-toggle="tooltip" data-placement="right"><div class="chat-username text-primary">'+n_pengirim[0]+'</div>'+obj.pesan+'</li></ul>');
                                    }
                                }
                                $('[data-toggle="tooltip"]').tooltip();
                                $('#chat-message').animate({ scrollTop: $('#chat-message').prop("scrollHeight") });
                            } else {
                                if(bubble.hasClass('him')) {
                                    bubble.append('<li title="'+cDate(obj.tanggal,true)+'" data-toggle="tooltip" data-placement="right">'+obj.pesan+'</li>');
                                } else {
                                    $('#chat-message').append('<ul class="chat-bubble him"><li title="'+cDate(obj.tanggal,true)+'" data-toggle="tooltip" data-placement="right">'+obj.pesan+'</li></ul>');
                                }
                                $('[data-toggle="tooltip"]').tooltip();
                                $('#chat-message').animate({ scrollTop: $('#chat-message').prop("scrollHeight") });
                            }
                            if($('.chat-conteiner .alert').length > 0) {
                                $('.chat-conteiner .alert').remove();
                            }
                            chatLinkify();
                            localStorage.removeItem('cData');
                        } else {
                            var foto = obj.foto_pengirim ? obj.foto_pengirim : 'default.png';
                            var i_foto = pengirim == '0' ? 'group.png' : foto;
                            pushNotification({
                                title : obj.nama_pengirim,
                                desc : obj.pesan,
                                icon : base_url + 'assets/uploads/user/' + foto,
                                link : 'openChatCallback##' + pengirim + '||' + nama_pengirim + '||' + obj.chat_key + '||' + base_url + 'assets/uploads/user/' + i_foto
                            });
                            var request = {
                                id : id_user_login,
                                type : 'unread'
                            };
                            conn.send(JSON.stringify(request));
                            if($('.chat-box').hasClass('chat-show') && localStorage.getItem('rt-type') == 'message') {
                                getChatList();
                            }
                        }
                    }
                });
            } else if(obj.type == 'user_online') {
                var cData = JSON.stringify(obj.data);
                localStorage.setItem('cData',cData);
                parseUserOnline(cData);
            } else if(obj.type == 'list_chat') {
                var cData = JSON.stringify(obj.data);
                localStorage.setItem('cData',cData);
                parseChatList(cData);
            } else if(obj.type == 'grup') {
                var cData = JSON.stringify(obj.data);
                localStorage.setItem('cData',cData);
                parseGroupList(cData);
            } else if(obj.type == 'unread') {
                var jml_not_read = parseInt(obj.unread) < 10 ? parseInt(obj.unread) : '9+';
                $('.navbar-nav .btn-nav-chat span').remove();
                if(parseInt(obj.unread) > 0) {
                    $('.navbar-nav .btn-nav-chat').append('<span class="tag tag-pill tag-up">'+jml_not_read+'</span>');
                }                
            } else if(obj.type == 'chat_content') {
                var cData = JSON.stringify(obj);
                if(id_last_chat == 0) {
                    localStorage.setItem('cData',cData);
                }
                parseChat(cData);
            } else if(obj.type == 'notification') {
                console.log(obj.id_penerima);
                if(inArray(id_user_login,obj.id_penerima) || obj.id_penerima.length == 0) {
                    pushNotification({
                        title : obj.judul,
                        desc : obj.pesan,
                        link : obj.link
                    });
                }
            }
        };
    };
}
function pushNotification(option) {
    if(Push.Permission.has()) {
        var title = typeof option.title !== 'undefined' ? option.title : $('meta[name="appname"]').attr('content');
        var desc = typeof option.desc !== 'undefined' ? option.desc : '';
        var icon = typeof option.icon !== 'undefined' ? option.icon : $('link[rel="shortcut icon"]').attr('href');
        var link = typeof option.link !== 'undefined' ? option.link : '';
        Push.create(title, {
            body: desc,
            icon: icon,
            timeout: 5000,
            onClick: function () {
                if(link) {
                    var l = link.split('##');
                    if(l.length == 2) {
                        var act = window[l[0]];
                        if(typeof act == 'function') {
                            act(l[1]);
                        } else {
                            window.location = link;
                        }
                    } else {
                        window.location = link;
                    }
                } else {
                    window.focus();
                }
                this.close();
            }
        });    
    }
}
$(document).ready(function(){
    if(!Push.Permission.has()) {
        Push.Permission.request(function(){
            Push.create($('meta[name="appname"]').attr('content'), {
                body: lang.notifikasi_diaktifkan,
                icon: $('link[rel="shortcut icon"]').attr('href'),
                timeout: 5000,
                onClick: function () {
                    window.focus();
                    this.close();
                }
            });    
        });
    }
});