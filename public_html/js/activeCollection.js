var DEBUG = 1;
function log(text) {
    DEBUG && console.log(text);
}

var KIVI = (function($, KIVI, undefined){
    var serverSort = function() {
        var self = this;
        if(self.sorted) {
            var ids = [];
            self.items.forEach(function(e) {
                ids.push(e.id);
            });
            log('making ajax call for sorting');
            $.ajax({
                url: '/active-collection/sort/' + ids.join(',')
            }).done(function(value) {
                log('ajax call made for sorting')
                self.sorted = false;
            });
        }
    };

    var toggleButtons = function() {
        if(this.selectedItems.length) {
            this._multipleButtons.removeClass('disabled');
        } else {
            this._multipleButtons.addClass('disabled');
        }
    };

    // Constructor for Collection Item
    KIVI.CollectionItem = function(id, title) {
        this.id = id;
        this.title = title;
    };

    // Constructor for Active Collection
    KIVI.ActiveCollection = function(container) {
        this._container = container;
        this._template = Handlebars.compile(container.find('#template-item').html());
        this.sorted = false;

        var heading = this._container.children('h3'),
            span = heading.find('span');
        if(!span.length) {
            span = $('<span> *</span>').hide().appendTo(heading);
        }
        this._editSpan = span;
        this._container.find('.alert-box').remove();
        this._multipleButtons = this._container.find('button.multiple').addClass('disabled');

        var self = this;

        this._container
            .find('ol#collection')
                .on('click', 'input[type=checkbox]', toggleButtons.bind(this));

        this._multipleButtons.on('click', function(evt) {
            var $this = $(this);
            if(!$this.hasClass('disabled')) {
                switch($this.data('action').toLowerCase()) {
                    case 'remove':
                        var ids = [];
                        self.selectedItems.forEach(function(e) {
                            ids.push(e.id);
                        });
                        log('making ajax call for removing items');
                        $.ajax({
                            url: '/active-collection/remove/' + ids.join(','),
                            context: self
                        }).done(function(value) {
                            this.remove();
                            this._multipleButtons.addClass('disabled');
                            log('ajax call successful. Items removed from the list');
                            log('showing add to collection buttons');
                            ids.forEach(function(e) {
                                var article = $('article[data-id=' + e + ']');
                                if(article.length) {
                                    var button = article.find('a.collection-add');
                                    if(!button.length) {
                                        var temp = Handlebars
                                            .compile($('script#template-add-button').html());
                                        article
                                            .find('ul.button-group')
                                            .append(temp({id: e}));
                                    } else {
                                        button.show();
                                    }
                                }

                            });
                        });
                        break;
                    case 'up':
                        self.moveUp();
                        break;
                    case 'down':
                        self.moveDown();
                        break;
                }
            }
            evt.preventDefault();
        });

        this._multipleButtons
                .parents('ul.button-group')
                    .on('mouseleave', serverSort.bind(this));
    };

    Object.defineProperties(KIVI.ActiveCollection.prototype, {
        name: {
            get: function() {
                return this._container.data('name') ? this._container.data('name') : '';
            }
        },
        id: {
            get: function() {
                return parseInt(this._container.data('id'));
            }
        },
        list: (function() {
            var list = $('ol#collection');
            return {
                get: function() {
                    return list;
                }
            }
        })(),
        edited: {
            get: function() {
                return (this._container.data('edited')) ? true : false;
            },
            set: function(val) {
                if(val && !this.edited) {
                    this._container.data('edited', true);
                    this._editSpan.show();
                } else if(!val && this.edited) {
                    this._container.data('edited', false);
                    this._editSpan.hide();
                }
                if(typeof this.onEditedChange == 'function') {
                    this.onEditedChange.call(this, this.edited);
                }
            }
        },
        items: {
            get: function() {
                var items = [];
                this.list.children('li').each(function() {
                    var id = parseInt($(this).data('id')),
                        title = $(this).clone().find('a').remove().end().text().replace(/\s+/g, ' ').replace(/\s+$/, '');
                    items.push(new KIVI.CollectionItem(id, title));
                });
                return items;
            }
        },
        selectedItems: {
            get: function() {
                var selectedItems = [];
                this.list.children('li').each(function() {
                    if($(this).children('input[type=checkbox]').is(':checked')) {
                        var id = parseInt($(this).data('id')),
                            title = $(this).clone().find('a').remove().end().text().replace(/\s+/g, ' ').replace(/\s+$/, '');
                        selectedItems.push(new KIVI.CollectionItem(id, title));
                    }
                });
                return selectedItems;
            }
        },
        itemExists: {
            value: function(id) {
                id = parseInt(id);
                var found = false;
                $.each(this.items, function(i, v) {
                    if(v.id == id) {
                        found = true;
                    }
                });
                return found;
            }
        },
        moveUp: {
            value: function() {
                var self = this;
                this.selectedItems.forEach(function(e) {
                    var curr = self.list.find('li[data-id=' + e.id + ']');
                    var prev = curr.prev('li');
                    if(prev.length) {
                        curr.insertBefore(prev);
                        self.sorted = true;
                        self.edited = true;
                    }
                });
            }
        },
        moveDown: {
            value: function() {
                var self = this;
                this.selectedItems.reverse().forEach(function(e) {
                    var curr = self.list.find('li[data-id=' + e.id + ']');
                    var next = curr.next('li');
                    if(next.length) {
                        curr.insertAfter(next);
                        self.sorted = true;
                        self.edited = true;
                    }
                });
            }
        },
        remove: {
            value: function() {
                var self = this;
                var tmp = [];
                this.selectedItems.forEach(function(e) {
                    if(self.itemExists(e.id)) {
                        tmp.push('[data-id=' + e.id + ']');
                    }
                });
                if(tmp.length) {
                    this.list.find(tmp.join(',')).remove();
                    this.edited = true;
                }
            }
        },
        add: {
            value: function(item) {
                if(!(item instanceof KIVI.CollectionItem)) {
                    throw new Error('Invalid item (argument) provided for adding to active collection');
                }
                if(!this.itemExists(item.id)) {
                    var html = this._template(item);
                    $(html).insertBefore('#template-item');
                    this.edited = true;
                }
            }
        }
    });
    KIVI.idFromUrl = function(url) {
        return parseInt(/active-collection\/(add|remove)\/(\d+)/.exec(url).pop());
    };
    KIVI.processResponse = function(response) {
        if(typeof response == 'object') {
            if(response.id) {
                return new KIVI.CollectionItem(response.id, response.title);
            }
        }
        return null;
    };

    return KIVI;
}(jQuery, KIVI || {}));

(function($){
    var container = $('aside#active-collection'),
        activeCollection = new KIVI.ActiveCollection(container);
    $('section#articles').on('click', 'article a.collection-add', function(evt) {
        var id = $(this).parents('article').data('id'),
            $this = $(this);
        $.ajax({
            url: '/active-collection/add/' + id,
            dataType: 'json'
        }).done(function(value) {
            if(value.id && value.title) {
                activeCollection.add(new KIVI.CollectionItem(value.id, value.title));
                $this.hide();
            }
        });
        evt.preventDefault();
    });
})(jQuery);