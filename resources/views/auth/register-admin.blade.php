<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Dashboard Aplikasi Document Generator</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        .vh-100 { height: 100vh; }
        .h-custom {
            /* Tinggi yang disesuaikan untuk menyisakan ruang untuk footer */
            height: calc(100% - 73px); 
        }
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }
        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
        }
        /* Styling untuk Form Outline ala MDBootstrap */
        .form-outline { position: relative; }
        .form-outline input:focus ~ label,
        .form-outline input:not(:placeholder-shown) ~ label {
            transform: translateY(-1.2rem) scale(0.8);
            color: #0d6efd;
            background-color: white;
            padding: 0 0.2rem;
        }
        .form-outline label {
            position: absolute;
            top: 0.75rem;
            left: 1rem;
            transition: all 0.2s ease-out;
            pointer-events: none;
            color: #6c757d;
        }

      /* Tambahkan Gradasi untuk Tombol Login User */
    .btn-gradient {
        /* Gradasi dari Biru ke Pink/Ungu, meniru template About Me */
        background: #1e30f3; /* Start color (Biru) */
        background: linear-gradient(135deg, #1e30f3 0%, #e21e80 100%); /* Gradasi utama */
        border: none; /* Hilangkan border agar gradasi terlihat mulus */
        transition: opacity 0.3s ease;

    /* === TAMBAHAN INI UNTUK WARNA PUTIH === */
        color: white !important; 
        /* !important memastikan warna putih mengalahkan default Bootstrap */
    }
    
    .btn-gradient:hover {
        opacity: 0.9; /* Sedikit efek hover */
        background-color: #1e30f3; /* Fallback */
    }

    /* ... kode CSS lainnya ... */
    </style>
</head>
<body>

@include('components.header')

<section class="vh-100 d-flex flex-column">
  <div class="container-fluid h-custom flex-grow-1">
    <div class="row d-flex justify-content-center align-items-center h-100">
      
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="https://unibless.co.id/wp-content/uploads/2021/01/organized-archive-searching-files-database_335657-3137.jpg"
          class="img-fluid" alt="Sample image">
      </div>
      
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        
        <form method="POST" action="{{ route('admin.register.post') }}">
            @csrf
            
          <div class="divider d-flex align-items-center my-4">
            <p class="text-center fw-bold mx-3 mb-0">Daftar Admin</p>
          </div>

          <div class="form-outline mb-4">
            <input type="text" id="name" name="name" class="form-control form-control-lg"
              placeholder=" " required />
            <label class="form-label" for="name">Nama Lengkap</label>
          </div>
          
          <div class="form-outline mb-4">
            <input type="email" id="email" name="email" class="form-control form-control-lg"
              placeholder=" " required />
            <label class="form-label" for="email">Alamat Email</label>
          </div>

          <div class="form-outline mb-3">
            <input type="password" id="password" name="password" class="form-control form-control-lg"
              placeholder=" " required />
            <label class="form-label" for="password">Password</label>
          </div>

          <div class="form-outline mb-3">
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg"
              placeholder=" " required />
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <div class="form-check mb-0">
              <input class="form-check-input me-2" type="checkbox" name="remember" value="1" id="form2Example3" />
              <label class="form-check-label" for="form2Example3">
                saya telah membaca dan menyetujui perjanjian pengguna serta kebijakan privasi
              </label>
            </div>
          </div>

          <div class="text-center text-lg-start mt-4 pt-2 d-grid gap-2">
            
            <button type="submit" name="role" value="user" class="btn btn-gradient btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;">
                <i class="fa fa-paper-plane me-2"></i> Daftar
            </button>
          </div>
        </form>
        <div class="d-flex justify-content-between align-items-center mt-3 pt-1 mb-0">
             <p class="small fw-bold mb-0">Sudah punya akun? <a href="{{ url('/login-admin') }}" class="link-primary">Login</a></p>
        </div>

      </div>
    </div>
  </div>
  
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@include('components.about')

@include('components.footer')
</body>
</html>