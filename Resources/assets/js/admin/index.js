'use strict';

init.push(() => {
    let table = $('#invoices-datatable');

    if(table.length) {
        table.DataTable({
            processing: true,
            severSide: true,
            ajax: $(table).data('ajax'),
            responsive: true,

            columns: [
                { data: 'id', name: 'id', orderable: true, searchable: true },
                { data: 'created_at', name: 'created_at', orderable: true, searchable: true },
                { data: 'user', name: 'user_id', orderable: true, searchable: true },
                { data: 'payment_method', name: 'payment_method_id', orderable: true, searchable: true },
                { data: 'total', name: 'total_with_vat', orderable: true, searchable: true },
                { data: 'actions', orderable: false, searchable: false },
            ]
        });

        table.parent().parent().find('input[type=search]').attr('placeholder', 'Search...');
        table.parent().parent().find('.table-caption').html('Invoices');
    }
});