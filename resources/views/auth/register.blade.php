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
                                    <p class="text-muted w-lg-75 mt-3">Create your Biry Suits account to manage access and enjoy the
                                        full admin experience.</p>
                                </div>

                                <div class="">
                                    <form action="{{ route('register') }}" method="post">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">First Name <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="first_name"
                                                    class="form-control mt-2 @error('first_name') is-invalid @enderror" id="first_name"
                                                    value="{{ old('first_name') }}" placeholder="Your first name" required
                                                    autofocus autocomplete="given-name">
                                            </div>
                                            @error('first_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">Last Name <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="last_name"
                                                    class="form-control mt-2 @error('last_name') is-invalid @enderror" id="last_name"
                                                    value="{{ old('last_name') }}" placeholder="Your last name" required
                                                    autocomplete="family-name">
                                            </div>
                                            @error('last_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email address <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="email" name="email"
                                                    class="form-control mt-2 @error('email') is-invalid @enderror" id="email"
                                                    value="{{ old('email') }}" placeholder="you@example.com" required
                                                    autocomplete="username">
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror" id="password"
                                                    placeholder="••••••••" required autocomplete="new-password">
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="password_confirmation"
                                                    class="form-control" id="password_confirmation"
                                                    placeholder="••••••••" required autocomplete="new-password">
                                            </div>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary fw-semibold py-2">Create
                                                Account</button>
                                        </div>
                                    </form>

                                    <p class="text-muted text-center mt-4 mb-0">
                                        Already registered?
                                        <a href="{{ route('login') }}"
                                            class="text-decoration-underline link-offset-3 fw-semibold">Sign in
                                            instead</a>
                                    </p>
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
    @include('dashboards.shared.scripts')
</body>

</html>
