<x-layouts.jamaah :title="'Profil Saya — PT. Zein Internasional'" activeTab="profile">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6 sm:space-y-8">

        {{-- 1. Kartu Header Profil & Foto --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-48 h-48 bg-[#EFF3EB]/60 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                    
                    {{-- Avatar / Foto Profil --}}
                    <div class="relative group shrink-0">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl overflow-hidden bg-[#1B3B2B] text-white flex items-center justify-center text-3xl font-bold shadow-lg shadow-[#1B3B2B]/20 ring-4 ring-[#EFF3EB] border border-[#CCD8C7]">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" 
                                     alt="Foto Profil {{ $user->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <span>{{ $user->initials }}</span>
                            @endif
                        </div>

                        {{-- Tombol Ganti Foto Overlay Cepat --}}
                        <label for="avatar_input" 
                               class="absolute -bottom-2 -right-2 p-2 rounded-2xl bg-[#1B3B2B] text-white hover:bg-[#132E22] transition-transform active:scale-95 shadow-md border-2 border-white cursor-pointer"
                               title="Pilih & Ganti Foto Profil">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                            </svg>
                        </label>
                    </div>

                    <div>
                        <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap mb-1.5">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/80 px-3 py-0.5 rounded-full border border-emerald-200">
                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Akun Jamaah Terverifikasi
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-[#12271E] tracking-tight">
                            {{ $user->name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-[#526057] mt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <span>{{ $user->email }}</span>
                            <span class="text-[#CCD8C7]">&bull;</span>
                            <span class="font-medium text-[#1B3B2B]">{{ $user->phone_formatted }}</span>
                        </p>
                    </div>
                </div>

                {{-- Form Aksi Foto Profil --}}
                <div class="flex flex-row sm:flex-col items-center sm:items-end justify-center gap-2">
                    <form id="photo-upload-form" action="{{ route('jamaah.profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="file" 
                               id="avatar_input" 
                               name="avatar" 
                               accept="image/jpeg,image/png,image/webp,image/jpg" 
                               class="hidden"
                               onchange="document.getElementById('photo-upload-form').submit();">
                    </form>

                    <label for="avatar_input" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E2EBDC] transition-colors border border-[#CCD8C7] cursor-pointer shadow-2xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <span>{{ $user->avatar ? 'Ganti Foto' : 'Unggah Foto' }}</span>
                    </label>

                    @if($user->avatar)
                        <form action="{{ route('jamaah.profile.photo.delete') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors border border-transparent hover:border-red-200 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                                <span>Hapus Foto</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @error('avatar')
                <div class="mt-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Pesan Sukses / Error Notifikasi --}}
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium leading-relaxed">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-800 flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span class="font-medium leading-relaxed">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 2. Formulir Informasi Pribadi --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs">
            <div class="border-b border-[#E0E7DC] pb-4 mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-[#12271E]">
                        Informasi Pribadi
                    </h2>
                    <p class="text-xs text-[#526057] mt-0.5">
                        Kelola data diri Anda untuk keperluan pendaftaran dan administrasi keberangkatan ibadah.
                    </p>
                </div>
            </div>

            <form action="{{ route('jamaah.profile.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    
                    {{-- Nama Lengkap --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">
                            Nama Lengkap (Sesuai KTP / Paspor) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required
                               placeholder="Contoh: Muhammad Arif Hasbi"
                               class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all @error('name') border-red-400 bg-red-50/50 @enderror">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email (Akun Login) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="email" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider">
                                Alamat Email
                            </label>
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700">
                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Email Utama
                            </span>
                        </div>
                        <input type="email" 
                               id="email" 
                               value="{{ $user->email }}" 
                               disabled 
                               class="w-full px-4 py-3 rounded-xl bg-[#EFF3EB]/60 border border-[#CCD8C7] text-sm text-[#526057] font-medium cursor-not-allowed">
                        <p class="text-[11px] text-[#526057]/80 mt-1">Email digunakan sebagai identitas login utama akun Anda.</p>
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="phone" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider">
                                Nomor WhatsApp / HP <span class="text-red-500">*</span>
                            </label>
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700">
                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Terverifikasi OTP
                            </span>
                        </div>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}" 
                               required
                               placeholder="Contoh: 081234567890"
                               class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all @error('phone') border-red-400 bg-red-50/50 @enderror">
                        @error('phone')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label for="gender" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">
                            Jenis Kelamin
                        </label>
                        <select id="gender" 
                                name="gender" 
                                class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all @error('gender') border-red-400 @enderror">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="laki-laki" {{ old('gender', $user->gender) === 'laki-laki' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="perempuan" {{ old('gender', $user->gender) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tempat Lahir --}}
                    <div>
                        <label for="birth_place" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">
                            Tempat Lahir
                        </label>
                        <input type="text" 
                               id="birth_place" 
                               name="birth_place" 
                               value="{{ old('birth_place', $user->birth_place) }}" 
                               placeholder="Contoh: Bandung"
                               class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all @error('birth_place') border-red-400 @enderror">
                        @error('birth_place')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label for="birth_date" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">
                            Tanggal Lahir
                        </label>
                        <input type="date" 
                               id="birth_date" 
                               name="birth_date" 
                               value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" 
                               class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all @error('birth_date') border-red-400 @enderror">
                        @error('birth_date')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">
                            Alamat Tempat Tinggal
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3" 
                                  placeholder="Contoh: Jl. Terusan Buah Batu No. 123, Kel. Batununggal, Kec. Bandung Kidul, Kota Bandung"
                                  class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all @error('address') border-red-400 @enderror">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="pt-4 border-t border-[#E0E7DC] flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-98 transition-all shadow-md shadow-[#1B3B2B]/20 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. Formulir Keamanan Akun & Ubah Kata Sandi --}}
        <div class="bg-white rounded-3xl border border-[#E0E7DC] p-6 sm:p-8 shadow-xs">
            <div class="border-b border-[#E0E7DC] pb-4 mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-[#12271E]">
                    Keamanan Akun
                </h2>
                <p class="text-xs text-[#526057] mt-0.5">
                    Perbarui kata sandi secara berkala untuk menjaga keamanan akun portal jamaah Anda.
                </p>
            </div>

            <form action="{{ route('jamaah.profile.password.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    
                    {{-- Kata Sandi Saat Ini --}}
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">
                            Kata Sandi Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               required
                               placeholder="••••••••"
                               class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all @error('current_password') border-red-400 bg-red-50/50 @enderror">
                        @error('current_password')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kata Sandi Baru --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">
                            Kata Sandi Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all @error('password') border-red-400 bg-red-50/50 @enderror">
                        @error('password')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Kata Sandi Baru --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-[#12271E] uppercase tracking-wider mb-1.5">
                            Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               placeholder="Ulangi kata sandi baru"
                               class="w-full px-4 py-3 rounded-xl bg-[#F8FAF7] border border-[#CCD8C7] text-sm text-[#12271E] font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B3B2B]/20 focus:border-[#1B3B2B] transition-all">
                    </div>

                </div>

                <div class="pt-4 border-t border-[#E0E7DC] flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-xs font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-98 transition-all shadow-md shadow-[#1B3B2B]/20 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</x-layouts.jamaah>
