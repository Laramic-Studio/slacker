<?php

namespace App\Services\V1\Workspace;

use App\Exceptions\ClientErrorException;
use App\Models\Invitation;
use App\Models\Organisation;
use App\Models\User;
use App\Services\V1\Attachment\AttachmentService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class WorkspaceService
{


    /**
     * Creates a new workspace.
     *
     * @param array $data
     * @return array
     */
    public function createWorkspace(array $data): array
    {
        try {
            DB::beginTransaction();
            $user = authUser();

            $workspace = Organisation::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . Str::random(5),
                'description' => $data['description'] ?? null,
                'visibility' => $data['visibility'],
            ]);

            $user->update([
                'organisation_id' => $workspace->id,
            ]);

            DB::commit();

            return [
                'workspace' => $workspace,
            ];
        } catch (Exception $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Invites members to join a workspace.
     *
     * @param array $payload
     * @return array
     * @throws ClientErrorException
     */
    public function inviteMembers(array $payload)
    {
        try {
            $workspace = authUser()->organisation;

            foreach ($payload['emails'] as $email) {
                $invite =  Invitation::create([
                    'organisation_id' => $workspace->id,
                    'email' => $email,
                    'token' => generateRandom(20),
                    'expires_at' => now()->addDays(7),
                    'sent_at' => now(),
                ]);

                $user = User::create([
                    'email' => $email,
                    'username' => Str::before($email, '@') . '-' . Str::random(5),
                    'full_name' => Str::before($email, '@'),
                    'password' => generateRandom(9),
                    'organisation_id' => $workspace->id,
                    'was_invited' => true,
                ]);

                $user->notify(new \App\Notifications\WorkspaceInvitationNotification($email, $workspace, $invite));
            }

            return [
                'workspace' => $workspace,
            ];
        } catch (Exception $th) {
            throw $th;
        }
    }

    /**
     * Retrieves the workspaces associated with the currently authenticated user.
     *
     * @return array
     * @throws Exception
     */
    public function getWorkspaces(): array
    {
        try {
            $user = authUser();
            $workspaces = $user->organisation;

            return [
                'workspaces' => $workspaces,
            ];
        } catch (Exception $th) {
            throw $th;
        }
    }
}
