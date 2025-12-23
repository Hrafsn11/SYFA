<div class="d-flex gap-2">
    <a href="{{ $detailUrl }}" class="btn btn-sm btn-info" title="Detail Program">
        <i class="ti ti-eye"></i>
    </a>
    @if (isset($editUrl) && $editUrl)
        <a href="{{ $editUrl }}" class="btn btn-sm btn-warning" title="Edit Program">
            <i class="ti ti-pencil"></i>
        </a>
    @endif
</div>
