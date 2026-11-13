<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Identity\Article;
use App\Models\Identity\Book;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $type = $request->get('type', 'all');

        if ($q === '') {
            return response()->json(['data' => []]);
        }

        $results = [];

        if ($type === 'all' || $type === 'article') {
            $articles = Article::where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where(function ($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                        ->orWhere('excerpt', 'like', "%{$q}%")
                        ->orWhere('seo_description', 'like', "%{$q}%");
                })
                ->with('writer', 'category')
                ->limit(10)
                ->get();

            foreach ($articles as $article) {
                $results[] = [
                    'type' => 'article',
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'url' => '/articles/' . $article->slug,
                    'author' => $article->writer?->display_name ?? $article->writer?->email,
                    'category' => $article->category?->name,
                ];
            }
        }

        if ($type === 'all' || $type === 'book') {
            $books = Book::where('is_available', true)
                ->where('title', 'like', "%{$q}%")
                ->with('writer')
                ->limit(10)
                ->get();

            foreach ($books as $book) {
                $results[] = [
                    'type' => 'book',
                    'id' => $book->id,
                    'title' => $book->title,
                    'slug' => $book->slug,
                    'url' => '/books/' . $book->slug,
                    'author' => $book->author,
                    'price' => $book->price,
                    'product_type' => $book->product_type,
                ];
            }
        }

        return response()->json(['data' => $results]);
    }
}
