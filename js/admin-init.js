/**
 * Created by moridrin on 5-12-16.
 */
jQuery(function ($) {
    $(document).ready(function () {
        $('.datetimepicker').datetimepicker({
            mask:'9999-19-39 29:59',
            format:'Y-m-d H:i'
        });
    });
});