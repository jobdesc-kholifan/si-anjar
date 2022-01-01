const BoxDashboardOptions = function(options, box) {
    this.__box = box;

    this.url = this.__box.$.data('url') !== undefined ? this.__box.$.data('url') : null;
    if(options.url !== undefined)
        this.url = options.url;
};

const BoxDashboard = function(selector, options = {}) {
    this.selectors = {
        parent: selector,
        value: '[data-name=value]',
    };
    this.$ = $(this.selectors.parent);
    this.options = new BoxDashboardOptions(options, this);

    this.$value = this.$.find(this.selectors.value);
};

BoxDashboard.prototype.init = function() {
    ServiceAjax.get(this.options.url)
        .done((res) => {
            if(res.result) {
                this.$value.html(res.data.value);
            }
        });
};
