/**
 * Created by gkratz on 08-06-17.
 */
jQuery(document).ready(function($){
    var selector = ".mcf_form_layout input, .mcf_form_layout textarea";
    $(selector).each(function(){
        var name = $(this).attr("name");
        if($(this).val().length > 0) {
            $("label[for=" + name + "]").addClass("active");
        }
    });

    $(selector).focus(function(){
        var name = $(this).attr("name");
        $("label[for=" + name + "]").addClass("active");
        $(this).prev("i").addClass("active");
    });

    $(selector).blur(function(){
        var name = $(this).attr("name");
        $(this).prev("i").removeClass("active");
        if($(this).val().length < 1) {
            $("label[for=" + name + "]").removeClass("active");
        }
    });

    textareaAutosize();
});

function textareaAutosize() {
    var textarea = document.querySelector('textarea');

    if(textarea !== null) {
        textarea.addEventListener('keydown', autosize);

        function autosize(){
            var el = this;
            setTimeout(function(){
                el.style.cssText = 'height:auto;padding: .8rem 0 1.6rem 0';
                // for box-sizing other than "content-box" use:
                // el.style.cssText = '-moz-box-sizing:content-box';
                el.style.cssText = 'height:' + el.scrollHeight + 'px';
            },0);
        }
    }
}
