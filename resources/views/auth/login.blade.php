<!DOCTYPE html>
@include('dashboards.shared.html')

<head>
    @include('dashboards.shared.meta')
</head>

<body>
    <div class="wrapper">
        <div class="auth-box overflow-hidden align-items-center d-flex">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-4 col-md-6 col-sm-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="auth-brand mb-4">
                                    <a href="{{ route('login') }}" class="logo-dark">
                                        <span class="d-flex align-items-center gap-1">
                                            {{-- <span class="avatar avatar-xs rounded-circle text-bg-dark">
                                                <span class="avatar-title">
                                                    <i data-lucide="sparkles" class="fs-md"></i>
                                                </span>
                                            </span> --}}
                                            <img src="{{ asset('logo.webp') }}" alt="" class="w-25 rounded-circle">
                                            <span class="logo-text text-body fw-bold fs-xl">New Cairo Archive System</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('login') }}" class="logo-light">
                                        <span class="d-flex align-items-center gap-1">
                                            <span class="avatar avatar-xs rounded-circle text-bg-dark">
                                                <span class="avatar-title">
                                                    <i data-lucide="sparkles" class="fs-md"></i>
                                                </span>
                                            </span>
                                            <span class="logo-text text-white fw-bold fs-xl">Biry Suits</span>
                                        </span>
                                    </a>
                                    <p class="text-muted w-lg-75 mt-3">Let’s get you signed in. Enter your email and password to continue.</p>
                                </div>

                                <div class="">
                                    <form action="{{ route('login') }}" method="post">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="Email" class="form-label" type="email" name="email" :value="old('email')" required autofocus autocomplete="username">Email address <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="email" name="email" class="form-control mt-2" id="Email"  placeholder="you@example.com" required :messages="$errors->get('email')"  >
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="Password" class="form-label" :value="__('Password')" type="password" name="password" required autocomplete="current-password">Password <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="password" class="form-control" id="userPassword"
                                                    placeholder="••••••••" required>
                                            </div>
                                        </div>

                                        {{-- <div class="d-flex justify-content-between align-items-center mb-3">
                                            <a href="{{ route('password.request') }}"
                                                class="text-decoration-underline link-offset-3 text-muted">Forgot
                                                Password?</a>
                                        </div> --}}

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary fw-semibold py-2">Sign
                                                In</button>
                                        </div>
                                    </form>

                                    {{-- <p class="text-muted text-center mt-4 mb-0">
                                        New here? <a href="{{ route('register') }}"
                                            class="text-decoration-underline link-offset-3 fw-semibold">Create an
                                            account</a>
                                    </p> --}}
                                </div>
                            </div>
                        </div>
                        <p class="text-center text-muted mt-4 mb-0">
                            ©
                            <script>
                                document.write(new Date().getFullYear())
                            </script> Biry Suits — by <span class="fw-semibold">HossamXstudios</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- @include('dashboards.shared.theme_settings') --}}
    @include('dashboards.shared.scripts')
</body>

</html>
