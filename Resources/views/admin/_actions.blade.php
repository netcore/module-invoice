<a href="{{ route('invoice::show', $row) }}" class="btn btn-xs btn-primary" target="_blank">
    <i class="fa fa-eye"></i> View
</a>

<a href="{{ route('invoice::edit', $row) }}" class="btn btn-xs btn-success">
    <i class="fa fa-eye"></i> Edit
</a>

<a
    class="btn btn-xs btn-danger confirm-action"
    data-title="Confirmation"
    data-text="Invoice will be deleted. Are you sure?"
    data-confirm-button-text="Delete"
    data-method="DELETE"
    data-href="{{ route('invoice::destroy', $row) }}"
    data-success-title="Success"
    data-success-text="Invoice was deleted"
    data-refresh-datatable="#invoices-datatable"
>
    <i class="fa fa-trash"></i> Delete
</a>
