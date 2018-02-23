$(function() {
    $.tablesorter.addParser({
        id: 'estimate',
        is: function() {
            return false;
        },
        format: function(s, table, cell) {
            var $cell = $(cell);
            return $cell.attr('data-sort-value');
        },
        parsed: false,
        type: 'text'
    });

    $.tablesorter.addParser({
        id: 'status',
        is: function() {
            return false;
        },
        format: function(s) {
            if (s === 'Backlog') {
                return 1;
            } else if (s === 'To Do') {
                return 2;
            }  else if (s === 'In Progress') {
                return 3;
            } else if (s === 'In Review') {
                return 4;
            } else if (s === 'Released to Staging') {
                return 5;
            } else if (s === 'Done') {
                return 6;
            } else {
                return 0;
            }
        },
        parsed: false,
        type: 'text'
    });

    $('table').tablesorter({
        sortInitialOrder: 'desc'
    });
});
