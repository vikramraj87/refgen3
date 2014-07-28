// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

$(document).ready(function(){
    var edited = $('#active-collection').data('edited');
    if(1 == edited) {
        var closeModal = $('<a class="close-reveal-modal">&#215;</a>'),
            newLink    = $('#collection-new'),
            logout     = $('#logout');

        var modalNew = $('<div><h2></h2><p></p><a></a></div>') //div
            .find('h2') //h2
            .text('Collection new') //h2
            .end() //div
            .find('p') //p
            .attr('class', 'lead') //p
            .text('Your active collection is edited . If you wish to continue, unsaved changes will be lost') //p
            .end() //div
            .find('a') //a.[tiny button]
            .attr('class', 'tiny button')
            .attr('href', newLink.attr('href'))
            .html('<span class="fa fa-file"></span> New collection')
            .end() //div
            .append(closeModal.clone())
            .attr('id', 'modal-new')
            .attr('class', 'reveal-modal small')
            .attr('data-reveal', '')
            .appendTo('body');

        var modalLogout = $('<div><h2></h2><p></p><a></a></div>') //div
            .find('h2') //h2
            .text('Logout') //h2
            .end() //div
            .find('p') //p
            .attr('class', 'lead') //p
            .text('Your active collection is edited . If you wish to continue, unsaved changes will be lost') //p
            .end() //div
            .find('a') //a.[tiny button]
            .attr('class', 'tiny button')
            .attr('href', logout.attr('href'))
            .html('<span class="fa fa-sign-out"></span> Logout')
            .end() //div
            .append(closeModal.clone())
            .attr('id', 'modal-logout')
            .attr('class', 'reveal-modal small')
            .attr('data-reveal', '')
            .appendTo('body');


        newLink.click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            modalNew.foundation('reveal', 'open');
        });

        logout.click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            modalLogout.foundation('reveal', 'open');
        });
    }
});