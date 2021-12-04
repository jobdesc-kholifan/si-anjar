+function($) {
    "use strict";

    $.fn.donetyping = function(callback,timeout){
        timeout = timeout || 500; // 1 second default timeout
        var timeoutReference,
            doneTyping = function(el, event){
                if (!timeoutReference) return;
                timeoutReference = null;
                callback(el, event);
            };
        return this.each(function(i,el){
            var $el = $(el);
            // Chrome Fix (Use keyup over keypress to detect backspace)
            // thank you @palerdot
            $el.is(':input') && $el.on('keyup keypress paste',function(e){
                // This catches the backspace button in chrome, but also prevents
                // the event from triggering too preemptively. Without this line,
                // using tab/shift+tab will make the focused element fire the callback.
                if (e.type==='keyup' && e.keyCode!==8) return;

                // Check if timeout has been set. If it has, "reset" the clock and
                // start over again.
                if (timeoutReference) clearTimeout(timeoutReference);
                timeoutReference = setTimeout(function(){
                    // if we made it here, our timeout has elapsed. Fire the
                    // callback
                    doneTyping(el, e);
                }, timeout);
            }).on('blur autocomplete',function(e){
                if (timeoutReference) clearTimeout(timeoutReference);
                timeoutReference = setTimeout(function(){
                    // if we made it here, our timeout has elapsed. Fire the
                    // callback
                    doneTyping(el, e);
                }, timeout);
            });
        });
    };
}(jQuery);
