var _af_url = '<?= base_url() ?>';
var _af_my_url = window.location.host;
var af_script = '<?= $script ?>';

var getQueryString = function ( field ) {
	var href = window.location.href;
	var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
	var string = reg.exec(href);
	return string ? string[1] : null;
};

function removeQString(key) {
    var urlValue=document.location.href;
    var searchUrl=location.search;
    
    if(key!="") {
        oldValue = getQueryString(key);
        removeVal=key+"="+oldValue;
        if(searchUrl.indexOf('?'+removeVal+'&')!= "-1") {
            urlValue=urlValue.replace('?'+removeVal+'&','?');
        }
        else if(searchUrl.indexOf('&'+removeVal+'&')!= "-1") {
            urlValue=urlValue.replace('&'+removeVal+'&','&');
        }
        else if(searchUrl.indexOf('?'+removeVal)!= "-1") {
            urlValue=urlValue.replace('?'+removeVal,'');
        }
        else if(searchUrl.indexOf('&'+removeVal)!= "-1") {
            urlValue=urlValue.replace('&'+removeVal,'');
        }
    }
    else {
        var searchUrl=location.search;
        urlValue=urlValue.replace(searchUrl,'');
    }
    history.pushState({state:1, rand: Math.random()}, '', urlValue);
}

function setCookie(c, cv, ex) {
    var d = new Date();
    d.setTime(d.getTime() + (ex*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = c + "=" + cv + ";" + expires + ";path=/";
}

function getCookie(cname) {
   console.log(document.cookie);
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

if (typeof aff_external_cookies_duration === 'undefined') {
    aff_external_cookies_duration = 30;
}

if (getQueryString('af_id')) {
    setCookie('af_id', getQueryString('af_id'), aff_external_cookies_duration);
    removeQString('af_id');
}

function af_call_api(url,data) 
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {};
    xhttp.open("POST", url, true);
    xhttp.setRequestHeader("Content-type", "application/json");

    
    data['current_page_url'] = btoa(window.location.href);
    data['base_url']    = btoa(_af_my_url);
    data['af_id']       = getCookie("af_id");
    data['script_name'] = af_script;

    var p = Object.keys(data).map(key => key + '=' + data[key]).join('&');
    xhttp.send(p);
}

var AffTracker = {
    customFields:[],
    productCampaignClick: function (product_id) {
        af_call_api(_af_url + 'Productsales/addProductCampaignClick', {
            "product_id"  : product_id,
            "customFields" : JSON.stringify(this.customFields),
        })
    },
	productClick: function (product_id) {
        af_call_api(_af_url + 'integration/addClick',{
            "product_id"  : product_id,
            "customFields" : JSON.stringify(this.customFields),
        })
    },
    createAction: function (actionCode) 
    {
        af_call_api(_af_url + 'integration/addClick',{
            "actionCode" : actionCode,
            "customFields" : JSON.stringify(this.customFields),
        })
    },
    setWebsiteUrl(url) {
        _af_my_url = url;
    },
    setData: function(key,value){
        this.customFields.push({
            key:key,
            value:value,
        })
    },
    add_order: function (data) {
		af_call_api(_af_url + 'integration/addOrder',{
            "order_id"       : data['order_id'],
            "order_currency" : data['order_currency'],
            "order_total"    : data['order_total'],
            "product_ids"    : data['product_ids'],
            "customFields"   : JSON.stringify(this.customFields),
		})
	},
    stop_recurring: function (order_id) {
        af_call_api(_af_url + 'integration/stopRecurring',{
            "order_id" : order_id,
        })
    },
    generalClick:function (page_name) {
        af_call_api(_af_url + 'integration/addClick',{
            "page_name"  : page_name,
            "customFields" : JSON.stringify(this.customFields),
        })
    },
}

var productCampignBtns = document.querySelectorAll("[AffTrackerProcutCampaign]");

productCampignBtns.forEach(function(btn){  

    btn.style.cursor = 'pointer';

    btn.onclick = function () {
        location.href = _af_url + 'product-campaign/'+btn.getAttribute('AffTrackerProcutCampaign')+'/'+getCookie("af_id");
    };
    
    AffTracker.productCampaignClick(btn.getAttribute('AffTrackerProcutCampaign'));
});