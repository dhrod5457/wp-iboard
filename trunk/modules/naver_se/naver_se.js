;
(function ($) {
    $(document).ready(function () {
        var oEditors = [];

        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: NaverSE.editor_name,
            sSkinURI: NaverSE.sSkinURI,
            htParams: {
                bUseToolbar: true,
                bUseVerticalResizer: true,
                bUseModeChanger: true,
                fOnBeforeUnload: function () {
                }
            },
            fOnAppLoad: function () {
                //oEditors.getById["ir1"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]);
            },
            fCreator: "createSEditor2"
        });

        $('#' + NaverSE.target_form).submit(function () {
            var v = oEditors.getById[NaverSE.editor_name].getIR();
            $('#' + NaverSE.name).val(v);
        });
    });
})(jQuery);