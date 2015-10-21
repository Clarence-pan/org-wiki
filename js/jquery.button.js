(function($, window){
    $.fn.button = function(action){
        switch (action){
            case ':isLoading':
                return !!this.data('button-isLoading');
            case 'loading':
                if (!this.data('button-isLoading')){
                    this.data('button-isLoading', true);

                    this.data('button-originalText', this.text());
                    this.text(this.data('loading'));
                }
                return this;
            case 'restore':
                if (this.data('button-isLoading')){
                    this.data('button-isLoading', false);

                    this.text(this.data('button-originalText'));
                }
                return this;
            default :
                console.error("Invalid action: " + action + ' %o', new Error('Invalid action: '+ action));
                return this;
        }
    };
})(window.jQuery, window);