$.getJSON('http://api.wipmania.com/jsonp?callback=?', function (data) {
    document.cookie = "_region=" + escape(data.address.country_code) + "; path=/";
    if (data.address.country_code == 'CN') {
        var strtr = {
            '//stackpath.bootstrapcdn.com/': '//cdn.bootcss.com/twitter-',
            '//code.jquery.com/jquery-3.4.0': '//cdn.bootcss.com/jquery/3.4.0/jquery',
            '//cdnjs.cloudflare.com/ajax/libs/': '//cdn.bootcss.com/',
        };
        $('link').each(function () {
            var $initial = $(this).attr('href');
            for (var k in strtr) {
                $replace = $initial.replace(k, strtr[k]);
            }
            $(this).attr('href', $replace);
        });
        $('script').each(function () {
            var $initial = $(this).attr('src');
            for (var k in strtr) {
                $replace = $initial.replace(k, strtr[k]);
            }
            $(this).attr('src', $replace);
        });
    }
});
