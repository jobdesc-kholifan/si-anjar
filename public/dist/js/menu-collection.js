const MenuCollection = function(target, options = {}) {
    this.items = [];
    this.slugs = options.slugs !== undefined ? options.slugs : {};
    this.$ = $(target);
};

MenuCollection.prototype.setData = function(data) {
    this.items = data;
};

MenuCollection.prototype.init = function() {
    this.$.empty();
    this.$.append(
        $('<thead>').append(
            $('<tr>').append(
                $('<th>').html("Menu"),
                $('<th>').html("Fitur - Fitur"),
            )
        ),
        $('<tbody>'),
    );
};

MenuCollection.prototype.parentId = function(id = null) {
    const filtered = [];
    this.items.forEach((item) => {
        if(item.parent_id === id)
            filtered.push(item);
    });

    return filtered.sort(function(a, b) {
        return a.sequence > b.sequence;
    });
};

MenuCollection.prototype.parentChecked = function($checkbox) {
    const $parentView = $('#feature-{0}-{1}'.format($checkbox.data('parent'), this.slugs.view));
    if($parentView.length > 0) {
        const $parentChild = $(`[data-parent=${$parentView.data('menu')}]`);

        if(!$parentView.prop('checked')) {
            $parentView.prop('checked', true);
            this.parentChecked($parentView);
        } else {

            let isChecked = false;
            for(let i = 0; i < $parentChild.length; i++) {
                const $child = $($parentChild.get(i));
                if($child.attr('name') === this.slugs.view
                    && $child.prop('checked')) {
                    isChecked = true;
                    break;
                }
            }

            if(!isChecked)
                $parentView.click();
        }
    }
};

MenuCollection.prototype.childrenChecked = function($checkbox) {
    const $children = $(`[data-parent=${$checkbox.data('menu')}]`);
    if($checkbox.attr('name') === this.slugs.view
        && !$checkbox.prop('checked')) {
        for(let i = 0; i < $children.length; i++) {
            const $child = $($children.get(i));
            $child.prop('checked', false);

            if($child.attr('name') === this.slugs.view)
                this.childrenChecked($child);
        }
    }

    if($checkbox.attr('name') === this.slugs.view
        && $checkbox.prop('checked')) {
        for(let i = 0; i < $children.length; i++) {
            const $child = $($children.get(i));
            if($child.attr('name') === this.slugs.view
                && !$child.prop('checked'))
                $child.click();
        }
    }
};

MenuCollection.prototype.renderChild = function(index, id = null) {
    index++;

    const menus = this.parentId(id);
    menus.forEach((menu) => {
        const $row = $('<tr>');
        const $label = $('<td>');
        $label.html(menu.name);
        $label.css({paddingLeft: 20*index, width: '30%'});
        if(this.parentId(menu.id).length > 0 || menu.parent_id === null)
            $label.addClass('text-bold');

        const $features = $('<td>');
        menu.features.forEach((feature) => {
            const $checkbox = $('<input>', {
                type: "checkbox",
                name: feature.slug,
                class: "custom-control-input",
                id: "feature-{0}-{1}".format(menu.id, feature.slug),
                'data-menu': menu.id,
                'data-parent': menu.parent_id
            });
            $checkbox.prop('checked', feature.has_access);

            const $label = $('<label>', {
                class: 'custom-control-label',
                for: "feature-{0}-{1}".format(menu.id, feature.slug),
                title: feature.description,
            });
            $label.html(feature.title);
            $label.tooltip('enable');

            $features.append(
                $('<div>', {class: 'custom-control custom-switch d-inline mr-2'}).append($checkbox,$label)
            );

            $checkbox.click(() => {
                const name = $checkbox.attr('name');
                const $triggered = $(`[data-menu=${menu.id}]`);
                if(name === this.slugs.view) {
                    $triggered.prop('checked', $checkbox.prop('checked'));
                } else {
                    $('#feature-{0}-{1}'.format(menu.id, this.slugs.view)).prop('checked', true);
                }

                this.parentChecked($checkbox);
                this.childrenChecked($checkbox);
            });
        });

        this.$.find('tbody').append($row.append($label, $features));

        this.renderChild(index, menu.id);
    });
};

MenuCollection.prototype.toJSON = function() {
    this.items.forEach(menu => {
        menu.features.forEach(feature => {
            const $checkbox = $('#feature-{0}-{1}'.format(menu.id, feature.slug));
            feature.has_access = $checkbox.prop('checked');
        });
    });

    return this.items;
};

MenuCollection.prototype.toString = function() {
    return JSON.stringify(this.toJSON());
};
