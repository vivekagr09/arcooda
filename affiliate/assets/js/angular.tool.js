var app = angular.module('affiliatePro', []);

app.config(['$httpProvider', function($httpProvider) {
    $httpProvider.interceptors.push(function($q) {
        return {
            'responseError': function(rejection){
                var defer = $q.defer();
                if(rejection.status == 401){
                    $("#modal-auth").modal("show");
                }
                defer.reject(rejection);
                return defer.promise;
            }
        };
    });
}]);

function handle_res(json){
    $container = $("#form_plan");
    $container.find(".is-invalid").removeClass("is-invalid");
    $container.find("span.invalid-feedback").remove();
    
    if(json['reload']){
        window.location.reload();
    }

    if(json['location']){
        window.location.href = json['location'];
    }

    if(json['errors']){
        $.each(json['errors'], function(i,j){
            $ele = $container.find('[name="'+ i +'"]');
            if($ele){
                $ele.addClass("is-invalid");
                if($ele.parent(".input-group").length){
                    $ele.parent(".input-group").after("<span class='invalid-feedback'>"+ j +"</span>");
                } else{
                    $ele.after("<span class='invalid-feedback'>"+ j +"</span>");
                }
            }
        })
    }
}

function ngCall($http, url, options, callback,before, after,errorCallback) {
    var default_args = {
        http : $http,
        postData : {}, 
        httpMethod : 'POST', 
        callDataType : "json",
    }

    $.extend(default_args,options);
    if(before){ before(); }

    default_args['postData']['csrf-token'] = $('[name="csrf-token"]').attr("content");
    var query_string = '';
    if(default_args['httpMethod'] == 'GET'){
        query_string = '?' + jQuery.param(default_args['postData']);
    }

    $http({
        method: default_args['httpMethod'],
        responseType: default_args['callDataType'],
        url: window.affiliatePro.base_url +'/'+ url + query_string,
        data: $.param(default_args['postData']),
        crossDomain: true,
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
    })
    .then(function(response){
        if(after) { after(); }
        callback(response.data); 
    }, function(e){
        if(e.status != 200 && typeof errorCallback == 'function'){
            errorCallback(e.data); 
        }
        if(after) { after(); }
    });
}
