export interface Administrator {
  id: string;
  email: string;
  display_name?: string;
  email_verified_at?: string | null;
}

export interface LoginAdminRequest {
  email: string;
  password: string;
}

export interface AuthResponse {
  administrator: Administrator;
  token: string;
}
