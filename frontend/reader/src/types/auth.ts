export interface Reader {
  id: string;
  email: string;
  display_name?: string;
  email_verified_at?: string | null;
}

export interface RegisterReaderRequest {
  email: string;
  password: string;
  password_confirmation: string;
  display_name?: string;
}

export interface LoginReaderRequest {
  email: string;
  password: string;
}

export interface AuthResponse {
  reader: Reader;
  token: string;
}
