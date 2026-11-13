export const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';

export interface ApiResponse<T> {
  data: T;
  meta?: { pagination?: any };
  errors?: any[];
}

class ApiClient {
  private baseURL: string;
  private token: string | null = null;

  constructor(baseURL: string) {
    this.baseURL = baseURL;
  }

  setToken(token: string | null) {
    this.token = token;
  }

  async request<T>(
    endpoint: string,
    options: RequestInit = {}
  ): Promise<ApiResponse<T>> {
    const url = `${this.baseURL}${endpoint}`;
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      ...(options.headers as Record<string, string>),
    };

    let token = this.token;
    if (!token && typeof window !== 'undefined') {
      token = window.localStorage.getItem('mylo_admin_token');
    }
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(url, {
      ...options,
      headers,
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData?.message || `API error: ${response.status}`);
    }

    return response.json();
  }

  get<T>(endpoint: string, options?: RequestInit) {
    return this.request<T>(endpoint, { ...options, method: 'GET' });
  }

  post<T>(endpoint: string, body?: any, options?: RequestInit) {
    return this.request<T>(endpoint, {
      ...options,
      method: 'POST',
      body: body ? JSON.stringify(body) : undefined,
    });
  }

  put<T>(endpoint: string, body?: any, options?: RequestInit) {
    return this.request<T>(endpoint, {
      ...options,
      method: 'PUT',
      body: body ? JSON.stringify(body) : undefined,
    });
  }

  delete<T>(endpoint: string, options?: RequestInit) {
    return this.request<T>(endpoint, { ...options, method: 'DELETE' });
  }
}

export const apiClient = new ApiClient(API_BASE_URL);


export function unwrapList<T>(body: unknown): T[] {
  const d = (body as { data?: unknown } | null | undefined)?.data;
  if (Array.isArray(d)) return d as T[];
  if (d && Array.isArray((d as { data?: unknown[] }).data)) return (d as { data: T[] }).data;
  return [];
}

export function unwrapData<T>(body: unknown): T | null {
  const d = (body as { data?: unknown } | null | undefined)?.data;
  if (d && !Array.isArray(d) && typeof d === 'object' && 'data' in (d as object)) {
    return (d as { data: T }).data;
  }
  return (d ?? null) as T | null;
}
