<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Services\Professional\KhaltiService;
use Illuminate\Http\Request;

class KhaltiController extends Controller
{
    protected $khaltiService;

    public function __construct(KhaltiService $khaltiService)
    {
        $this->khaltiService = $khaltiService;
    }

    /**
     * Initiate Khalti payment.
     */
    public function initiate(Request $request)
    {
        $purpose = $request->query('purpose', 'examApply');
        return $this->khaltiService->initiate($request, $purpose);
    }

    /**
     * Khalti payment callback.
     */
    public function callback(Request $request)
    {
        $purpose = $request->query('purpose', 'examApply');
        return $this->khaltiService->callback($request, $purpose);
    }
}
