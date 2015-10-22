function debounce(func, timeout){
    var enable = true;
    return function (){
        if (!enable){
            return false;
        }

        enable = false;
        setTimeout(function(){
            enable = true;
        }, timeout || 500);

        return func.apply(this, arguments);
    };
}