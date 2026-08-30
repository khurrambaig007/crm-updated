jQuery(document).ready(function ($) {
    $('.cp-tab-link').on('click', function (e) {
        e.preventDefault();

        var $link = $(this);
        var target = $link.attr('href').substring(1);
        var $nav = $link.closest('.cp-tabs');
        var active = $nav.data('active');
        var inactive = $nav.data('inactive');

        $('.cp-tab-pane').addClass('hidden');
        $('#' + target).removeClass('hidden');

        var paneTable = $('#' + target).find('table.dataTable').first().get(0);
        if (paneTable && $.fn.DataTable && $.fn.DataTable.isDataTable(paneTable)) {
            $(paneTable).DataTable().columns.adjust().responsive.recalc();
        }

        $('.cp-tab-link').removeClass(active).addClass(inactive);
        $link.removeClass(inactive).addClass(active);
    });

    var urlParams = new URLSearchParams(window.location.search);
    var activeTab = urlParams.get('tab');
    if (activeTab) {
        $('.cp-tab-link[href="#' + activeTab + '"]').trigger('click');
    }
});
