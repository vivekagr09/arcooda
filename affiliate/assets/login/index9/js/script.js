
var lang_imgEle=document.getElementById("lang_flag");
var lang_nameEle=document.getElementById("lang_name");
var lang_drop_down=document.getElementById("lang_drop_down");
var cross_btn=document.getElementById("cross_btn");

function cng_lang(el){
    var img_src=el.querySelector("img").getAttribute("src");
    var lang_text=el.innerText;
    var id = el.id;
    lang_imgEle.src=img_src;
    lang_nameEle.innerHTML=lang_text;
    lang_drop_down.classList.add("d-none");
    change_language(id);
}
function toggle_lang(){
    lang_drop_down.classList.toggle("d-none");
}

function cng_page_sec(sec_name){
    document.querySelectorAll("[data-page-section]").forEach((el)=>{
        el.classList.add("d-none");
    });
    document.querySelector(`[data-page-section="${sec_name}"]`).classList.remove("d-none");
}
function show_section(sec_name){
    document.querySelectorAll("[data-view-section]").forEach((el)=>{
        el.classList.add("d-none");
    });
    document.querySelector(`[data-view-section="${sec_name}"]`).classList.remove("d-none");
    cross_btn.classList.remove("d-none");
}
function hide_cross(el){
    el.classList.add("d-none");
}