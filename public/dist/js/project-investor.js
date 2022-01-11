const ProjectInvestorOptions = function(options = {}) {
    this.route = {
        investor: options.route !== undefined && options.route.investor !== undefined ? options.route.investor : null,
        selectInvestor: options.route !== undefined && options.route.selectInvestor !== undefined ? options.route.selectInvestor : null,
    };
    this.projectValue = options.projectValue ? options.projectValue : 0;
    this.sharesValue = options.sharesValue ? options.sharesValue : 0;
    this.isDraft = false;
};

const DataProjectInvestor = function(value = {}) {
    this.id = value.id !== undefined ? value.id : 0;
    this.investor_id = value.investor_id  !== undefined ? value.investor_id : null;
    this.investor = value.investor !== undefined ? value.investor : null;
    this.shares_value = value.shares_value !== undefined ? value.shares_value : 0;
    this.investment_value = value.investment_value !== undefined ? value.investment_value : 0;
    this.shares_percentage = value.shares_percentage !== undefined ? value.shares_percentage : 0;
    this.deleted = false;
};

DataProjectInvestor.prototype.toJson = function() {
    return {
        id: this.id,
        investor_id: this.investor_id,
        shares_value: this.shares_value,
        investment_value: this.investment_value,
        shares_percentage: this.shares_percentage,
        deleted: this.deleted,
    };
}

const ProjectInvestorForm = function(element, form) {
    this.$ = element;
    this.__form = form;

    this.$labelNomor = $(this.$.find('[data-action=nomor]'));
    this.$inputNoKTP = $(this.$.find('[data-action=input-noktp]'));
    this.$selectInvestor = $(this.$.find('[data-action=select-investor]'));
    this.$nominalShares = $(this.$.find('[data-action=input-shares]'));
    this.$invesmentValue = $(this.$.find('[data-action=investment-value]'));
    this.$percentage = $(this.$.find('[data-action=percentage]'));
    this.$btnDelete = $(this.$.find('[data-action=btn-delete]'));
    this.$btnAdd = $(this.$.find('[data-action=btn-add]'));
};

ProjectInvestorForm.prototype.init = function() {
    FormComponents.select2.init(this.$selectInvestor);
    FormComponents.number.init(this.$nominalShares);

    this.$nominalShares.on('keydown keyup keypress', () => {
        let perShares = this.__form.options.sharesValue;
        let nominal = parseInt(this.$nominalShares.val());
        let shares = Math.round((nominal / perShares) * 100)/100;
        let percentage = Math.round((nominal/this.__form.options.projectValue * 100) * 100) / 100;

        this.$invesmentValue.html(`${shares} Lembar`);
        this.$percentage.html(`${percentage} %`);

        this.$.data('data').shares_percentage = percentage;
        this.$.data('data').shares_value = shares;
        this.$.data('data').investment_value = nominal;

        this.__form.isValid();
    });

    this.$selectInvestor.change(() => {
        this.$inputNoKTP.html(null);
        this.$selectInvestor.closest('td').find('small').empty();

        if(this.$selectInvestor.val() !== null) {
            this.$inputNoKTP.attr('disabled', 'disabled');
            ServiceAjax.get(`${this.__form.options.route.investor}/${this.$selectInvestor.val()}`, {
                success: (res) => {
                    this.$inputNoKTP.removeAttr('disabled');

                    if(res.result) {
                        this.$inputNoKTP.html(res.data.no_ktp);
                    }
                },
                error: () => {
                    this.$inputNoKTP.removeAttr('disabled');
                }
            });

            this.$.data('data').investor_id = this.$selectInvestor.val();
            this.$.data('data').investor = {id: this.$selectInvestor.val(), text: this.$selectInvestor.find('option:selected').text()};
        }

        this.__form.isValid();
    });

    this.$btnDelete.click(() => {
        if(this.__form.$body.children().length > 1) {
            if(this.$.data('data').id > 0) {
                this.$.addClass('d-none');
                this.$.data('data').deleted = true;
            }

            else this.$.remove();
        } else {
            this.$nominalShares.val(null);
            this.$inputNoKTP.empty();
            this.$selectInvestor.val(null).text(null);
            this.$percentage.html('0 %');
            this.$invesmentValue.html('Rp. 0');
            this.$selectInvestor.closest('td').find('small').empty();
            this.$nominalShares.closest('td').find('small').empty();
        }

        this.__form.$body.children().last().data('form').$btnAdd.removeClass('d-none');
    });

    this.$btnAdd.click(() => {
        this.$selectInvestor.closest('td').find('small').empty();
        this.$nominalShares.closest('td').find('small').empty();

        const countOf = this.__form.countOf();
        const currentShare = parseInt(this.$.data('data').investment_value);
        const shareReady = this.__form.options.projectValue - (countOf.nominal - currentShare);

        if(countOf.nominal < this.__form.options.projectValue) {
            if (this.$selectInvestor.val() === null) {
                this.$selectInvestor.closest('td').find('small')
                    .html('Investor tidak boleh kosong');
            } else if (isNaN(parseInt(this.$nominalShares.val()))) {
                this.$nominalShares.closest('td').find('small')
                    .html('Lembar saham tidak boleh kosong');
            } else if (this.$nominalShares.val() !== null && this.$nominalShares.val() !== null) {
                this.$btnAdd.addClass('d-none');
                this.__form.add();
            }
        }

        else if(countOf.nominal === this.__form.options.projectValue) {
            this.$nominalShares.closest('td').find('small')
                .html(`Tidak dapat menambahkan investor, jumlah modal sudah mencapai batas maksimal`);
        }

        else {
            this.$nominalShares.closest('td').find('small')
                .html(`Jumlah maksimal nominal saham yang diperbolehkan adalah Rp. ${idr(shareReady)}`);
        }
    });
};

ProjectInvestorForm.prototype.update = function() {
    const data = this.$.data('data');
    this.$inputNoKTP.html(data.investor.no_ktp);
    this.$selectInvestor.append($('<option>', {value: data.investor.id}).text(data.investor.investor_name));
    this.$nominalShares.val(data.investment_value);
    this.$invesmentValue.html(`${idr(data.shares_value)} Lembar`);
    this.$percentage.html(`${idr(data.shares_percentage)} %`);
};

const ProjectInvestor = function(selector, options) {
    this.$ = $(selector);
    this.$body = $(this.$.find('tbody'));

    this.options = new ProjectInvestorOptions(options);
    this.$labelModalValue = $('#label-modal-value');
    this.$labelModalLack = $('#label-modal-lack');

    this.$form = $('<tr>').append(
        $('<td>').append($('<span>', {'data-action': 'nomor'})),
        $('<td>').append($('<span>', {'data-action': 'input-noktp'})),
        $('<td>').append(
            $('<select>', {
                class: 'form-control',
                'data-toggle': 'select2',
                'data-url': this.options.route.selectInvestor === null ? `${this.options.route.investor}/select` : this.options.route.selectInvestor,
                'data-action': 'select-investor'
            }).css({width: 150}),
            $('<small>', {class: 'text-danger'}),
        ).css({width: 300}),
        $('<td>').append(
            $('<input>', {
                type: 'text',
                class: 'form-control',
                placeholder: '0',
                'data-action': 'input-shares',
                'data-toggle': 'jquery-number'
            }).css({width: 100}),
            $('<small>', {class: 'text-danger'}),
        ).css({width: 300}),
        $('<td>', {class: 'text-center'}).append($('<span>', {'data-action': 'investment-value'}).html('Rp. 0')).css({width: 200}),
        $('<td>', {class: 'text-center'}).append($('<span>', {'data-action': 'percentage'}).html('0 %')),
        $('<td>').append(
            $('<button>', {type: 'button', class: 'btn btn-outline-primary btn-xs d-none mr-1', 'data-action': 'btn-add'}).append(
                $('<i>', {class: 'fa fa-plus-circle'}),
            ),
            $('<button>', { type: 'button', class: 'btn btn-outline-danger btn-xs', 'data-action': 'btn-delete'}).append(
                $('<i>', {class: 'fa fa-trash'}),
            ),
        )
    );
}

ProjectInvestor.prototype.add = function() {
    const $form = $(this.$form.clone());
    const data = new DataProjectInvestor();
    const form = new ProjectInvestorForm($form, this);
    form.init();

    $form.data('form', form);
    $form.data('data', data);

    this.$body.append($form);

    form.$btnAdd.removeClass('d-none');

    return $form;
};

ProjectInvestor.prototype.set = function(values) {
    values.forEach(value => {
        const $form = this.add();
        $form.data('data', new DataProjectInvestor(value));
        $form.data('form').update();
    });

    this.isValid();
};

ProjectInvestor.prototype.isValid = function() {
    let isValid = true;
    const children = this.$body.children();

    let modalValue = 0, shareValue = 0;
    for(let i = 0; i < children.length; i++) {
        const $item = $(children[i]);

        const data = $item.data('data');
        const form = $item.data('form');

        if(modalValue + parseFloat(data.investment_value) > this.options.projectValue) {
            isValid = false;
            break;
        }

        if (form.$selectInvestor.val() === null) {
            form.$selectInvestor.closest('td').find('small')
                .html('Investor tidak boleh kosong');
            isValid = false;
            break;
        }

        if (isNaN(parseInt(form.$nominalShares.val()))) {
            form.$nominalShares.closest('td').find('small')
                .html('Nominal saham tidak boleh kosong');
            isValid = false;
            break;
        }

        modalValue += parseFloat(data.investment_value);
        shareValue += parseFloat(data.shares_value);
    }

    const kekurangan = this.options.projectValue - modalValue;
    const sharesKekurangan = kekurangan/this.options.sharesValue;

    this.$labelModalValue.html(`Rp. ${$.number(modalValue, null, ',', '.')} (${Math.round(shareValue * 100)/100} Lembar)`);
    this.$labelModalLack.html(`Rp. ${$.number(kekurangan, null, ',', '.')} (${Math.round(sharesKekurangan * 100)/100} Lembar)`);

    if(modalValue !== this.options.projectValue) {
        isValid = false;
    }

    return isValid;
};

ProjectInvestor.prototype.validate = function() {
    if(!this.options.isDraft) {
        const children = this.$body.children();

        let modalValue = 0;
        let sharesValue = this.options.sharesValue;
        for(let i = 0; i < children.length; i++) {
            const $item = $(children[i]);

            const data = $item.data('data');
            const form = $item.data('form');

            if(modalValue + parseFloat(data.investment_value) > this.options.projectValue) {
                form.$nominalShares.closest('td').find('small')
                    .html(`Jumlah lembar maksimal yang disa digunakan adalah ${idr(sharesValue)} lembar`);
            }

            modalValue += parseFloat(data.investment_value);
            sharesValue -= data.shares_value;
        }

        if(modalValue !== 0 && modalValue < this.options.projectValue)
            AlertNotif.toastr.error("Jumlah modal belum mencukupi kebutuhan");
    }
}

ProjectInvestor.prototype.countOf = function() {
    const countOf = {
        shares: 0,
        nominal: 0,
        percentage: 0,
    };

    this.$body.children().each((i, item) => {
        const $item = $(item);
        const data = $item.data('data');

        countOf.shares += parseFloat(data.shares_value);
        countOf.nominal += parseFloat(data.investment_value);
        countOf.percentage += parseFloat(data.shares_percentage);
    });

    return countOf;
};

ProjectInvestor.prototype.toJSON = function() {
    const json = [];

    this.$body.children().each((i, item) => {
        json.push($(item).data('data').toJson());
    });

    return json;
};

ProjectInvestor.prototype.toString = function() {
    return JSON.stringify(this.toJSON());
};
