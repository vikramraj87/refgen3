var DEBUG = 1;
function log(text) {
    DEBUG && console.log(text);
}

(function($){
    var modal = {
        config: {
            modals: {
                new: {
                    id: "modal-new",
                    heading: "New collection",
                    para: "Your active collection is edited . If you wish to " +
                        "continue creating new collection, all unsaved changes will be lost",
                    button: {
                        url: "",
                        icon: "file",
                        text: "New collection",
                        title: "Create a new empty collection"
                    }
                },
                logout: {
                    id: "modal-logout",
                    heading: "Logout",
                    para: "Your active collection is edited . If you wish to " +
                        "continue, all unsaved changes will be lost",
                    button: {
                        url: "",
                        icon: "sign-out",
                        text: "Logout",
                        title: "Logout"
                    }
                },
                setActive: {
                    id: "modal-active",
                    heading: "Set as active collection",
                    para: "Your active collection is edited . If you wish to " +
                        "continue, all unsaved changes will be lost",
                    button: {
                        url: "",
                        icon: "paperclip",
                        text: "Set as collection",
                        title: "Set as active"
                    }
                },
                delete: {
                    id: "modal-delete",
                    heading: "Delete collection",
                    para: "Are you sure? Do you want to delete the collection permanently?",
                    button: {
                        url: "",
                        icon: "trash-o",
                        text: "Delete collection",
                        title: "Delete collection"
                    }
                },
                openUnedited: {
                    id: "modal-open-unedited",
                    heading: "Open collection",
                    para: "The collection is not yet saved. The changes won't be reflected on opening" +
                        " the collection. Alternatively, you might save and continue.",
                    button: {
                        url: "",
                        icon: "open",
                        text: "Open anyway",
                        title: "Open anyway"
                    }
                }
            },
            template: $('#confirm-modal-template').html()
        },
        template: undefined,
        modals: {},

        init: function(config) {
            $.extend(this.config, config);
        },

        load: function(name, options) {
            if(this.modals[name]) {
                $(this.modals[name]).foundation('reveal', 'open');
                return;
            }
            var context = $.extend(true, this.config.modals[name], options),
                template = this.getTemplate();
            this.modals[name] = template(context);
            $(this.modals[name]).foundation('reveal', 'open');
        },

        getTemplate: function() {
            if(typeof(this.template) == 'undefined') {
                this.template = Handlebars.compile(this.config.template);
            }
            return this.template;
        }
    };
    var activeCollectionContainer = $('aside#active-collection');
    var setActiveLink = $('a[href*="collection/set-active"]'),
        newLink = $('#collection-new'),
        logoutLink = $('#logout');

    setActiveLink.on('click', function(evt) {
        if (activeCollectionContainer.data('edited')) {
            var collectionName = $(this).closest('section').data('collection-name'),
                url = $(this).attr('href'),
                options = {
                    heading: 'Set ' + collectionName + ' as active collection',
                    button: {
                        url: url,
                        text: 'Set ' + collectionName + ' as active collection',
                        title: 'Set ' + collectionName + ' as active collection'
                    }
                };
            modal.load('setActive', options);
            evt.preventDefault();
        }
    });

    newLink.on('click', function(evt) {
        if(activeCollectionContainer.data('edited')) {
            var url = $(this).attr('href'),
                options = {
                    button: {
                        url: url
                    }
                };
            modal.load('new', options);
            evt.preventDefault();
        }
    });

    logoutLink.on('click', function(evt) {
        if(activeCollectionContainer.data('edited')) {
            var url = $(this).attr('href'),
                options = {
                    button: {
                        url: url
                    }
                };
            modal.load('logout', options);
            evt.preventDefault();
        }
    });

    $('a[href*="collection/open"]').on('click', function(evt) {
        var href  = $(this).attr('href'),
            match = /collection\/open\/(\d+)/.exec(href),
            id    = match[1];
        if(id == activeCollectionContainer.data('id') && activeCollectionContainer.data('edited')) {
            modal.load('openUnedited', {button: {url: href}});
            evt.preventDefault();
        }

    });

    $('a[href*="collection/delete"]').on('click', function(evt) {
        var href  = $(this).attr('href'),
            match = /collection\/delete\/(\d+)/.exec(href),
            id    = match[1],
            collectionName = $(this).closest('section').data('collection-name'),
            options = {
                heading: 'Delete collection ' + collectionName,
                para: "Are you sure? Do you want to delete the collection " +
                    collectionName + " permanently?",
                button: {
                    url: href,
                    text: "Delete collection " + collectionName,
                    title: "Delete collection " + collectionName
                }
            };
        modal.load('delete', options);
        evt.preventDefault();
    });
})(jQuery);