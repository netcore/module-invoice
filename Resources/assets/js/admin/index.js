'use strict';

init.push(() => {
    let table = $('#invoices-datatable');

    if (table.length) {

        let columns = [
            {data: 'invoice_nr', name: 'invoice_nr', orderable: true, searchable: true},
            {data: 'created_at', name: 'created_at', orderable: true, searchable: true},
        ];

        $.each(enabledRelations, (i, relation) => {
            columns.push({
                data: relation.table.d_data,
                name: relation.table.d_name,
                orderable: relation.table.sortable,
                searchable: relation.table.searchable
            });
        });

        columns.push({data: 'total_without_vat', name: 'total_without_vat', orderable: true, searchable: true});
        columns.push({data: 'total_with_vat', name: 'total_with_vat', orderable: true, searchable: true});
        columns.push({data: 'payment', orderable: false, searchable: false });
        columns.push({data: 'actions', orderable: false, searchable: false, className: 'text-right'});

        table.dataTable({
            processing: true,
            serverSide: true,
            ajax: $(table).data('ajax'),
            responsive: true,
            columns: columns,
            order: [[0, 'desc']]
        });

        table.parent().parent().find('input[type=search]').attr('placeholder', 'Search...');
        table.parent().parent().find('.table-caption').html('Invoices');
    }
});