<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <!-- <title>Mukha Web App - Artist ()</title> -->
    <title>Household Mapping and Profiling </title>

    <!-- <link rel="stylesheet" href="./css/custom-css.css"> -->
    <link href="./assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/loader.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./assets/sweet-alert/sweetalert2.min.css">
    <script src="./assets/sweet-alert/sweetalert2@11.js"></script>
</head>

<body>
    <div class="container-fluid" style="min-height: 100vh; background-image: url('./assets/img/Banner1.webp'); background-size: cover; background-position: center; position: relative;">
        <div style="position:absolute; inset:0; background: rgba(0,0,0,0.2);"></div>
        <div style="position:relative;">
            <div class="row min-vh-100 align-items-center justify-content-center g-0">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="col-12 col-md-6 col-lg-4">



                            <div class="card shadow-lg">
                                <div class="card-body p-4">
                                    <div class="text-center mb-3">
                                        <div class="mx-auto" style="width: 72px; height: 72px; border-radius: 18px;  display:flex; align-items:center; justify-content:center;">
                                            <!-- <i class="fas fa-house-user" style="color:#0d6efd; font-size: 30px;"></i> -->

                                            <img src="./assets/img/Logo.png" alt="logo" class="img-fluid">


                                        </div>
                                        <h3 class="mt-3 mb-1">Login</h3>
                                        <p class="text-muted mb-0" style="font-size: 0.95rem;">Household Mapping and Profiling</p>
                                    </div>

                                    <form method="post" action="admin/index.php" autocomplete="on" validate>
                                        <div class="mb-3">
                                            <label class="form-label" for="uname">Username</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="uname"
                                                name="uname"
                                                placeholder="Enter your username"
                                                required>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="password">Password</label>
                                            <div class="input-group">
                                                <input
                                                    type="password"
                                                    class="form-control"
                                                    id="password"
                                                    name="password"
                                                    placeholder="Enter your password"
                                                    required>
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Show/Hide password">
                                                    <i class="fas fa-eye-slash" id="togglePasswordIcon"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="d-grid mt-4">
                                            <button class="btn btn-primary btn-sm form-control" type="submit">
                                                <i class="fas fa-sign-in-alt me-2"></i>Sign in
                                            </button>
                                        </div>
                                        <!-- 
                                    <div class="text-center mt-3" style="font-size: 0.95rem;">
                                        <a href="#" class="text-decoration-none">Forgot password?</a>
                                    </div> -->
                                    </form>
                                </div>
                            </div>

                            <div class="text-center text-muted mt-3" style="font-size: 0.85rem;">
                                © Household Mapping and Profiling
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    (function() {
                        var btn = document.getElementById('togglePassword');
                        var input = document.getElementById('password');
                        var icon = document.getElementById('togglePasswordIcon');

                        if (!btn || !input || !icon) return;

                        btn.addEventListener('click', function() {
                            var isPassword = input.type === 'password';
                            input.type = isPassword ? 'text' : 'password';

                            icon.classList.toggle('fa-eye', !isPassword);
                            icon.classList.toggle('fa-eye-slash', isPassword);
                        });
                    })();
                </script>
</body>
<script src="./assets/dist/js/ajax.js" defer></script>
<script src="./assets/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="./js/BaseClass.js" defer></script>
<script src="./js/Notification.js" defer></script>
<script src="./js/LoadingScript.js" defer></script>


</html>