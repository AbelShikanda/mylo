export interface Writer {
  id: string;
  email: string;
  display_name?: string;
  email_verified_at?: string | null;
}

export interface RegisterWriterRequest {
  email: string;
  password: string;
  password_confirmation: string;
  display_name?: string;
}

export interface LoginWriterRequest {
  email: string;
  password: string;
}

export interface AuthResponse {
  writer: Writer;
  token: string;
}
