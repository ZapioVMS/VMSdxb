<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApprovalsController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Display a listing of approvals based on the user's scope.
     */
    public function index(Request $request): View
    {
        // Fetch all approvals manageable by the current user
        // Using a mock auth user for now, in a real app this would be Auth::user()
        $user = \App\Models\User::first(); // Assuming first user is logged in
        
        $pendingApprovals = $this->approvalService->getPendingApprovalsForUser($user);

        // Fetch user's past actions / finalized approvals for the History tab
        $history = ApprovalRequest::with(['requester', 'approvable'])
            ->where('organization_id', $user->organization_id)
            ->whereNotNull('decided_at')
            ->orderBy('decided_at', 'desc')
            ->take(50)
            ->get();

        return view('approvals.index', compact('pendingApprovals', 'history'));
    }

    /**
     * Handle the approval action.
     */
    public function action(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,request_more_info'],
            'comments' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = \App\Models\User::first(); // Mock Auth::user()
        $approvalRequest = ApprovalRequest::where('organization_id', $user->organization_id)->findOrFail($id);

        $this->approvalService->submitAction(
            $approvalRequest,
            $user,
            $validated['action'],
            $validated['comments']
        );

        $actionWord = match($validated['action']) {
            'approve' => 'approved',
            'reject' => 'rejected',
            'request_more_info' => 'sent back for more info',
        };

        return redirect()->route('approvals.index')->with('success', "Request successfully {$actionWord}.");
    }
}
