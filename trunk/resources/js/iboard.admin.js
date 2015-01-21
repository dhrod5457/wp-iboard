;
(function ($) {
    $(document).ready(function () {
        $('.chosen-select').chosen({});

        $('.chosen-select').chosen().change(function (e, obj) {
            var value = $(this).val();
            var selected = obj.selected;

            if (selected == 'all') {
                $(this).val(['all']);
                $(this).trigger('chosen:updated');
                return;
            }

            //전체공개가 들어있을때 항상 체크된다.
            if ($.inArray('all', value) != -1) {
                var result = $.grep(value, function (n, i) {
                    return n != 'all';
                });
                $(this).val(result);
                $(this).trigger('chosen:updated');
            }
        });

        function beforeRoleSetting(roles, $item) {
            if (roles != null)
                $item.val(roles.join(','));
        }

        $('.chosen-select').each(function (i, item) {
            var v = $(item).val();

            if (v == null) {
                $(item).find('option[value="all"]').attr('selected', 'selected');
                $(item).trigger('chosen:updated');
            }
        });


        $('#iboardSettingForm').ajaxForm({
            beforeSerialize: function ($form, options) {
                $('.chosen-select').each(function (i, item) {
                    var target = $(item).data('target');
                    beforeRoleSetting($(item).val(), $("#" + target));
                });
            },
            success: function (response) {
                if (response.result != 'error') {
                    alert('수정되었습니다.');
                } else {
                    alert(response.message);
                }
            }
        });

        $('.deleteBoardSetting').bind('click', function (e) {
            e.preventDefault();

            var id = $(this).data('id');
            var bid = $(this).data('bid');

            if (confirm('삭제하시겠습니까?')) {
                $.post(ajaxurl, {
                    'ID': id,
                    'BID': bid,
                    'action': 'iboard_setting_ajax',
                    'm': 'delete'
                }, function (response) {
                    location.reload();
                });
            }
        });
    });
})(jQuery);