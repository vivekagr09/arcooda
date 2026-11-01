function getCookie(cname) {
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

var cc = getCookie('af_id');
var id = cc.toString().split("-");
var iid =  (typeof id[1] == 'undefined' ? 0 : atob(id[1]));
var tmp = iid.toString().split("-")
var afID = typeof tmp[0] == 'string' ? tmp[0] : 0;


document.addEventListener('DOMContentLoaded', function(){
if(parseInt(afID)){
    var nFilter = document.createElement('div');
    nFilter.className = 'affiliate-id-info';
    nFilter.innerHTML = af_df_setting.text.replace("{id}",afID);

    if( af_df_setting.position =='left'){
        nFilter.setAttribute("style", "background: #E10E49;position: fixed;left: 0;width: auto;z-index: 999;text-align: center;color: #fff;padding: 10px;font-family: inherit;top: 50%;");
    } else if( af_df_setting.position =='right'){
        nFilter.setAttribute("style", "background: #E10E49;position: fixed;right: 0;width: auto;z-index: 999;text-align: center;color: #fff;padding: 10px;font-family: inherit;top: 50%;");
    } else if( af_df_setting.position =='top'){
        nFilter.setAttribute("style", "background: #E10E49;position: fixed;top: 0;width: 100%;z-index: 999;text-align: center;color: #fff;padding: 10px;font-family: inherit;");
    } else if( af_df_setting.position =='top-right'){
        nFilter.setAttribute("style", "background: #E10E49;position: fixed;right: 0;width: auto;z-index: 999;text-align: center;color: #fff;padding: 10px;font-family: inherit;top: 0;");
    } else if( af_df_setting.position =='top-left'){
        nFilter.setAttribute("style", "background: #E10E49;position: fixed;left: 0;width: auto;z-index: 999;text-align: center;color: #fff;padding: 10px;font-family: inherit;top: 0;");
    } else if( af_df_setting.position =='bottom-left'){
        nFilter.setAttribute("style", "background: #E10E49;position: fixed;left: 0;width: auto;z-index: 999;text-align: center;color: #fff;padding: 10px;font-family: inherit;bottom: 0;");
    } else if( af_df_setting.position =='bottom-right'){
        nFilter.setAttribute("style", "background: #E10E49;position: fixed;right: 0;width: auto;z-index: 999;text-align: center;color: #fff;padding: 10px;font-family: inherit;bottom: 0;");
    } else {
        nFilter.setAttribute("style", "background: #E10E49;position: fixed;bottom: 0; left:0; width: 100%;z-index: 999;text-align: center;color: #fff;padding: 10px;font-family: inherit;");
    }
    document.body.appendChild(nFilter)
}

}, false);
