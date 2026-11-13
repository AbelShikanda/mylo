'use client';

import { useState, useEffect, use } from 'react';

interface Block {
  id: string;
  type: string;
  content: string;
  alignment?: string;
  caption?: string;
  credit?: string;
  alt?: string;
}

interface Writer {
  id: string;
  email: string;
  display_name?: string;
}

interface Category {
  name: string;
}

interface Article {
  id: string;
  title: string;
  slug: string;
  excerpt?: string;
  content: Block[];
  featured_image?: string;
  writer: Writer;
  category?: Category;
  tags: unknown[];
  reading_time: number;
  published_at: string;
  seo_title?: string;
  seo_description?: string;
}

interface ApiResponse<T> {
  data: T;
}

const BlockRenderers: Record<string, React.ComponentType<{ block: Block }>> = {
  heading: ({ block }: { block: Block }) => (
    <h1 className="text-4xl font-serif font-bold text-burgundy-deep mb-6 mt-10">
      {block.content}
    </h1>
  ),
  subheading: ({ block }: { block: Block }) => (
    <h2 className="text-2xl font-serif font-bold text-burgundy-deep mb-4 mt-8">
      {block.content}
    </h2>
  ),
  paragraph: ({ block }: { block: Block }) => (
    <p className="text-lg text-charcoal/80 leading-relaxed mb-6">
      {block.content}
    </p>
  ),
  pullquote: ({ block }: { block: Block }) => (
    <blockquote className="border-l-4 border-burgundy-wine bg-ivory-soft pl-6 py-4 my-8 italic text-charcoal/90">
      {block.content}
      {block.credit && <br />}
      {block.credit && <span className="block text-charcoal/60 mt-2 -mt-1 text-sm">{block.credit}</span>}
    </blockquote>
  ),
  list: ({ block }: { block: Block }) => (
    <ul className="list-disc pl-6 mb-6 text-charcoal/80 leading-relaxed">
      {block.content.split('\n').filter(Boolean).map((item, i) => (
        <li key={i} className="mb-2">{item}</li>
      ))}
    </ul>
  ),
  callout: ({ block }: { block: Block }) => (
    <div className="bg-burgundy-deep/10 border border-burgundy-deep/20 pl-4 py-4 my-8 rounded-lg text-charcoal/80">
      {block.content}
    </div>
  ),
  image: ({ block }: { block: Block }) => (
    <figure className="my-8">
      {block.content && (
        <img
          src={block.content}
          alt={block.alt || ''}
          className="w-full h-auto rounded-lg shadow-md"
        />
      )}
      {block.caption && (
        <figcaption className="mt-2 text-sm text-charcoal/60 text-center italic">
          {block.caption}
          {block.credit && <span className="ml-2">— {block.credit}</span>}
        </figcaption>
      )}
    </figure>
  ),
};

const DefaultRenderer: React.ComponentType<{ block: Block }> = ({ block }) => (
  <div className="mb-6 text-charcoal">{block.content}</div>
);

function ReadingTime({ article }: { article: Article }) {
  const minutes = Math.max(1, Math.ceil(article.reading_time || article.content.length / 200));
  return <span className="text-charcoal/50 text-sm">{minutes} min read</span>;
}

export default function ArticlePage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = use(params);

  const [article, setArticle] = useState<Article | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetch(`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000'}/api/v1/articles/${slug}`)
      .then((res) => res.json())
      .then((data: ApiResponse<Article>) => {
        setArticle(data.data);
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, [slug]);

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <div className="text-center">
          <div className="h-8 bg-burgundy-deep/20 rounded w-1/3 mx-auto mb-4"></div>
          <div className="h-4 bg-burgundy-deep/20 rounded w-1/2 mx-auto"></div>
        </div>
      </div>
    );
  }

  if (error || !article) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4 text-center">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep">Article not found</h1>
        <p className="text-charcoal/70 mt-4">{error}</p>
      </div>
    );
  }

  return (
    <article className="max-w-2xl mx-auto py-12 px-4">
      <header className="mb-12">
        {article.category && (
          <span className="text-xs font-semibold text-burgundy-wine uppercase tracking-wider mb-4 block">
            {article.category.name}
          </span>
        )}
        <h1 className="text-5xl font-serif font-bold text-burgundy-deep mb-6 leading-tight">
          {article.title}
        </h1>
        {article.excerpt && (
          <p className="text-xl text-charcoal/70 mb-6 italic">
            {article.excerpt}
          </p>
        )}
        <div className="flex items-center gap-4 text-sm text-charcoal/60 border-b border-burgundy-deep/20 pb-6">
          <span>{article.writer.display_name || article.writer.email}</span>
          <span>•</span>
          <ReadingTime article={article} />
          <span>•</span>
          <span>{new Date(article.published_at).toLocaleDateString()}</span>
        </div>
      </header>

      <div className="prose prose-lg text-charcoal/80 leading-relaxed">
        {article.content.map((block) => {
          const Renderer = BlockRenderers[block.type] || DefaultRenderer;
          return <Renderer key={block.id} block={block} />;
        })}
      </div>

      <footer className="mt-12 pt-8 border-t border-burgundy-deep/20">
        <div className="flex items-center gap-3 text-sm text-charcoal/60">
          <span className="font-medium text-charcoal">By</span>
          <span className="text-burgundy-wine">{article.writer.display_name || article.writer.email}</span>
        </div>
      </footer>
    </article>
  );
}
