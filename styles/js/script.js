$(document).ready(function(){  
    $('.img-tovar').elevateZoom({ zoomWindowWidth:300, zoomWindowHeight:200 });
    
    $('input[type=checkbox]').click(function(){
        var b = $(this).val();
        if(b == 'off'){
            $(this).val('on');
        }else{
            $(this).val('off');
        }
    });
    
    $('#view_modal').click(function(){
        $('.modal').attr('style', 'display: block;');
    });
    
    $('.close_modal').click(function(){
        $('.modal').attr('style', 'display: none;');
    });
});