(function () {

    $('body > #body-scroll').perfectScrollbar({
        theme: 'neoflow-cms'
    });

    bootbox.setDefaults({
        locale: NEOFLOW_LOCALE,
        size: 'small'
    });

    $('.confirm:not([disabled])').on('click', function (e) {
        e.preventDefault();

        var $this = $(this);

        bootbox.confirm($this.data('message'), function (result) {
            if (result) {
                document.location.href = $this.data('link') ? $this.data('link') : $this.attr('href');
            }
        });
    });

    $('a[disabled]').on('click', function (e) {
        e.preventDefault();
    });


    $('table.datatable').DataTable({
        'language': {
            'url': NEOFLOW_THEME_URL + '/vendor/dataTables/i18n/' + NEOFLOW_LOCALE + '.json'
        },
        autoWidth: false,
        responsive: {
            details: true,
            breakpoints: [
                {name: 'desktop', width: Infinity},
                {name: 'tablet', width: 1024},
                {name: 'fablet', width: 768},
                {name: 'phone', width: 480}
            ]
        },
        columnDefs: [
            {targets: 'no-order', orderable: false},
            {targets: 'no-search', searchable: false},
            {responsivePriority: 2, targets: 0},
            {responsivePriority: 2, targets: -1}
        ]
    }).one('draw', function (e, settings) {

        var $wrapper = $(this).parents('div.dataTables_wrapper'),
                $label = $wrapper.find('div.dataTables_filter label'),
                $input = $label.find('input');

        $label.addClass('control-label small').after($input);

        $wrapper.find('div.dataTables_info').addClass('small');

        var $label = $wrapper.find('div.dataTables_length label'),
                $select = $label.find('select');

        $label.addClass('control-label small').append(':').after($select);

        $wrapper.find('.dataTables_paginate ul').addClass('pagination-sm');

    });




    $('nav.sidebar').each(function () {
        var $navSidebar = $(this);

        $navSidebar.perfectScrollbar({
            theme: 'neoflow-cms'
        });
        $navSidebar.find('ul.sidebar-nav').metisMenu({
            activeClassOnComplete: false
        });
    });

    $('select').select2({
        theme: 'bootstrap',
        minimumResultsForSearch: -1,
        allowClear: true,
        templateResult: function (item) {
            if (item.hasOwnProperty('element')) {
                var $element = $(item.element);
                if ($element.data('level')) {
                    var level = $element.data('level');
                    return $('<span style="padding-left:' + (20 * parseInt(level)) + 'px;">' + item.text + '</span>');
                } else if ($element.data('description')) {
                    var description = $element.data('description');
                    return $('<span>' + item.text + '</span><br /><small>' + description + '</small>');
                }
            }
            return item.text;
        },
        placeholder: '',
    }).focus(function () {
        $(this).select2('open');
    });

    $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).parent().siblings().removeClass('open');
        $(this).parent().toggleClass('open');
    });

    $('form.auto-submit').on('change', function () {
        $(this).submit();
    });


})();

(function () {

    $('.alert-success').delay(3000).animate({
        height: 0,
        paddingTop: 0,
        paddingBottom: 0,
        marginBottom: 0,
        opacity: 0
    }, 2000, function () {
        $(this).addClass('closed').remove();
    });

})();

(function () {
    var $nestable = $('#nestable');

    $nestable.nestable({
        rootClass: 'nestable',
        listClass: 'nestable-list',
        itemClass: 'nestable-item',
        placeClass: 'nestable-placeholder',
        dragClass: 'nestable-dragging',
        handleClass: 'nestable-handle',
        expandBtnHTML: '<button class="btn btn-link btn-xs" data-action="expand"><i class="fa fa-fw fa-plus fa-fw"></i></button>',
        collapseBtnHTML: '<button class="btn btn-link btn-xs" data-action="collapse"><i class="fa fa-fw fa-minus fa-fw"></i></button>',
    }).on('change', function (e) {
        var $target = $(e.target);
        if (!$target.is('input') && !$target.is('a')) {
            $.ajax({
                type: 'POST',
                url: $nestable.data('save-url'),
                data: JSON.stringify($nestable.nestable('serialize')),
                contentType: 'application/json',
                dataType: 'json'
            });
        }
    }).on('collapse', function (el, item) {
        Cookies.set(item.data('id'), 'collapsed');
    }).on('expand', function (el, item) {
        Cookies.remove(item.data('id'));
    });
})();


