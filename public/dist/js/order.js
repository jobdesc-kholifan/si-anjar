const OrderData = function() {
    this.order_date = new Date();
    this.location_id = 0;
    this.location_name = '-';
    this.customer_id = 0;
    this.customer_name = null;
    this.customer_unique_code = null;
    this.customer_phone = null;
    this.customer_email = null;
    this.customer_address = null;
    this.admin_id = 0;
    this.admin_name = '-';
    this.admin_phone = '';
    this.admin_email = '';
    this.admin_address = '';
    this.payment_method_id = 0;
    this.payment_method_name = null;
    this.grand_total = 0;
    this.items = [];
};

const OrderDataItem = function() {
    this.product = {
        id: 0,
        code: 0,
        name: null,
        price: 0,
        uom: {
            id: 0,
            name: null,
        },
        package: {
            id: null,
            name: null,
        }
    };
    this.qty = 0;
    this.total = 0;
};

const OrderItems = function(order) {
    this.__order = order;

    this.form = $('<tr>', {'data-id': 'null'}).append(
        $('<td>').html(
            $('<input>', {
                type: 'text',
                class: 'form-control',
                placeholder: 'Kode Barang',
                maxlength: '100',
                'data-action': 'input'
            })
        ),
        $('<td>').html(
            $('<select>', {
                class: 'form-control',
                'data-action': 'select'
            })
        ),
        $('<td>', {class: 'text-right', 'data-action': 'price'}).html('-'),
        $('<td>', {class: 'text-center', 'data-action': 'uom'}).html('-'),
        $('<td>', {class: 'text-center'}).html(
            $('<div>', {class: 'btn-group', 'data-action': 'qty'}).append(
                $('<button>', {type: 'button', class: 'btn btn-primary btn-sm', 'data-value': -1, disabled: 'disabled'}).html(
                    $('<i>', {class: 'fa fa-minus-circle'})
                ),
                $('<input>', {
                    class: 'form-control text-center',
                    type: 'text',
                    placeholder: '0',
                    maxlength: '0',
                    disabled: 'disabled'
                }).css({width: '60px'}),
                $('<button>', {type: 'button', class: 'btn btn-primary btn-sm', 'data-value': 1, disabled: 'disabled'}).html(
                    $('<i>', {class: 'fa fa-plus-circle'})
                ),
            )
        ),
        $('<td>', {class: 'text-right', 'data-action': 'total'}).html('-'),
        $('<td>', {'data-action': 'actions'}).html('')
    );
}

OrderItems.prototype.addForm = function() {
    const $form = $(this.form.clone());
    $form.data('form', new FormItem($form, this.__order));
    $form.data('data', new OrderDataItem());

    this.$table.find('tbody').append($form);
    setTimeout(() => {
        if($form.data('form').inputItem.length > 0)
            $form.data('form').inputItem.focus();
    }, 500);
    return $form;
};

OrderItems.prototype.init = function() {
    this.$table = $(this.__order.selector.table);
    this.addForm();

    this.$table.find('tbody').children().each((i, item) => {
        const $item = $(item);
        const formItem = $item.data('form') !== undefined ? $item.data('form') : new FormItem($item, this.__order);
        const dataItem = $item.data('data') !== undefined ? $item.data('data') : new OrderDataItem();

        $item.data('form', formItem);
        $item.data('data', dataItem);
    });
};

const FormItem = function(element, order) {
    this.$ = element;
    this.__order = order;

    this.selectItem = $(this.$.find('[data-action=select]'));
    this.inputItem = $(this.$.find('[data-action=input]'));
    this.labelPrice = $(this.$.find('[data-action=price]'));
    this.labelUom = $(this.$.find('[data-action=uom]'));
    this.inputQty = $(this.$.find('[data-action=qty]'));
    this.labelTotal = $(this.$.find('[data-action=total]'));
    this.actions = $(this.$.find('[data-action=actions]'));


    this.init();
};

FormItem.prototype.init = function() {
    this.btnremove = $(this.actions.find('[data-action=remove-item]'));

    this.inputItem.donetyping((el, e) => {
        const keycode = (e.keyCode ? e.keyCode : e.which);
        if(keycode === 13 && this.inputItem.val() !== '')
            this.infoItem({code: this.inputItem.val()});
    });

    FormComponents.select2.init(this.selectItem, {
        allowClear: false,
        ajax: {
            url: this.__order.route.selectItem,
            data: (params) => {
                params.location_id = this.__order.orders.location_id;
                return params;
            }
        },
        placeholder: 'Pilih barang'
    });
    this.selectItem.change(() => {
        if(this.selectItem.val() !== null)
            this.infoItem({id: this.selectItem.val()});
        else {
            this.$.remove();
            this.__order.recap();
        }
    });
    this.btnremove.click(() => {
        this.$.remove();
        this.__order.recap();
    });

    this.initInputQty();
};

FormItem.prototype.infoItem = function(params) {
    const data = this.$.data('data');
    params.location_id = this.__order.orders.location_id;

    this.$.addClass('loading');
    ServiceAjax.get(this.__order.route.infoItem, {
        data: params,
        success: (res) => {
            this.$.removeClass('loading');

            if(res.result && res.data !== null) {
                if(data.product.id === 0)
                    this.__order.orderItems.addForm();

                data.product = res.data;
                data.product.package = {id: null, name: null};
                data.qty = 1;
                data.total = data.qty * data.product.price;

                this.$.data('data', data);
                this.inputQty.find('input, button').removeAttr('disabled');
                this.renderItem();
            }
        },
        error: () => {
            this.$.removeClass('loading');
        }
    });
};

FormItem.prototype.initInputQty = function() {
    this.inputQty.find('button').each((i, item) => {
        const $item = $(item);
        $item.click(() => {
            const data = this.$.data('data');
            const value = parseInt($item.data('value'));

            if(data.qty + value > 0)
                data.qty = data.qty + value;

            data.total = data.qty * data.product.price;

            this.$.data('data', data);
            this.renderItem();
        });
    });
    this.inputQty.find('input').donetyping(() => {
        const data = this.$.data('data');
        const value = parseInt(this.inputQty.find('input').val());

        if(value > 0)
            data.qty = value;
        else data.qty = 1;

        data.total = data.qty * data.product.price;

        this.$.data('data', data);
        this.renderItem();
    });
};

FormItem.prototype.renderItem = function() {
    const data = this.$.data('data');
    this.$.attr('data-id', `${data.product.package.id}-${data.product.id}`);

    this.inputItem.val(data.product.code);
    this.selectItem.append($('<option>', {value: data.product.id}).text(data.product.name));
    this.labelPrice.html(`Rp. ${$.number(data.product.price)}`);
    this.labelUom.html(data.product.uom.name);
    this.inputQty.find('input').val(data.qty);

    this.labelTotal.html(`Rp. ${$.number(data.total)}`);

    if(data.product.id !== 0)
        this.actions.html(
            $('<button>', {
                type: 'button',
                class: 'btn btn-outline-danger btn-xs',
                'data-action': 'remove-item',
            }).html($('<i>', {class: 'fa fa-times-circle'})).click(() => {
                this.$.remove();
                this.__order.recap();
            })
        );

    this.__order.recap();
};

const OrderPackage = function(order) {
    this.__order = order;
    this.$ = $(order.$.find(order.selector.package).get(0));

    this.$inputSearch = $(order.$.find('[data-action=search-package]'));
    this.$wrapper = $(order.$.find('[data-action=wrapper-package]'));
    this.$packageItem = $('<div>', {class: 'col-sm-6'}).append(
        $('<div>', {class: 'border bg-light rounded py-2 px-3 d-flex justify-content-between align-items-center'}).append(
            $('<div>', {class: ''}).append(
                $('<h5>', {class: 'm-0', 'data-action': 'package-name'}).html('-').css({
                    width: '100%',
                    whiteSpace: 'nowrap',
                    textOverflow: 'ellipsis'
                }),
                $('<small>', {'data-action': 'package-description'}).html('-').css({
                    width: '100%',
                    whiteSpace: 'nowrap',
                    textOverflow: 'ellipsis'
                })
            ),
            $('<div>', {class: 'btn-group', 'data-action': 'qty'}).append(
                $('<button>', {type: 'button', class: 'btn btn-primary btn-xs', 'data-value': '-1'}).append(
                    $('<i>', {class: 'fa fa-minus-circle'}),
                ),
                $('<input>', {class: 'text-center', 'data-action': 'input-qty', 'placeholder': 0, 'onkeypress': 'return Helpers.isNumberKey(event)'}).css({width: 60}),
                $('<button>', {type: 'button', class: 'btn btn-primary btn-xs', 'data-value': '1'}).append(
                    $('<i>', {class: 'fa fa-plus-circle'}),
                ),
            )
        )
    );
};

OrderPackage.prototype.init = function() {
    this.update();
    this.$inputSearch.donetyping(() => this.update());
};

OrderPackage.prototype.update = function() {
    ServiceAjax.get(this.__order.route.searchPackage, {
        data: {
            location_id: this.__order.orders.location_id,
            search_value: this.$inputSearch.val(),
        },
        success: (res) => {
            this.$wrapper.empty();
            if(res.result) {
                if(res.data.length > 0) {
                    res.data.forEach((data) => {
                        const $item = this.$packageItem.clone();
                        const packageItem = new OrderPackageItems($item, this.__order);
                        packageItem.init();
                        packageItem.update(data);

                        $item.data('packageItem', packageItem);
                        this.$wrapper.append($item);
                    });
                } else this.$wrapper.append($('<small>', {class: 'col-12'}).html('Tidak ditemukan paket tersedia'));
            }
        },
    })
};

const OrderPackageItems = function(selector, order) {
    this.__order = order;
    this.$ = $(selector);

    this.$name = $(this.$.find('[data-action=package-name]').get(0));
    this.$description = $(this.$.find('[data-action=package-description]').get(0));
    this.$qty = $(this.$.find('[data-action=qty]').get(0));
};

OrderPackageItems.prototype.init = function() {
    this.$qty.find('input').donetyping((el) => {
        const $el = $(el);
        $el.data('value', $el.val());
        this.load($el);
    });
    this.$qty.find('button').click((e) => {
        const $button = $(e.currentTarget);
        this.load($button);
    });
};

OrderPackageItems.prototype.load = function($el) {
    ServiceAjax.get(this.__order.route.infoPackage,{
        data: {id: $el.parent().data('id') },
        success: (res) => {
            if(res.result) {
                this.__order.orderItems.$table.find('tbody').children().last().remove();
                res.data.items.forEach((item) => {
                    let $form = $(this.__order.orderItems.$table.find(`tr[data-id=${res.data.id}-${item.item.id}]`));
                    if($form.length === 0)
                        $form = this.__order.orderItems.addForm();

                    const form = $form.data('form');
                    const data = $form.data('data');

                    if(data.qty > 0 || (data.qty === 0 && parseInt($el.data('value')) === 1)) {
                        data.product = item.item;
                        data.product.package = {id: res.data.id, name: res.data.package_name};

                        if($el.is('button'))
                            data.qty += parseInt($el.data('value'));

                        else if($el.is('input'))
                            data.qty = parseInt($el.val());

                        this.$qty.find('input').val(data.qty);

                        data.total = data.qty * item.item.price;

                        $form.data('data', data);
                        form.inputQty.find('input, button').removeAttr('disabled');
                        form.renderItem();
                    }

                    if(data.qty === 0 || (data.qty === 1 && parseInt($el.data('value')) === -1)) {
                        const $el = $(this.__order.orderItems.$table.find(`tr[data-id=${res.data.id}-${item.item.id}]`));
                        $el.remove();
                        $form.remove();
                        this.$qty.find('input').val(null);
                    }
                });

                if(this.__order.orderItems.$table.find('tr[data-id=null]').length === 0)
                    this.__order.orderItems.addForm();
            }
        }
    })
}

OrderPackageItems.prototype.update = function(data) {
    this.$name.html(data.package_name);
    this.$description.html(data.description === null ? '-' : data.description);
    this.$qty.data('id', data.id);
};

const Order = function(selector) {
    this.$ = $(selector);

    this.selector = {
        table: '[data-action=table]',
        formLocation: '[data-action=location]',
        formDate: '[data-action=date]',
        formAdmin: '[data-action=admin]',
        total: '[data-action=subtotal]',
        totalQty: '[data-action=total-qty]',
        grandTotal: '[data-action=grand-total]',
        buttonPayment: '[data-action=button-payment]',
        package: '[data-action=package]'
    };

    this.route = {
        selectItem: null,
        infoItem: null,
        searchPackage: null,
        infoPackage: null,
    };

    this.$selectLocation = this.$.find(this.selector.formLocation);
    this.$selectAdmin = this.$.find(this.selector.formAdmin);
    this.$inputDate = this.$.find(this.selector.formDate);
    this.$buttonPayment = this.$.find(this.selector.buttonPayment);
    this.$package = this.$.find(this.selector.package);

    this.orders = new OrderData();
    this.orderItems = new OrderItems(this);
    this.package = new OrderPackage(this);
};

Order.prototype.init = function() {
    this.orderItems.init();

    this.orders.location_id = this.$selectLocation.val();
    this.orders.location_name = this.$selectLocation.find('option:selected').text();

    this.orders.admin_id = this.$selectAdmin.val();
    this.orders.admin_name = this.$selectAdmin.find('option:selected').text();

    this.orders.order_date = this.$inputDate.val();

    this.$selectLocation.change(() => {
        this.orders.location_id = this.$selectLocation.val();
        this.orders.location_name = this.$selectLocation.find('option:selected').text();
        this.orderItems.$table.find('tbody').empty();
        this.orderItems.addForm();

        this.recap();
        this.package.init();
    });

    let clockTimeout;
    this.updateDate = () => {
        this.$inputDate.val(moment(new Date()).format('DD/MM/YYYY HH:mm'));
        clockTimeout = setTimeout(() => this.updateDate(), 1000);
    };
    this.updateDate();

    this.$inputDate.on('blur', () => {
        clearTimeout(clockTimeout);
        this.orderItems.$table.find('tbody').children().last().data('form').inputItem.focus();
    });

    this.package.init();
};

Order.prototype.recap = function() {
    let total = 0;
    const items = [];
    this.orderItems.$table.find('tbody').children().each((i, item) => {
        const $item = $(item);
        const data = $item.data('data');

        if(data.product.id) {
            items.push(data);

            total += data.total;
        }
    });

    if(items.length > 0)
        this.$buttonPayment.removeAttr('disabled');

    else this.$buttonPayment.attr('disabled', 'disabled');

    this.orders.grand_total = total;
    this.orders.items = items;

    this.$.find(this.selector.total).html(`Rp. ${$.number(total)}`);

    this.$.find(this.selector.totalQty).html('Total');
    if(this.orders.items.length > 0)
        this.$.find(this.selector.totalQty).html(`Total (${this.orders.items.length} Barang)`);

    this.$.find(this.selector.grandTotal).html(`Rp. ${$.number(total)}`);
};
