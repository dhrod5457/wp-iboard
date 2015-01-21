;
(function ($) {
    IBoard.iboard_file_pre = function (obj) {
        var max_file_cnt = IBoard.iboard_file_cnt;
        var current_file_cnt = $upload_file_wrapper.children().length;

        if (max_file_cnt == current_file_cnt) {
            alert(IBoard.message_over_file_cnt);
            return false;
        }

        return true;
    }

    IBoard.iboard_file_callback = function (response) {
        var fileName = response.file.name;
        var url = response.url;

        render_item(fileName, url);

        $(document).trigger('iboard_file_callback', response);
    }

    function render_item(name, url) {
        var $item = $("<li>", {
            "html": "<span>" + name + "</span>"
        });

        $('<a>', {
            "class": "iboard_file_del",
            "text": IBoard.message_delete,
            "href": "#",
            "click": function (e) {
                e.preventDefault();
                $item.remove();
            }
        }).appendTo($item);

        $("<input>", {
            "name": "iboard_file[]",
            "value": url,
            "type": "hidden"
        }).appendTo($item);

        $("<input>", {
            "name": "iboard_file_name[]",
            "value": name,
            "type": "hidden"
        }).appendTo($item);

        $upload_file_wrapper.append($item);
    }

    $(document).ready(function () {
        window.$upload_file_wrapper = $('.iboard_upload_file_list');

        $.post(IBoard.ajax_url, {
            "ID": IBoard.query_vars.ID,
            "action": "iboard_file_ajax_list"
        }, function (response) {
            if (response != null) {
                $.each(response, function (i, item) {
                    render_item(item.name, item.url);
                })
            }
        });
    });
}(jQuery));