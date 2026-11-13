<?php

namespace App\Services\Administration;

use App\Models\Identity\Comment;
use App\Models\Identity\ModerationRecord;
use App\Models\Identity\Administrator;
use App\Models\Identity\Report;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdvancedModerationService extends \App\Services\Shared\BaseService
{
    public function getReportedContent(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Report::with('reporter')
            ->orderBy('created_at', 'desc');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['reportable_type'])) {
            $query->where('reportable_type', $filters['reportable_type']);
        }

        if (isset($filters['search'])) {
            $query->where('reason', 'like', "%{$filters['search']}%");
        }

        return $query->paginate(20);
    }

    public function resolveReport(string $reportId, string $action, string $reason, Administrator $moderator): void
    {
        $report = Report::findOrFail($reportId);

        $report->update([
            'status' => $action,
        ]);

        ModerationRecord::create([
            'id' => (string) Str::uuid(),
            'moderator_id' => $moderator->id,
            'subject_type' => $report->reportable_type,
            'subject_id' => $report->reportable_id,
            'action' => $action,
            'reason' => $reason,
        ]);
    }

    public function getModerationRecords(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = ModerationRecord::with('moderator')
            ->orderBy('created_at', 'desc');

        if (isset($filters['moderator_id'])) {
            $query->where('moderator_id', $filters['moderator_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        return $query->paginate(20);
    }

    public function suspendAccount(string $accountType, string $accountId, string $reason, Administrator $moderator): void
    {
        $model = match ($accountType) {
            'reader' => \App\Models\Identity\Reader::findOrFail($accountId),
            'writer' => \App\Models\Identity\Writer::findOrFail($accountId),
            default => throw new \Exception("Invalid account type: {$accountType}"),
        };

        $model->delete();

        ModerationRecord::create([
            'id' => (string) Str::uuid(),
            'moderator_id' => $moderator->id,
            'subject_type' => $accountType,
            'subject_id' => $accountId,
            'action' => 'suspend',
            'reason' => $reason,
        ]);
    }

    public function reinstateAccount(string $accountType, string $accountId, string $reason, Administrator $moderator): void
    {
        $model = match ($accountType) {
            'reader' => \App\Models\Identity\Reader::withTrashed()->findOrFail($accountId),
            'writer' => \App\Models\Identity\Writer::withTrashed()->findOrFail($accountId),
            default => throw new \Exception("Invalid account type: {$accountType}"),
        };

        $model->restore();

        ModerationRecord::create([
            'id' => (string) Str::uuid(),
            'moderator_id' => $moderator->id,
            'subject_type' => $accountType,
            'subject_id' => $accountId,
            'action' => 'reinstate',
            'reason' => $reason,
        ]);
    }

    public function removeComment(string $commentId, string $reason, Administrator $moderator): void
    {
        $comment = Comment::findOrFail($commentId);
        $comment->delete();

        ModerationRecord::create([
            'id' => (string) Str::uuid(),
            'moderator_id' => $moderator->id,
            'subject_type' => 'comment',
            'subject_id' => $commentId,
            'action' => 'remove_comment',
            'reason' => $reason,
        ]);
    }

    public function restrictAccount(string $accountType, string $accountId, array $restrictions, Administrator $moderator): void
    {
        // Restrictions are stored as moderation records
        ModerationRecord::create([
            'id' => (string) Str::uuid(),
            'moderator_id' => $moderator->id,
            'subject_type' => $accountType,
            'subject_id' => $accountId,
            'action' => 'restrict',
            'reason' => json_encode($restrictions),
        ]);
    }
}
