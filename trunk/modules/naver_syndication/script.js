(function ($) {
    $(document).ready(function () {
        $('#iboard_syndication_verify_btn').on('click', function () {
            var token = $.trim($('#iboard_syndication_token').val());

            if (token.length == 0) {
                alert('토큰값을 입력하세요');
                return false;
            }

            $.post(ajaxurl, {
                'action': 'iboard_syndication_verify_token',
                'token': token
            }, function (response) {
                if (response.error_code != 122) {
                    alert(response.msg);
                } else {
                    alert('정상 토큰입니다.');
                }
            });
        });
    });
})(jQuery);