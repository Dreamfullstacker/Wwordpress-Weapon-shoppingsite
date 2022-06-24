;(function($){
    "use strict";

    $(document).ready(function(){
        
        // show countdown based on server timzone
        var finalTime, daysTime, hours, minutes, second;
        $('.ht-product-countdown').each(function() {
            var $this       = $(this),
                finalDate   = $(this).data('countdown'),
                customlavel = $(this).data('customlavel');

            $this.countdown(finalDate, function(event) {
                $this.html(event.strftime('<div class="cd-single"><div class="cd-single-inner"><h3>%D</h3><p>'+customlavel.daytxt+'</p></div></div><div class="cd-single"><div class="cd-single-inner"><h3>%H</h3><p>'+customlavel.hourtxt+'</p></div></div><div class="cd-single"><div class="cd-single-inner"><h3>%M</h3><p>'+customlavel.minutestxt+'</p></div></div><div class="cd-single"><div class="cd-single-inner"><h3>%S</h3><p>'+customlavel.secondstxt+'</p></div></div>'));
            });
        });

    });

})(jQuery);