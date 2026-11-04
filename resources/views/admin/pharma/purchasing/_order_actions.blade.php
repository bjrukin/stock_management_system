@if($order->status === 'draft')
    @if(auth()->guard('admin')->user()->hasPermission('PurchaseOrder', 'edit'))
        <a href="{{ route('admin.pharma.purchase-order.edit', $order->id) }}" class="btn btn-sm btn-outline-primary" title="Edit purchase order"><i class="bi bi-pencil me-1" aria-hidden="true"></i>Edit</a>
    @endif
    @if(auth()->guard('admin')->user()->hasPermission('PurchaseOrder', 'delete'))
        <form action="{{ route('admin.pharma.purchase-order.delete', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this draft purchase order? Stock and supplier balances will not change.');">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete draft purchase order"><i class="bi bi-trash me-1" aria-hidden="true"></i>Delete</button>
        </form>
    @endif
@endif
