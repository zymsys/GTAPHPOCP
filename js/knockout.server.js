ko.server = {};

ko.server.ModelFactory = function (options) {
    var ModelConstructor;

    //Add our new Model to the viewModel
    options.viewModel[options.name] = {
        items: ko.observableArray([]),
        selected: ko.observable(),
        edit: ko.observable(),
        remove: ko.observable(),
        fields: options.fields,
        add: function() {
            options.viewModel[options.name].selected(null);
            options.viewModel[options.name].edit(new ModelConstructor(options.defaults));
        }
    };

    ModelConstructor = function (values) {
        for (var i = 0; i < options.fields.length; i++) {
            if ($.isArray(options.defaults[i])) {
                this[options.fields[i]] = ko.observableArray(values[i]);
            } else {
                this[options.fields[i]] = ko.observable(values[i]);
            }
        }

        this.edit = function() {
            var currentValues = [];
            for (var i = 0; i < options.fields.length; i++) {
                currentValues[i] = this[options.fields[i]]();
            }
            options.viewModel[options.name].selected(this);
            options.viewModel[options.name].edit(new ModelConstructor(currentValues));
        };

        this.remove = function() {
            options.viewModel[options.name].remove(this);
        };
    };
    options.viewModel[options.name].Constructor = ModelConstructor;

    return ModelConstructor;
};

ko.server.buildDataAdapter = function(viewModel, modelName, adapter) {
    if (!adapter) {
        adapter = {};
    }
    if (!adapter.error) {
        adapter.error = function(message) {
            alert(message);
        }
    }
    if (!adapter.index) {
        adapter.index = {};
    }
    if (!adapter.index.url) {
        adapter.index.url = '/' + modelName;
    }
    if (!adapter.index.mapfield) {
        adapter.index.mapfield = function(field, value) {
            return value;
        };
    }
    if (!adapter.index.mapper) {
        adapter.index.mapper = function(data) {
            var fields = viewModel[modelName].fields;
            return $.map(data, function(item) {
                var values = [];
                for (var i = 0; i < fields.length; i++) {
                    var fieldname = fields[i];
                    var value = item[fields[i]];
                    value = adapter.index.mapfield(fieldname, value);
                    values.push(value);
                }
                return new viewModel[modelName].Constructor(values);
            });
        };
    }
    if (!adapter.index.method) {
        adapter.index.method = function() {
            //if (this.lastUserRequest) this.lastUserRequest.abort(); // Prevent concurrent requests
            this.lastUserRequest = $.get(adapter.index.url, function (data) {
                var mapped = adapter.index.mapper(data);
                viewModel[modelName].items(mapped);
            });
        };
    }
    if (!adapter.edit) {
        adapter.edit = {};
    }
    if (!adapter.edit.url) {
        adapter.edit.url = '/' + modelName;
    }
    if (!adapter.edit.dialog) {
        adapter.edit.dialog = $('#' + modelName + 'Dialog');
    }
    if (!adapter.edit.mapfield) {
        adapter.edit.mapfield = function(field, value) {
            return value;
        };
    }
    if (!adapter.edit.mapper) {
        adapter.edit.mapper = function(data) {
            var mapped = {};
            var fields = viewModel[modelName].fields;
            for (var i = 0; i < fields.length; i++) {
                mapped[fields[i]] = adapter.edit.mapfield(fields[i],data[fields[i]]);
            }
            return mapped;
        };
    }
    if (!adapter.edit.error) {
        adapter.edit.error = function(data) {
            if (data.status == 'error') {
                adapter.error(data.message);
                return true;
            }
            return false;
        }
    }
    if (!adapter.edit.method) {
        adapter.edit.method = function(data, isNew) {
            if (!adapter.edit.error(data)) {
                if (adapter.edit.onSave) {
                    adapter.edit.onSave();
                }
                if (isNew) {
                    viewModel[modelName].items.push(viewModel[modelName].edit());
                } else {
                    var idx = viewModel[modelName].items.indexOf(viewModel[modelName].selected());
                    var item = viewModel[modelName].items()[idx];
                    var edititem = viewModel[modelName].edit();
                    var fields = viewModel[modelName].fields;
                    for (var i = 0; i < fields.length; i++) {
                        item[fields[i]](edititem[fields[i]]());
                    }
                }
            }
        }
    }
    if (!adapter.edit.save) {
        adapter.edit.save = function() {
            var item = viewModel[modelName].edit();
            var isNew = viewModel[modelName].selected() == null;
            var url = adapter.edit.url;
            if (!isNew) {
                url += '/' + viewModel[modelName].selected().id();
            }
            $.ajax(url, {
                type: isNew ? 'POST' : 'PUT',
                dataType: 'json',
                data: adapter.edit.mapper(item),
                success: function(data, textStatus, jqXHR) {
                    adapter.edit.method(data, isNew);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    adapter.error(textStatus);
                }
            });
            $(this).dialog("close");
        };
    }
    if (!adapter.edit.set) {
        adapter.edit.set = function() {
            var item = viewModel[modelName].edit();
            if (item == null) return;
            adapter.edit.dialog.dialog({
                width: 'auto',
                resizable: false,
                modal: true,
                buttons: {
                    Save: adapter.edit.save,
                    Cancel: function () {
                        $(this).dialog("close");
                    }
                }
            });
        };
    }
    if (!adapter.remove) {
        adapter.remove = {};
    }
    if (!adapter.remove.url) {
        adapter.remove.url = '/' + modelName;
    }
    if (!adapter.remove.confirm) {
        adapter.remove.confirm = function() {
            var item = viewModel[modelName].remove();
            if (item == null) return;
            $('#' + modelName + 'RemoveConfirmation').html($( '#' + modelName + 'RemoveConfirmationTemplate' ).tmpl( item )).dialog({
                resizable: false,
                modal: true,
                buttons: {
                    "Yes": function() {
                        adapter.remove.method(item);
                        $( this ).dialog( "close" );
                        viewModel.user.remove(null);
                    },
                    No: function() {
                        $( this ).dialog( "close" );
                        viewModel.user.remove(null);
                    }
                }
            });
        }
    }
    if (!adapter.remove.method) {
        adapter.remove.method = function(item) {
            $.ajax(adapter.remove.url + '/' + item.id(), {
                type: 'DELETE'
            });
            viewModel[modelName].items.remove(item);
        }
    }
    viewModel[modelName].dataAdapter = adapter;
    return adapter;
}

ko.server.dataAdapter = function(viewModel, modelName, adapter) {
    adapter = ko.server.buildDataAdapter(viewModel, modelName, adapter);

    ko.dependentObservable(adapter.index.method, viewModel);
    ko.dependentObservable(adapter.edit.set, viewModel);
    ko.dependentObservable(adapter.remove.confirm, viewModel)
};
