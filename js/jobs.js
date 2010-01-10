(function ($) {

    var c_path = '/projects/jobs/';
    var c_expr = 3650; // in days
    var c_size = 84;

    $(function () {
        $('#jobs').click(function (e) {
            var aha = $(e.target);
            if (!aha.hasClass('hd')) {
                aha = aha.parents('.hd');
            }
            aha.siblings('.bd').toggle();
            if (!aha.hasClass('viewed')) {
                aha.addClass('viewed');
                var track = aha.attr('id');
                console.log(track);
                var storage  = $.cookie('feedtrack');
                var json_str = '';
                if (storage == null) {
                    storage = [];
                    storage.push(track);
                    json_str = $.toJSON({t: storage});
                } else {
                    storage = $.evalJSON(storage);
                    var l = storage.t.length;
                    if (l >= c_size) {
                        while (true) {
                            storage.t.shift();
                            if (storage.t.length < c_size) {
                                break;
                            }
                        }
                    }
                    storage.t.push(track);
                    json_str = $.toJSON({t: storage.t});
                }
                $.cookie('feedtrack', json_str, { expires: c_expr, path: c_path });
            }
        });

        var feed_pref = function () {
            $('#jobs .jobItem').addClass('invisible').removeClass('visible');
            var pref = []
            $('input[name="feeds[]"]:checked').each(function () {
                $('.' + this.value).removeClass('invisible').addClass('visible');
                pref.push(this.value);
            });
            $('#filter').keyup();
            $.cookie('feedpref', $.toJSON(pref), { expires: c_expr, path: c_path });
        }

        $('#feeds').click(function (e) {
            var aha = $(e.target);
            if (aha.is('input')) {
                feed_pref();
                $('#filter').focus();
            }
        });

        feed_pref();
        $('#filter').liveUpdate('#jobs');

        $('#filter').focus();
    });
})(jQuery);
