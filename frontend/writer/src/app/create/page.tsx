'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';

type BlockType =
  | 'heading'
  | 'subheading'
  | 'paragraph'
  | 'image'
  | 'image-gallery'
  | 'pullquote'
  | 'list'
  | 'table'
  | 'callout';

interface Block {
  id: string;
  type: BlockType;
  content: string;
  alignment?: 'left' | 'center' | 'right' | 'full';
  caption?: string;
  credit?: string;
  alt?: string;
}

interface ArticleForm {
  title: string;
  slug: string;
  excerpt: string;
  content: Block[];
  status: 'draft' | 'published' | 'scheduled';
  category_id: string;
  tags: string[];
}

const BLOCK_TYPES: { value: BlockType; label: string }[] = [
  { value: 'heading', label: 'Heading' },
  { value: 'subheading', label: 'Subheading' },
  { value: 'paragraph', label: 'Paragraph' },
  { value: 'image', label: 'Image' },
  { value: 'image-gallery', label: 'Image Gallery' },
  { value: 'pullquote', label: 'Pull Quote' },
  { value: 'list', label: 'List' },
  { value: 'table', label: 'Table' },
  { value: 'callout', label: 'Callout' },
];

let blockIdCounter = 0;

const defaultBlock = (type: BlockType): Block => ({
  id: `block_${++blockIdCounter}`,
  type,
  content: '',
  alignment: 'left',
});

const BLOCK_EDITORS: Record<
  BlockType,
  React.ComponentType<{
    block: Block;
    onChange: (updates: Partial<Block>) => void;
    onActivate: () => void;
    isActive: boolean;
  }>
> = {
  heading: HeadingEditor,
  subheading: SubheadingEditor,
  paragraph: ParagraphEditor,
  image: ImageEditor,
  'image-gallery': ImageGalleryEditor,
  pullquote: PullQuoteEditor,
  list: ListEditor,
  table: TableEditor,
  callout: CalloutEditor,
};

function HeadingEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <input
        type="text"
        value={block.content}
        onChange={(e) => onChange({ content: e.target.value })}
        className="flex-1 text-xl font-serif font-bold text-burgundy-deep bg-transparent border-none focus:ring-0 placeholder:text-charcoal/30"
        placeholder="Heading"
        onFocus={onActivate}
        onBlur={onActivate}
      />
    </div>
  );
}

function SubheadingEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <input
        type="text"
        value={block.content}
        onChange={(e) => onChange({ content: e.target.value })}
        className="flex-1 text-lg font-serif font-semibold text-charcoal bg-transparent border-none focus:ring-0 placeholder:text-charcoal/30"
        placeholder="Subheading"
        onFocus={onActivate}
        onBlur={onActivate}
      />
    </div>
  );
}

function ParagraphEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <textarea
        value={block.content}
        onChange={(e) => onChange({ content: e.target.value })}
        rows={3}
        className="flex-1 text-charcoal bg-transparent border-none focus:ring-0 placeholder:text-charcoal/30 min-h-[80px] resize-y"
        placeholder="Write your paragraph here…"
        onFocus={onActivate}
        onBlur={onActivate}
      />
    </div>
  );
}

function ImageEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <div className="flex-1">
        <input
          type="text"
          value={block.content}
          onChange={(e) => onChange({ content: e.target.value })}
          className="w-full px-3 py-2 border border-burgundy-deep/30 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
          placeholder="Image URL"
          onFocus={onActivate}
          onBlur={onActivate}
        />
        <input
          type="text"
          value={block.alt || ''}
          onChange={(e) => onChange({ alt: e.target.value })}
          className="mt-2 w-full px-3 py-2 border border-burgundy-deep/30 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
          placeholder="Alt text"
        />
      </div>
    </div>
  );
}

function PullQuoteEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <textarea
        value={block.content}
        onChange={(e) => onChange({ content: e.target.value })}
        rows={3}
        className="flex-1 italic text-charcoal bg-ivory-soft/50 border-l-4 border-burgundy-wine pl-4 focus:ring-0 placeholder:text-charcoal/30 min-h-[80px] resize-y"
        placeholder="Pull quote text…"
        onFocus={onActivate}
        onBlur={onActivate}
      />
    </div>
  );
}

function ListEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <textarea
        value={block.content}
        onChange={(e) => onChange({ content: e.target.value })}
        rows={4}
        className="flex-1 text-charcoal bg-transparent border-none focus:ring-0 placeholder:text-charcoal/30 min-h-[100px] resize-y"
        placeholder="List items (one per line)…"
        onFocus={onActivate}
        onBlur={onActivate}
      />
    </div>
  );
}

function TableEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <input
        type="text"
        value={block.content}
        onChange={(e) => onChange({ content: e.target.value })}
        className="flex-1 text-sm text-charcoal bg-transparent border-none focus:ring-0 placeholder:text-charcoal/30"
        placeholder="Table data (JSON)…"
        onFocus={onActivate}
        onBlur={onActivate}
      />
    </div>
  );
}

function CalloutEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <textarea
        value={block.content}
        onChange={(e) => onChange({ content: e.target.value })}
        rows={3}
        className="flex-1 text-charcoal bg-burgundy-deep/10 border border-burgundy-deep/20 pl-4 focus:ring-0 placeholder:text-charcoal/30 min-h-[80px] resize-y"
        placeholder="Callout text…"
        onFocus={onActivate}
        onBlur={onActivate}
      />
    </div>
  );
}

function ImageGalleryEditor({
  block,
  onChange,
  onActivate,
  isActive,
}: {
  block: Block;
  onChange: (updates: Partial<Block>) => void;
  onActivate: () => void;
  isActive: boolean;
}) {
  return (
    <div className="flex items-start gap-3">
      <div className="w-8 text-center text-charcoal/40">
        {isActive ? '●' : '○'}
      </div>
      <input
        type="text"
        value={block.content}
        onChange={(e) => onChange({ content: e.target.value })}
        className="flex-1 text-sm text-charcoal bg-transparent border-none focus:ring-0 placeholder:text-charcoal/30"
        placeholder="Image URLs (comma separated)…"
        onFocus={onActivate}
        onBlur={onActivate}
      />
    </div>
  );
}

export default function CreateArticlePage() {
  const [form, setForm] = useState<ArticleForm>({
    title: '',
    slug: '',
    excerpt: '',
    content: [defaultBlock('heading')],
    status: 'draft',
    category_id: '',
    tags: [],
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [activeBlockId, setActiveBlockId] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
    }
  }, []);

  const addBlock = (type: BlockType) => {
    const newBlock = defaultBlock(type);
    setForm((prev) => ({
      ...prev,
      content: [...prev.content, newBlock],
    }));
    setActiveBlockId(newBlock.id);
  };

  const removeBlock = (id: string) => {
    setForm((prev) => ({
      ...prev,
      content: prev.content.filter((b) => b.id !== id),
    }));
  };

  const updateBlock = (id: string, updates: Partial<Block>) => {
    setForm((prev) => ({
      ...prev,
      content: prev.content.map((b) =>
        b.id === id ? { ...b, ...updates } : b
      ),
    }));
  };

  const moveBlock = (id: string, direction: 'up' | 'down') => {
    setForm((prev) => {
      const idx = prev.content.findIndex((b) => b.id === id);
      if (direction === 'up' && idx > 0) {
        const newContent = [...prev.content];
        [newContent[idx], newContent[idx - 1]] = [
          newContent[idx - 1],
          newContent[idx],
        ];
        return { ...prev, content: newContent };
      }
      if (direction === 'down' && idx < prev.content.length - 1) {
        const newContent = [...prev.content];
        [newContent[idx], newContent[idx + 1]] = [
          newContent[idx + 1],
          newContent[idx],
        ];
        return { ...prev, content: newContent };
      }
      return prev;
    });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      const token = authStorage.getToken();
      const response = await apiClient.post<{ data: { id: string } }>(
        '/api/v1/writer/articles',
        form
      );
      const res = response as unknown as { data: { data: { id: string } } };
      window.location.href = `/articles/${res.data.data.id}`;
    } catch (err: any) {
      setError(err.message || 'Failed to create article');
      setLoading(false);
    }
  };

  return (
    <div className="max-w-4xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between">
        <Link href="/articles" className="text-burgundy-wine hover:underline">
          ← Back to articles
        </Link>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep ml-4">
          Create Article
        </h1>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-6 bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">
            Title
          </label>
          <input
            name="title"
            value={form.title}
            onChange={(e) =>
              setForm({ ...form, title: e.target.value })
            }
            className="w-full px-3 py-2 border border-burgundy-deep/30 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            placeholder="Article title"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">
            Slug
          </label>
          <input
            name="slug"
            value={form.slug}
            onChange={(e) =>
              setForm({ ...form, slug: e.target.value })
            }
            className="w-full px-3 py-2 border border-burgundy-deep/30 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            placeholder="my-article-slug"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">
            Excerpt
          </label>
          <textarea
            name="excerpt"
            value={form.excerpt}
            onChange={(e) =>
              setForm({ ...form, excerpt: e.target.value })
            }
            rows={3}
            className="w-full px-3 py-2 border border-burgundy-deep/30 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            placeholder="Brief summary of the article"
          />
        </div>

        <div className="border-t border-burgundy-deep/20 pt-4">
          <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-4">
            Content Blocks
          </h3>

          <div className="flex gap-2 mb-4">
            {BLOCK_TYPES.map((bt) => (
              <button
                key={bt.value}
                type="button"
                onClick={() => addBlock(bt.value)}
                className="px-3 py-1.5 text-xs text-charcoal bg-ivory-soft border border-burgundy-deep/20 rounded hover:bg-burgundy-deep/50 hover:text-burgundy-deep transition-colors"
              >
                + {bt.label}
              </button>
            ))}
          </div>

                <div className="space-y-3">
            {form.content.map((block) => {
              const BlockComp = BLOCK_EDITORS[block.type];
              return (
                <div
                  key={block.id}
                  className={`p-3 rounded-lg border ${
                    activeBlockId === block.id
                      ? 'border-burgundy-wine bg-burgundy-deep/10'
                      : 'border-burgundy-deep/20 hover:border-burgundy-wine/50'
                  }`}
                >
                  <div className="flex items-center justify-between mb-2">
                    <span className="text-xs text-charcoal/60 uppercase tracking-wider">
                      {block.type}
                    </span>
                    <div className="flex gap-1">
                      <button
                        type="button"
                        onClick={() => moveBlock(block.id, 'up')}
                        disabled={form.content[0].id === block.id}
                        className="text-xs text-charcoal/40 hover:text-burgundy-wine disabled:opacity-30"
                      >
                        ↑
                      </button>
                      <button
                        type="button"
                        onClick={() => moveBlock(block.id, 'down')}
                        disabled={
                          form.content[form.content.length - 1].id === block.id
                        }
                        className="text-xs text-charcoal/40 hover:text-burgundy-wine disabled:opacity-30"
                      >
                        ↓
                      </button>
                      <button
                        type="button"
                        onClick={() => removeBlock(block.id)}
                        className="text-xs text-red-600 hover:bg-red-50"
                      >
                        ×
                      </button>
                    </div>
                  </div>
                  <BlockComp
                    block={block}
                    onChange={(updates) => updateBlock(block.id, updates)}
                    isActive={activeBlockId === block.id}
                    onActivate={() => setActiveBlockId(block.id)}
                  />
                </div>
              );
            })}
          </div>
        </div>

        <div className="grid md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-charcoal mb-1">
              Status
            </label>
            <select
              name="status"
              value={form.status}
              onChange={(e) =>
                setForm({ ...form, status: e.target.value as ArticleForm['status'] })
              }
              className="w-full px-3 py-2 border border-burgundy-deep/30 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            >
              <option value="draft">Draft</option>
              <option value="published">Publish Now</option>
              <option value="scheduled">Schedule</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-charcoal mb-1">
              Category
            </label>
            <select
              name="category_id"
              value={form.category_id}
              onChange={(e) =>
                setForm({ ...form, category_id: e.target.value })
              }
              className="w-full px-3 py-2 border border-burgundy-deep/30 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            >
              <option value="">No category</option>
            </select>
          </div>
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine disabled:opacity-60 transition-colors"
        >
          {loading ? 'Saving…' : form.status === 'published' ? 'Publish Article' : 'Save Draft'}
        </button>
      </form>
    </div>
  );
}
