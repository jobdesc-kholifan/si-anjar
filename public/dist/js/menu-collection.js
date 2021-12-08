const MenuCollection = function(target) {
    this.items = [];
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

    return filtered;
};

MenuCollection.prototype.renderChild = function(index, id = null) {
    index++;

    const menus = this.parentId(id);
    menus.forEach((menu) => {
        const $row = $('<tr>');
        const $label = $('<td>');
        $label.html(menu.name);
        $label.css({paddingLeft: 20*index, width: '30%'});

        const $features = $('<td>');
        menu.features.forEach((feature, index) => {
            const $checkbox = $('<input>', {
                type: "checkbox",
                name: feature.slug,
                class: "custom-control-input",
                id: "feature-{0}-{1}".format(menu.id, feature.slug),
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
               feature.has_access = $checkbox.prop('checked');
            });
        });

        this.$.find('tbody').append($row.append($label, $features));

        this.renderChild(index, menu.id);
    });
};
