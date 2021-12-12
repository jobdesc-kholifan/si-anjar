const ActionsModal = function(actions) {
    this._actions = actions;
    this.context = null;
};

ActionsModal.prototype.init = function() {
    this.context = $.createModal({
       url: this._actions.routes.form !== null ? this._actions.routes.form : `${this._actions.routes.index}/form`,
       onLoadComplete: (res) => {
           if(this._actions.callback.modal.onLoadComplete !== null)
               this._actions.callback.modal.onLoadComplete(res, this);
       }
    });
};

ActionsModal.prototype.close = function() {
    this.context.close();
};

ActionsModal.prototype.open = function(options) {
    this.context.open(options);
};

ActionsModal.prototype.form = function() {
    return this.context.form();
};

const ActionsForm = function(actions) {
    this._actions = actions;
};

ActionsForm.prototype.init = function() {
    this._form = this._actions.modal.form();
    FormComponents.select2.init();
    FormComponents.daterangepicker.init();
    FormComponents.number.init();
};

ActionsForm.prototype.action = function(action) {
    return this._form.action(action);
};

ActionsForm.prototype.disabled = function(disabled) {
    this._form.setDisabled(disabled);

    if(this._actions.callback.form.onDisabled !== null)
        this._actions.callback.form.onDisabled(this);
};

ActionsForm.prototype.post = function(action) {
    this._actions.modal.context.form().post(action);
};

ActionsForm.prototype.reset = function() {
    this._form.reset();
    FormComponents.select2.reset();

    if(this._actions.callback.form.onReset !== null)
        this._actions.callback.form.onReset(this);
};

ActionsForm.prototype.setData = function(items) {
    Object.keys(items).forEach(key => {
        const $el = this._form.find(`[name="${key}"]`);

        if($el.length > 0
            && ($el.is('input')&& !['radio', 'checkbox', 'file'].includes($el.attr('type')))
        )$el.val(items[key]);

        if(this._actions.callback.form.onSetData !== null)
            this._actions.callback.form.onSetData(items[key], key, items, this);
    });
};

ActionsForm.prototype.find = function(selector, check) {
    return this._form.find(selector, check);
};

ActionsForm.prototype.submit = function() {
    this._form.submit({
        data: (params) => {
            if(this._actions.callback.form.appendData !== null) {
                return this._actions.callback.form.appendData(params, this);
            }
            return params;
        },
        beforeSubmit: () => {
            this.disabled(true);

            if(this._actions.callback.form.beforeSubmit !== null) {
                const returned = this._actions.callback.form.beforeSubmit(this);
                return returned !== null ? returned : true;
            }

            return true;
        },
        successCallback: (res) => {
            if(res.result) {
                this._actions.modal.close();
                this._actions.datatable.reload();

                if(res.data !== undefined && res.data.redirect !== undefined)
                    window.location.href = res.data.redirect;
            }

            AlertNotif.toastr.response(res);
            this.disabled(false);

            if(this._actions.callback.form.onSuccessCallback !== null)
                this._actions.callback.form.onSuccessCallback(res, this);
        },
        errorCallback: (res, type, message) => {
            this._actions.modal.close();

            if(res.hasOwnProperty('responseJSON')) {
                AlertNotif.adminlte.error(res.responseJSON.message, {
                    title: DBMessage.ERROR_PROCESSING_TITLE,
                    autoHide: false
                });
            }

            else {
                AlertNotif.adminlte.error(DBMessage.ERROR_SYSTEM_MESSAGE, {
                    title: DBMessage.ERROR_PROCESSING_TITLE,
                    autoHide: false
                });
            }

            if(this._actions.callback.form.onErrorCallback !== null)
                this._actions.callback.form.onErrorCallback(res, type, message, this);
        }
    });
};

const ActionsDatatable = function(actions) {
    this._actions = actions;

    this.context = null;

    this.dom = "<'row no-gutters align-items-center'<'col-6 d-none d-sm-block'l><'col-12 col-sm-6'f><'table-responsive't><'col-12 col-sm-6'i><'col-12 col-sm-6'p>>";

    this.columnDefs = [];

    this.order = [[1, 'asc']];

    this.serverSide = true;

    this.url = this._actions.routes.datatable !== null ? this._actions.routes.datatable : `${this._actions.routes.index}/datatables`;

    this.type = 'post';

    this.params = {};
};

ActionsDatatable.prototype.init = function() {
    this.context = $(this._actions.selectors.table).DataTable({
        dom: this.dom,
        order: this.order,
        serverSide: this.serverSide,
        ajax: !this.serverSide ? null : {
            url: this.url,
            type: this.type,
            data: (param) => {
                Object.keys(this.params).forEach(value => {
                   param[value] = this.params[value];
                });
                return param;
            },
        },
        columnDefs: this.columnDefs,
    });
};

ActionsDatatable.prototype.reload = function(reset = null, position = false) {
    this.context.ajax.reload(reset, position);
};

const Actions = function(url) {
    this.selectors = {
        table: '#table-data'
    };

    this.routes = {
        index: url,
        form: null,
        create: null,
        store: null,
        edit: null,
        update: null,
        delete: null,
        datatable: null,
    };

    this.callback = {
        onCreate: null,
        onEdit: null,
        onDelete: null,
        modal: {
            onLoadComplete: null,
        },
        form: {
            appendData: null,
            beforeSubmit: null,
            onReset: null,
            onDisabled: null,
            onSetData: null,
            onSuccessCallback: null,
            onErrorCallback: null,
        }
    };

    this.modal = new ActionsModal(this);
    this.form = new ActionsForm(this);
    this.datatable = new ActionsDatatable(this);
};

Actions.prototype.build = function() {
    this.modal.init();
    this.datatable.init();
};

Actions.prototype.create = function() {
    this.modal.open({
        success: () => {
            this.form.init();
            this.form.submit();
            this.form.post(this.routes.store !== null ? this.routes.store : this.routes.index);

            if(this.callback.onCreate !== null)
                this.callback.onCreate(this);
        },
    });
};

Actions.prototype.edit = function(id) {
    this.modal.open({
        success: () => {
            this.form.init();
            this.form.submit();
            this.form.disabled(true);

            this.form.post(this.routes.update !== null ? this.routes.update : `${this.routes.index}/{id}`.format({id: id}));

            ServiceAjax.get(this.form.action())
                .done((res) => {
                    if(res.result)
                        this.form.setData(res.data);

                    this.form.disabled(false);

                    if(this.callback.onEdit !== null)
                        this.callback.onEdit(res.data, res, this);
                });
        }
    });
};

Actions.prototype.delete = function(id) {
    $.confirmModal({
        onChange: (value, modal) => {
            if(value) {
                modal.disabled(true);
                ServiceAjax.delete(this.routes.delete !== null ? this.routes.delete : `${this.routes.index}/{id}`.format({id: id}))
                    .done((res) => {
                        if(res.result) {
                            modal.close();
                            this.datatable.reload();
                        }

                        modal.disabled(false);
                        AlertNotif.toastr.response(res);

                        if(this.callback.onDelete !== null)
                            this.callback.onDelete(res.data, res, this);
                    });
            } else modal.close();
        },
    }).show();
};
