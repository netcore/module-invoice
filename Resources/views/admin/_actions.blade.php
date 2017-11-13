<a href="{{ route('invoice::show', $row) }}" class="btn btn-xs btn-primary" target="_blank">
    <i class="fa fa-eye"></i> View
</a>

<a href="{{ route('invoice::show', $row) }}?download=1" class="btn btn-xs btn-success" download target="_blank">
    <i class="fa fa-cloud-download"></i> Download
</a>