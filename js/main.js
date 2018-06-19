$(document).ready(() => {
    let chat = $('.chat');
    let inp = $('.inp');

    const getMessages = () => {
        setInterval(() => {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: '/server.php',
                success: (data) => {
                    chat.text('');
                    for (let i = 0; i < data.messages.length; i++) {
                        let span = $('<span></span>');
                        let spanLogin = $('<span class="login"></span>');
                        span.text(data.messages[i].text);
                        spanLogin.text(data.messages[i].login);
                        chat.append(spanLogin);
                        chat.append(span);
                    }
                }
            });
        }, 700);
    };

    inp.keypress((event) => {
        if (event.which === 13) {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: '/server.php',
                data: {message: inp.val()}
            });
            inp.val('');
        }
    });

    $.ajax({
        dataType: 'json',
        method: 'post',
        url: '/check.php',
        data: {id: $.cookie('id'), hash: $.cookie('hash')},
        success: (data) => {
            $('.outh-block').addClass('d-none');
            $('.border-chat').addClass('d-block');
            getMessages();
        },
        error: (data) => {
        }
    });

    VK.init({
        apiId: 6607349
    });

    $('.outh-btn').click(() => {
        VK.Auth.login((data) => {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: '/register.php',
                data: data,
                success: (data) => {
                    window.location.reload();
                },
                error: (data) => {
                    window.location.reload();
                }
            });
        });
    });
});