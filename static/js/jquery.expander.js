(function ($, window) {
    /**
     * make expander of something
     * @param defaultExpand
     * @returns {*}
     */
    $.fn.initExpander = function (defaultExpand) {
        return initExpander(this, defaultExpand);
    };

    /**
     * init expander
     * @param triggers $('dt,hX')
     * @param defaultExpanded bool initial expanded
     * @events expand, shrink, toggle-expand
     */
    function initExpander(triggers, defaultExpanded) {
        triggers.each(function (i, e) {
            var $trigger = $(e);

            $trigger.on('expand', function () {
                var $this = $(this);
                findTarget($this).show();
                $this.addClass('expanded');
            });

            $trigger.on('shrink', function () {
                var $this = $(this);
                findTarget($this).hide();
                $this.removeClass('expanded');
            });

            $trigger.on('toggle-expand', function () {
                var $this = $(this);
                $this.trigger(findTarget($this).is(':visible') ? 'shrink' : 'expand');
            });

            $trigger.on('click', function () {
                $(this).trigger('toggle-expand');
            });

            $trigger.addClass('expander');
        });

        if (!defaultExpanded) {
            triggers.trigger('shrink');
        } else {
            triggers.trigger('expand');
        }

        function findTarget($trigger){
            var tagName = $trigger[0].tagName;
            if (!tagName || !tagName.match(/^(dt|h\d*)/i)) {
                console.error("Unsupported tag: %o of %o!", tagName);
                return $('<div></div>');
            }
            return $trigger.nextUntil(tagName);
        }
    }

})(window.jQuery, window);