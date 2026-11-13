<?php

namespace App\Services\Administration;

use App\Models\Identity\Article;
use App\Models\Identity\EditorialSelection;
use Illuminate\Support\Str;

class EditorialSchedulingService extends \App\Services\Shared\BaseService
{
    public function getEditorialSelections(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = EditorialSelection::with('article.writer', 'article.category')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');

        if (isset($filters['section'])) {
            $query->where('section', $filters['section']);
        }

        if (isset($filters['active_only']) && $filters['active_only'] === 'true') {
            $query->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                });
        }

        return $query->paginate(20);
    }

    public function createEditorialSelection(array $data): EditorialSelection
    {
        return EditorialSelection::create([
            'id' => (string) Str::uuid(),
            'article_id' => $data['article_id'],
            'section' => $data['section'],
            'sort_order' => $data['sort_order'] ?? 0,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);
    }

    public function updateEditorialSelection(EditorialSelection $selection, array $data): EditorialSelection
    {
        $selection->update([
            'article_id' => $data['article_id'] ?? $selection->article_id,
            'section' => $data['section'] ?? $selection->section,
            'sort_order' => $data['sort_order'] ?? $selection->sort_order,
            'starts_at' => $data['starts_at'] ?? $selection->starts_at,
            'ends_at' => $data['ends_at'] ?? $selection->ends_at,
        ]);

        return $selection->fresh();
    }

    public function deleteEditorialSelection(EditorialSelection $selection): void
    {
        $selection->delete();
    }

    public function getScheduledArticles(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Article::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->with('writer', 'category')
            ->orderBy('scheduled_at', 'asc');

        if (isset($filters['from_date'])) {
            $query->where('scheduled_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('scheduled_at', '<=', $filters['to_date']);
        }

        return $query->paginate(20);
    }

    public function getHomepageSections(): array
    {
        return [
            [
                'id' => 'featured_stories',
                'name' => 'Featured Stories',
                'type' => 'article_carousel',
            ],
            [
                'id' => 'recent_published',
                'name' => 'Recently Published',
                'type' => 'article_grid',
            ],
            [
                'id' => 'popular_topics',
                'name' => 'Popular Topics',
                'type' => 'category_grid',
            ],
            [
                'id' => 'recommended_books',
                'name' => 'Recommended Books',
                'type' => 'book_carousel',
            ],
            [
                'id' => 'editorial_selections',
                'name' => 'Editorial Selections',
                'type' => 'article_list',
            ],
        ];
    }
}
