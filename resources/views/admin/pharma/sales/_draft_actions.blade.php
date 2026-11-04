@if($document->status === 'draft')
    @if(auth()->guard('admin')->user()->hasPermission($module, 'edit'))
        <a href="{{ route('admin.pharma.' . $prefix . '.edit', $document->id) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1" aria-hidden="true"></i>Edit
        </a>
    @endif
    @if(auth()->guard('admin')->user()->hasPermission($module, 'delete'))
        <form action="{{ route('admin.pharma.' . $prefix . '.delete', $document->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this draft? It will be removed from the list. Stock and account balances will not change.');">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1" aria-hidden="true"></i>Delete
            </button>
        </form>
    @endif
@endif
