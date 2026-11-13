(function ($, bootstrap) {
    'use strict';

    if (!$ || !bootstrap) {
        throw new Error('Bootstrap 5 jQuery compatibility requires jQuery and Bootstrap.');
    }

    function installPlugin(name, Component) {
        $.fn[name] = function (option) {
            const args = Array.prototype.slice.call(arguments, 1);

            return this.each(function () {
                const instance = Component.getOrCreateInstance(
                    this,
                    typeof option === 'string'
                        ? { toggle: false }
                        : typeof option === 'object' && option !== null ? option : undefined
                );

                if (typeof option === 'string') {
                    if (typeof instance[option] !== 'function' || option.startsWith('_')) {
                        throw new TypeError('Unknown Bootstrap ' + name + ' method: ' + option);
                    }
                    instance[option].apply(instance, args);
                } else if (name === 'modal') {
                    instance.show();
                }
            });
        };
    }

    installPlugin('modal', bootstrap.Modal);
    installPlugin('collapse', bootstrap.Collapse);
    installPlugin('tooltip', bootstrap.Tooltip);

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
            bootstrap.Tooltip.getOrCreateInstance(element);
        });
    });
})(window.jQuery, window.bootstrap);
