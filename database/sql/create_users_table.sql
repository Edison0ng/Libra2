-- Jalankan script ini di Supabase Dashboard -> SQL Editor -> New Query

create table if not exists public.users (
  id          uuid primary key default gen_random_uuid(),
  fullname    text not null,
  username    text not null unique,
  email       text not null unique,
  password    text not null,           -- selalu berisi HASH bcrypt, bukan plaintext
  created_at  timestamptz not null default now()
);

-- Index tambahan untuk mempercepat pencarian saat login
create index if not exists idx_users_username on public.users (username);
create index if not exists idx_users_email on public.users (email);

-- Aktifkan Row Level Security. Sengaja TIDAK dibuatkan policy untuk role
-- anon/authenticated, karena semua akses ke tabel ini HARUS lewat backend
-- Laravel yang menggunakan Service Role Key (otomatis bypass RLS).
-- Ini mencegah tabel users diakses langsung dari browser memakai anon key.
alter table public.users enable row level security;
