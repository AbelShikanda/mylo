export default function Dashboard() {
  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">Writer Dashboard</h1>
        <p className="text-charcoal/70">Your independent publishing workspace.</p>
      </div>

      <div className="grid md:grid-cols-3 gap-6">
        <div className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
          <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-2">Articles</h3>
          <p className="text-2xl font-bold text-charcoal">0</p>
          <p className="text-charcoal/60 text-sm mt-2">Published articles</p>
        </div>
        <div className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
          <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-2">Drafts</h3>
          <p className="text-2xl font-bold text-charcoal">0</p>
          <p className="text-charcoal/60 text-sm mt-2">Unpublished drafts</p>
        </div>
        <div className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
          <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-2">Earnings</h3>
          <p className="text-2xl font-bold text-charcoal">$0.00</p>
          <p className="text-charcoal/60 text-sm mt-2">Available balance</p>
        </div>
      </div>

      <div className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
        <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-4">Quick Actions</h3>
        <div className="flex gap-4">
          <a href="/create" className="px-6 py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors">
            Create New Article
          </a>
          <a href="/drafts" className="px-6 py-3 border border-burgundy-deep/30 text-burgundy-deep font-medium rounded-md hover:bg-burgundy-deep/50 transition-colors">
            View Drafts
          </a>
        </div>
      </div>
    </div>
  );
}
