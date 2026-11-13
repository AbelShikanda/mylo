'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Comment {
  id: string;
  content: string;
  is_approved?: boolean;
  created_at: string;
  article?: { title: string; slug: string };
  reader?: { display_name?: string; email: string };
}

export default function CommentsPage() {
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/writer/comments')
      .then((res) => setComments(unwrapList<Comment>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Comments…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Comments
        </h1>
        <p className="text-charcoal/70">
          Reader feedback on your articles.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {comments.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No comments yet.</p>
        </div>
      ) : (
        <div className="space-y-4">
          {comments.map((comment) => (
            <div
              key={comment.id}
              className="mylo-card p-5"
            >
              <div className="flex items-center justify-between gap-4 mb-2">
                <span className="text-sm font-medium text-charcoal">
                  {comment.reader?.display_name || comment.reader?.email || 'Reader'}
                </span>
                <span className="text-xs text-charcoal/50">
                  {new Date(comment.created_at).toLocaleDateString()}
                </span>
              </div>
              {comment.article && (
                <p className="text-xs text-burgundy-wine uppercase tracking-wide mb-2">
                  On: {comment.article.title}
                </p>
              )}
              <p className="text-charcoal/80">{comment.content}</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
