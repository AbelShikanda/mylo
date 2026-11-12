export default function Dashboard() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-serif font-bold text-champagne mb-2">Administrator Dashboard</h1>
        <p className="text-rose-muted">Platform control centre — manage the Mylo publishing ecosystem.</p>
      </div>

      <div className="grid md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">Total Readers</p>
          <p className="text-2xl font-bold text-champagne">0</p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">Total Writers</p>
          <p className="text-2xl font-bold text-champagne">0</p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">Published Articles</p>
          <p className="text-2xl font-bold text-champagne">0</p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">Drafts</p>
          <p className="text-2xl font-bold text-champagne">0</p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">Total Books</p>
          <p className="text-2xl font-bold text-champagne">0</p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">Platform Revenue</p>
          <p className="text-2xl font-bold text-champagne">$0.00</p>
        </div>
      </div>
    </div>
  );
}
