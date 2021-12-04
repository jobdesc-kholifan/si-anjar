function unitIDR(value) {
    let idr 	= parseInt(value);
    if(idr === 0) {
        return 0;
    } else if(idr < 1000000) {
        return $.number(idr / 1000) + 'Rb';
    } else if(idr < 1000000000) {
        return $.number(idr / 1000000) + "Jt";
    } else if(idr < 1000000000000) {
        return $.number(idr / 1000000000) + "M";
    } else if(idr < 1000000000000000) {
        return $.number(idr / 1000000000000) + "B";
    }
}
const ChartSales = function(selector, options) {
    this.$ = $(selector);
    this.$canvas = this.$.find('canvas');
    this.context = this.$canvas.get(0).getContext('2d');

    this.options = options;

    this.chart = null;
};
ChartSales.prototype.update = function() {

    this.$.addClass('loading');
    ServiceAjax.get(this.options.source, {
        data: this.options.params !== undefined ? this.options.params : {},
        success: (res) => {
            this.$.removeClass('loading');
            if(res.result) {
                this.chart.data = res.data;
                this.chart.update();
            }
        },
        error: () => {
            this.$.removeClass('loading');
    }
    })
};
ChartSales.prototype.render = function() {
    this.chart = new Chart(this.context, {
        type: 'line',
        options: {
            tooltips: {
                callbacks: {
                    label: function(tooltip, data) {
                        return `${tooltip.label}: ${$.number(tooltip.value)}`;
                    }
                }
            },
            maintainAspectRatio : false,
            responsive : true,
            legend: {
                display: true
            },
            scales: {
                xAxes: [{
                    gridLines : {
                        display : false,
                    }
                }],
                yAxes: [{
                    gridLines : {
                        display : false,
                    },
                    ticks: {
                        callback: function(value, index, values) {
                            return unitIDR(value);
                        }
                    }
                }]
            },
        },
    });

    this.update();
};

const RecapDataOptions = function(recap, options) {
    this.url = recap.$.data('url');
    if(options !== undefined && options.url !== undefined)
        this.url = options.url;
};

const RecapData = function(selector, options) {
    this.$ = $(selector);
    this.options = new RecapDataOptions(this, options);

    this.$value = $(this.$.find('[data-action=value]'));
};

RecapData.prototype.init = function() {
    console.log(this.options.url);
    if(this.options.url !== undefined) {
        this.$.addClass('loading');
        ServiceAjax.get(this.options.url, {
            success: (res) => {
                this.$.removeClass('loading');
                if(res.result) {
                    this.$value.html(res.data.value);
                }
            },
            error: () => {
                this.$.removeClass('loading');
            }
        });
    }
};
