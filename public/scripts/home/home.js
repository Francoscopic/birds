

function article_click_home(){

    const i = '1',
        n = $('.vw-anchor');

    $(n).on('click',function(){
        var e = $(this).siblings("#page-assistant");
        !function(n,e){
            $.post("pages/in/depends/profiles/article/verbs.php",{views:i,note_id:n,viewer_id:e},function(){}).fail(function(i,n,e){console.error(e)})
        }(e.attr("pid"), e.attr("uid"))
    })
}
            
            
$(document).ready(function(){
    article_click_home();
});