<div class="d-flex gap-2">
    <a href="{{ $detailUrl }}" class="btn btn-sm btn-outline-primary" title="Detail Program">
        <i class="ti ti-file"></i>
    </a>
    @if (isset($generateKontrakUrl) && $generateKontrakUrl)
        <a href="{{ $generateKontrakUrl }}" class="btn btn-sm btn-success" title="Generate Kontrak">
            <i class="ti ti-file-check"></i>
        </a>
    @endif
    @if (isset($editUrl) && $editUrl)
        <a href="{{ $editUrl }}" class="btn btn-sm btn-outline-warning" title="Edit Program">
            <i class="ti ti-edit"></i>
        </a>
    @endif
</div>