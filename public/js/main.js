var Spiner = {

    show: function(){
        $("body").append('<div class="spinner"><span uk-spinner="ratio: 4.5"></span></div>');
    },

    hide: function() {
        $(".spinner").remove();
    }
}