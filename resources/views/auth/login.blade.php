<!DOCTYPE html>
@include('admin.main.html')
<head>
    @include('admin.main.meta')
</head>
<body>
    <div class="wrapper">
        <div class="overflow-hidden auth-box align-items-center d-flex">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-4 col-md-6 col-sm-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4 auth-brand">
                                    <a href="{{ route('login') }}" class="logo-dark">
                                        <span class="gap-1 d-flex align-items-center">
                                            <img src="{{ asset('logo.webp') }}" alt="" class="w-25 rounded-circle">
                                            <span class="logo-text text-body fw-bold fs-xl">New Cairo Archive System</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('login') }}" class="logo-light">
                                        <span class="gap-1 d-flex align-items-center">
                                            <span class="avatar avatar-xs rounded-circle text-bg-dark">
                                                <span class="avatar-title">
                                                    <i data-lucide="sparkles" class="fs-md"></i>
                                                </span>
                                            </span>
                                            <span class="text-white logo-text fw-bold fs-xl">Biry Suits</span>
                                        </span>
                                    </a>
                                    <p class="mt-3 text-muted w-lg-75">Let’s get you signed in. Enter your email and password to continue.</p>
                                </div>
                                <div class="">
                                    <form action="{{ route('login') }}" method="post">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="Email" class="form-label" type="email" name="email" :value="old('email')" required autofocus autocomplete="username">Email address <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="email" name="email" class="mt-2 form-control" id="Email"  placeholder="you@example.com" required :messages="$errors->get('email')"  >
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="Password" class="form-label" :value="__('Password')" type="password" name="password" required autocomplete="current-password">Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="password" class="form-control" id="userPassword" placeholder="••••••••" required>
                                            </div>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="py-2 btn btn-primary fw-semibold">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.main.scripts')
</body>
</html>
