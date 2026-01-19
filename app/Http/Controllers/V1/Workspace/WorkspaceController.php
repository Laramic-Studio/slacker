<?php

namespace App\Http\Controllers\V1\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteMemberRequest;
use App\Http\Requests\WorkspaceSetupRequest;
use App\Services\V1\Workspace\WorkspaceService;

class WorkspaceController extends Controller
{

    public function __construct(public WorkspaceService $workspaceService) {}

    public function createWorkspace(WorkspaceSetupRequest $request)
    {
        $response = $this->workspaceService->createWorkspace($request->validated());
        return $this->respondWithCustomData('Workspace created successfully.', $response, 201);
    }

    public function getWorkspaces()
    {
        $response = $this->workspaceService->getWorkspaces();
        return $this->respondWithCustomData('Workspaces retrieved successfully.', $response);
    }

    public function inviteMembers(InviteMemberRequest $request)
    {
        $response = $this->workspaceService->inviteMembers($request->validated());
        return $this->respondWithCustomData('Members invited successfully.', $response);
    }
}
