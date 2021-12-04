const BoxList = function(history) {
    this.__history = history;
    this.$ = $('<div>', {class: 'box-list'});
};

BoxList.prototype.init = function() {
    this.__history.$.append(this.$);
};

const BoxItem = function(selector, history) {
    this.__history = history;
    this.$ = $(selector);
    this.dataItem = {};

    this.$title = $(this.$.find('.item-detail-title').get(0));
    this.$date = $(this.$.find('.item-info-date').get(0));
    this.$time = $(this.$.find('.item-info-time').get(0));
    this.$desc = $(this.$.find('.item-detail-desc').get(0));
    this.$amount = $(this.$.find('.text-amount').get(0));
    this.$saldo = $(this.$.find('.item-info-saldo').get(0));
    this.$payment = $(this.$.find('.item-payment').get(0));
};
BoxItem.prototype.data = function(dataItem) {
    if(dataItem !== undefined)
        this.dataItem = dataItem;

    else return this.dataItem;
};
BoxItem.prototype.update = function() {
    this.$title.html(this.dataItem.title);
    this.$date.html(this.dataItem.tr_date);
    this.$time.html(this.dataItem.tr_time);
    this.$desc.html(this.dataItem.description);
    this.$amount.html(`${this.dataItem.calc_value > 0 ? '+' : '-'} Rp. ${$.number(this.dataItem.nominal)}`);
    this.$payment.html(this.dataItem.payment_method.name);

    this.__history.saldo += this.dataItem.nominal * this.dataItem.calc_value;
    this.$saldo.html(`Rp. ${$.number(this.__history.saldo)}`);

    if(this.dataItem.calc_value > 0)
        this.$.addClass('box-primary');

    else this.$.addClass('box-danger');
};

const TransactionHistory = function(selector, options = {}) {
    this.$ = $(selector);
    this.options = options;

    this.$boxItem = $('<div>', {class: 'box-item'}).append(
        $('<div>', {class: 'item-detail'}).append(
            $('<div>', {class: 'd-flex align-items-center'}).append(
                $('<h5>', {class: 'item-detail-title'}).html('-'),
            ),
            $('<div>', {class: 'item-detail-info'}).append(
                $('<div>', {class: 'item-info border-right pr-2 mr-2'}).append(
                    'Tanggal : ',
                    $('<span>', {class: 'item-info-bold item-info-date'}).html('-')
                ),
                $('<div>', {class: 'item-info border-right pr-2 mr-2'}).append(
                    'Pukul : ',
                    $('<span>', {class: 'item-info-bold item-info-time'}).html('-')
                ),
            ),
            $('<div>', {class: 'item-detail-desc'}).html('-'),
        ),
        $('<div>', {class: 'item-amount text-right'}).append(
            $('<div>', {class: 'badge bg-success item-payment'}).html('-'),
            $('<div>', {class: 'text-amount'}).html('Rp. 0'),
            $('<div>', {class: 'item-info'}).append(
                'Sisa Saldo : ',
                $('<span>', {class: 'item-info-bold item-info-saldo'}).html('Rp. 0'),
            )
        )
    );
    this.$filterType = $(this.$.find('.filter-type'));

    this.saldo = options !== undefined && options.saldo !== undefined ? options.saldo : 0;
    this.boxList = new BoxList(this);
    this.params = {};
};
TransactionHistory.prototype.init = function() {
    this.boxList.init();
    this.load();

    this.$filterType.each((i, item) => {
        const $item = $(item);
        $item.children().each((j, child) => {
            const $child = $(child);
            console.log($child);
            $child.click(() => {
                $($item.find('.btn-primary')).removeClass('btn-primary').addClass('btn-outline-primary');
                $child.removeClass('btn-outline-primary').addClass('btn-primary');
                this.params.tr_type = $child.data('value');
                this.load();
            })
        })
    });
};

TransactionHistory.prototype.load = function() {
    ServiceAjax.get(this.options.route, {
        data: this.params,
        success: (res) => {
            if(res.result) {
                this.saldo = 0;

                this.boxList.$.empty();
                if(res.data.history.length > 0) {
                    res.data.history.forEach((data) => {
                        const $item = this.$boxItem.clone();
                        const boxItem = new BoxItem($item, this);
                        boxItem.data(data);
                        boxItem.update();
                        $item.data('boxItem', boxItem);

                        this.boxList.$.prepend($item);
                    });
                } else {
                    this.boxList.$.append($('<h4>', {class: 'text-center'}).html('Tidak ada transaksi ditemukan'));
                }
            }
        },
        error: function() {
            AlertNotif.adminlte.error(DBMessage.ERROR_SYSTEM_MESSAGE, {
                title: DBMessage.ERROR_SYSTEM_TITLE,
            })
        }
    });
};
