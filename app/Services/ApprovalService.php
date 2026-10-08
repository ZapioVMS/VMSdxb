<?php

namespace App\Services;

use App\Models\ApprovalRequest;
use App\Models\ApprovalAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ApprovalService
{
    /**
     * Get pending approvals for a user based on their permissions and scope.
     */
    public function getPendingApprovalsForUser(User $user)
    {
        // 1. Determine which types of approvals the user can manage
        $manageableTypes = [];
        // Assuming AuthorizationService is injected or we use the Gate
        // For simplicity, we assume we check role permissions here or the user has a specific role
        // For the sake of this implementation, we will fetch based on 'status' = 'pending_l1' or 'pending_l2'
        
        $query = ApprovalRequest::with(['requester', 'approvable'])->where('organization_id', $user->organization_id);

        // Apply state filtering based on user permissions
        // e.g. If user can approve L1, show pending_l1
        // If user can approve L2, show pending_l2
        
        // This logic can be expanded based on the exact roles (e.g. Host vs Security vs Management)
        // For now, return all non-finalized requests the user is authorized to see
        
        $query->whereIn('status', ['pending_l1', 'pending_l2', 'more_info_requested']);

        // Apply scope filtering (site or department)
        // ->whereIn('site_id', $user->getSiteScopes()) 
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Submit an approval action (approve, reject, request_more_info)
     */
    public function submitAction(ApprovalRequest $request, User $actor, string $action, ?string $comments = null)
    {
        $actionRecord = ApprovalAction::create([
            'approval_request_id' => $request->id,
            'actor_user_id' => $actor->id,
            'action_type' => $action,
            'reason' => $comments,
            'previous_status' => $request->status,
            'new_status' => $action === 'approve' ? 'approved' : ($action === 'reject' ? 'rejected' : 'more_info_requested'),
        ]);

        if ($action === 'approve') {
            if ($request->status === 'pending_l1') {
                $request->update(['status' => 'pending_l2']);
            } elseif ($request->status === 'pending_l2') {
                $request->update([
                    'status' => 'approved',
                    'decided_at' => now(),
                    'decided_by' => $actor->id,
                ]);
            }
        } elseif ($action === 'reject') {
            $request->update([
                'status' => 'rejected',
                'decided_at' => now(),
                'decided_by' => $actor->id,
            ]);
        } elseif ($action === 'request_more_info') {
            // Revert back or just mark as needing info
            $request->update(['status' => 'more_info_requested']);
        }

        return $actionRecord;
    }

    /**
     * Create a new approval request
     */
    public function createRequest(array $data)
    {
        return ApprovalRequest::create(array_merge([
            'status' => 'pending_l1',
        ], $data));
    }
}
