(function ($) {

    var c_path = '/projects/jobs/';
    var c_expr = 3650; // in days
    var c_size = 84;

    $(function () {
        $('#jobs').click(function (e) {
            var aha = $(e.target);
            if (aha.is('a') && !aha.hasClass('visitJob')) {
                aha.parents('.hd').siblings('.bd').toggle();
                aha.parents('.jobItem').toggleClass('jobItemSelected');
                if (!aha.hasClass('viewed')) {
                    aha.addClass('viewed');
                    var track    = aha.attr('id');
                    var storage  = $.cookie('feedtrack');
                    var json_str = '';
                    if (storage == null) {
                        storage = [];
                        storage.push(track);
                        json_str = $.jSONToString({t: storage});
                    } else {
                        storage = $.toJSON(storage);
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
                        json_str = $.jSONToString({t: storage.t});
                    }
                    $.cookie('feedtrack', json_str, { expires: c_expr, path: c_path });
                }
                aha.blur();
                return false;
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
            $.cookie('feedpref', $.jSONToString(pref), { expires: c_expr, path: c_path });
        }

        $('#feeds').click(function (e) {
            var aha = $(e.target);
            if (aha.is('input')) {
                feed_pref();
            }
        });

        feed_pref();
        $('#filter').liveUpdate('#jobs');
    });
})(jQuery);
