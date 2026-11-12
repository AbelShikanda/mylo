export interface LoginAdminRequest {
  email: string;
  password: string;
}

export interface Administrator {
  id: string;
  email: string;
  display_name?: string;
  email_verified_at?: string | null;
}

export interface Reader {
  id: string;
  email: string;
  display_name?: string;
  email_verified_at?: string | null;
}

export interface Writer {
  id: string;
  email: string;
  display_name?: string;
  email_verified_at?: string | null;
}

export interface Category {
  id: string;
  name: string;
  slug: string;
  description?: string;
  image?: string;
  is_featured: boolean;
  sort_order: number;
  is_active: boolean;
}

export interface AuditLog {
  id: string;
  action: string;
  subject_type: string;
  subject_id?: string;
  old_values?: any;
  new_values?: any;
  ip?: string;
  user_agent?: string;
  created_at: string;
}

export interface Role {
  id: string;
  name: string;
  slug: string;
  description?: string;
}

export interface Permission {
  id: string;
  name: string;
  slug: string;
  description?: string;
}

export interface DashboardStats {
  users: {
    total_readers: number;
    total_writers: number;
    new_registrations: number;
    active_accounts: number;
    suspended_accounts: number;
  };
  publishing: {
    total_articles: number;
    published_articles: number;
    drafts: number;
    scheduled_articles: number;
    recently_published: number;
    reported_articles: number;
  };
  engagement: {
    article_views: number;
    likes: number;
    comments: number;
    popular_articles: any[];
    popular_categories: any[];
  };
  marketplace: {
    total_books: number;
    ebooks: number;
    physical_books: number;
    orders: number;
    completed_sales: number;
    pending_orders: number;
  };
  finance: {
    contributions: number;
    book_sales: number;
    platform_revenue: number;
    writer_earnings: number;
    pending_payouts: number;
    refunds: number;
    failed_transactions: number;
  };
}
