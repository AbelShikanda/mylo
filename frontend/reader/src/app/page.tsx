export default function Home() {
  return (
    <div className="space-y-8">
      <div className="text-center py-16">
        <h1 className="text-4xl font-serif font-bold text-burgundy-deep mb-4">
          Mylo Reader Portal
        </h1>
        <p className="text-lg text-charcoal/80 max-w-2xl mx-auto">
          Discover and enjoy premium independent digital magazine content.
          Content comes first — no social networking, no follower pressure.
        </p>
      </div>

      <div className="grid md:grid-cols-3 gap-6">
        <div className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
          <h3 className="font-serif font-bold text-burgundy-deep text-xl mb-3">Featured Stories</h3>
          <p className="text-charcoal/70 text-sm">Editorially curated stories from independent writers.</p>
        </div>
        <div className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
          <h3 className="font-serif font-bold text-burgundy-deep text-xl mb-3">Personalized Recommendations</h3>
          <p className="text-charcoal/70 text-sm">Based on your reading preferences and interests.</p>
        </div>
        <div className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
          <h3 className="font-serif font-bold text-burgundy-deep text-xl mb-3">Recommended Books</h3>
          <p className="text-charcoal/70 text-sm">Physical books and ebooks from independent authors.</p>
        </div>
      </div>
    </div>
  );
}
