<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with(['user'])
            ->when($request->filled('action'), fn ($query) => $query->where('action', 'like', '%' . $request->action . '%'))
            ->when($request->filled('user'), fn ($query) => $query->where('user_id', $request->user))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('auditoria.index', [
            'logs' => $logs,
            'filters' => $request->only(['action', 'user']),
        ]);
    }
}
