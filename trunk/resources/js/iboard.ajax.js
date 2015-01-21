(function ($) {
    $.IBoardAjax = {
        defHandler: {
            success: function (result) {
            },
            error: function (result) {
            }
        },
        getDetail: function (param, handler) {
            var self = this;
            var handler = $.extend({}, self.defHandler, handler);
            var param = $.extend({
                'ID': '',
                'password': '',
                'BID': '',
                'action': 'iboard_api_detail'
            }, param);

            $.post(IBoard.ajax_url, param, function (result) {
                if (result) {
                    handler.success(result);
                } else {
                    handler.error(result);
                }
            });

            return self;
        },
        getList: function (param, handler) {
            var self = this;
            var handler = $.extend({}, self.defHandler, handler);

            var param = $.extend({
                'BID': '',
                'pageNo': '1',
                'action': 'iboard_api_list'
            }, param);

            $.post(IBoard.ajax_url, param, function (result) {
                if (result.objects.list) {
                    handler.success(result);
                } else {
                    handler.error(result);
                }
            });

            return self;
        },
        insert: function (param, handler) {
            var self = this;
            var handler = $.extend({}, self.defHandler, handler);

            var param = $.extend({
                'action': 'iboard_api_insert'
            }, param);

            $.post(IBoard.ajax_url, param, function (result) {
                if (result) {
                    handler.success(result);
                } else {
                    handler.error(result);
                }
            });
        },
        update: function (param, handler) {
            var self = this;
            var handler = $.extend({}, self.defHandler, handler);

            var param = $.extend({
                'action': 'iboard_api_update'
            }, param);

            $.post(IBoard.ajax_url, param, function (result) {
                if (result) {
                    handler.success(result);
                } else {
                    handler.error(result);
                }
            });
        },
        delete: function (param, handler) {
            var self = this;
            var handler = $.extend({}, self.defHandler, handler);

            var param = $.extend({
                'action': 'iboard_api_delete'
            }, param);

            $.post(IBoard.ajax_url, param, function (result) {
                if (result) {
                    handler.success(result);
                } else {
                    handler.error(result);
                }
            });
        }
    };
})(jQuery);