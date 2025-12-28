<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    
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

    /* ... kode CSS yang sudah ada ... */

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
        <img src="https://www.marketeers.com/_next/image/?url=https%3A%2F%2Froom.marketeers.com%2Fwp-content%2Fuploads%2F2023%2F03%2F162796896_presentation-wide_normal_none.jpg&w=1920&q=75"
          class="img-fluid" alt="Sample image">
      </div>
      
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">

        {{-- Notif sukses / error --}}
                    <div class="container mt-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    </div>
        
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            
          <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
            <p class="lead fw-normal mb-0 me-3">Login Cepat:</p>
            <button type="button" class="btn btn-primary btn-floating mx-1" title="Login via Facebook"><i class="fab fa-facebook-f"></i></button>
            <button type="button" class="btn btn-primary btn-floating mx-1" title="Login via Twitter"><i class="fab fa-twitter"></i></button>
            <button type="button" class="btn btn-primary btn-floating mx-1" title="Login via LinkedIn"><i class="fab fa-linkedin-in"></i></button>
          </div>

          <div class="divider d-flex align-items-center my-4">
            <p class="text-center fw-bold mx-3 mb-0">Atau</p>
          </div>

          <div class="form-outline mb-4">
            <input type="email" id="form3Example3" name="email" class="form-control form-control-lg"
              placeholder=" " required />
            <label class="form-label" for="form3Example3">Email address</label>
          </div>

          <div class="form-outline mb-3">
            <input type="password" id="form3Example4" name="password" class="form-control form-control-lg"
              placeholder=" " required />
            <label class="form-label" for="form3Example4">Password</label>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <div class="form-check mb-0">
              <input class="form-check-input me-2" type="checkbox" name="remember" value="1" id="form2Example3" />
              <label class="form-check-label" for="form2Example3">
                Ingat Saya
              </label>
            </div>
            <a href="#!" class="text-body">Lupa password?</a>
          </div>

          <div class="text-center text-lg-start mt-4 pt-2 d-grid gap-2">
            
            <button type="submit" name="role" value="user" class="btn btn-gradient btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;">
                <i class="fa fa-user-secret me-2" aria-hidden="true"></i> Login
            </button>
          </div>
        </form>
        <div class="d-flex justify-content-between align-items-center mt-3 pt-1 mb-0">
             <p class="small fw-bold mb-0">Ke halaman user? <a href="{{ url('/login') }}" class="link-primary">Login</a></p>
             <p class="small fw-bold mb-0">Belum punya akun? <a href="{{ url('/register-admin') }}" class="link-primary">Daftar</a></p>
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