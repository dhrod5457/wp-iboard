;
(function ($) {
    function executeFunctionByName(functionName /*, args */) {
        var args = [].slice.call(arguments).splice(1);
        var namespaces = functionName.split(".");
        var func = namespaces.pop();

        return IBoard[func].apply(this, args);
    }

    IBoard.iboard_upload_editor_callback = function (response) {
        var $image = $('<img>', {
            'src': response.url
        });

        var output = $image.clone().wrapAll("<div/>").parent().html();
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, output);
    }

    IBoard.iboard_upload_editor_pre = function (obj) {
        var fileName = obj.input.val();
        var ext = ['.jpg', '.png', '.gif', 'jpeg', 'bmp'];
        var result = (new RegExp('(' + ext.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);

        if (!result) {
            alert('허용하지 않는 확장자입니다.');
            return false;
        } else {
            return true;
        }
    }

    $.iboardUploadButton = function (element, options) {
        var $uploadForm = $('#iboardUploadForm');
        var $fileInput = $('#iboard_upload_file');

        var $this = $(element);
        var callbackName = $this.data('callback');
        var preFunction = $this.data('pre_function');
        var p_check = $this.data('p_check');

        $this.bind('click', function (e) {
            e.preventDefault();

            $fileInput.unbind('change');
            $uploadForm.find('#p_check').val(p_check);
            input_bind();
            $fileInput.trigger('click');
        });

        function input_bind() {
            $fileInput.bind('change', function (e) {
                var v = $(this).val();

                if (v.length == 0) {
                    return;
                }

                var preResult = executeFunctionByName('IBoard.' + preFunction, {
                        'input': $fileInput,
                        'form': $uploadForm
                    }
                );

                if (!preResult)
                    return;

                $uploadForm.ajaxSubmit({
                    success: function (response) {
                        try {
                            if (!response.result) {
                                alert(response.message);
                            } else {
                                executeFunctionByName('IBoard.' + callbackName, response);
                            }

                        } catch (e) {
                            alert(e);
                        }
                    }
                });
            });
        }
    }

    $.fn.iboardUploadButton = function (options) {
        return this.each(function () {
            $.iboardUploadButton($(this), options);
        });
    }

    IBoard.query_vars = JSON.parse(IBoard.query_vars);

    function hide_media_button() {
        var mce_active = $('#wp-iboard_content-wrap').hasClass('tmce-active');

        if (!mce_active) {
            $('#iboard_upload_btn').hide();
        } else {
            $('#iboard_upload_btn').show();
        }
    }

    $(document).ready(function () {
        $('.itemClass').each(function (i, item) {
            var $items = $(item).children();
            var last = $items.length - 1;

            $items.each(function (index, child) {
                $(child).addClass('item' + (index + 1));

                if (last == index) {
                    $(child).addClass('last');
                }
                if (0 == index) {
                    $(child).addClass('first');
                }
            });
        });

        $('.iboard_upload_btn').iboardUploadButton();

        if ($.fn.validate != undefined) {
            $('.validateForm').validate();
        }

        $('#wp-iboard_content-wrap .wp-editor-tabs button').click(function () {
            hide_media_button();
        });

        hide_media_button();
    });
})(jQuery);