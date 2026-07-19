<?php
session_start();
ob_start();

if (isset($_COOKIE['UserID'])) {
    ob_end_flush(header("Location: admin/index.php"));
}


?>


<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <!-- <title>Mukha Web App - Artist ()</title> -->
    <title>Household Mapping and Profiling </title>
    <link rel="icon" type="image/png" href="assets/img/Logo.png" />
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
                                    </div>

                                    <form autocomplete="on" validate id="loginForm">
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
                                            <button class="btn btn-primary btn-sm form-control" type="submit" id="login_btn">
                                                <i class="fas fa-sign-in-alt me-2"></i>Sign in
                                            </button>
                                        </div>
                                        <!-- 
                                    <div class="text-center mt-3" style="font-size: 0.95rem;">
                                        <a href="#" class="text-decoration-none">Forgot password?</a>
                                    </div> -->
                                    </form>
                                    <div class="text-center text-muted mt-3 mt-0 pb-0" style="font-size: 0.85rem;">
                                        © Household Mapping and Profiling
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>


</body>
<script src="./assets/dist/js/ajax.js"></script>
<script src="./assets/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="./js/BaseClass.js" defer></script>
<script src="./js/Notification.js" defer></script>
<script defer>
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


    $(document).on("submit", "#loginForm", async function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append("loginUser", true);

        // Set loading state
        $("#login_btn").html(
            "<div class='text-center'><i class='spinner-border spinner-border-sm'></i></div>"
        );
        document.getElementById("login_btn").disabled = true;
        console.log("asdd")

        try {
            const response = await fetch("admin/inputConfig.php", {
                method: "POST",
                body: formData
            });
            // Fix 1: Corrected spelling from 'reposnse' to '!response.ok'
            if (!response.ok) {
                throw new Error(`Server status ${response.status}`);
            }

            // Fix 2: Parse the response body into usable JSON data
            const res = await response.json();
            if (res.status == 200) {
                await ClsAlert({
                    icon: "success",
                    title: "Log in Successfully"
                })

                var url = new URL(window.location.href);
                var params = new URLSearchParams(url.search);

                if (params.get("UserID")) {
                    window.location.reload();
                } else {
                    window.location.href = res.redirect;
                }
            } else {
                ClsAlert({
                    icon: "error",
                    title: res.message
                });
                resetLoginButton();
            }

        } catch (error) {
            // Fix 3: Safely catches network drops, invalid JSON parsing, or 500 server crashes
            console.error("Login Process Error:", error);
            ClsAlert({
                icon: "error",
                title: "Something went wrong. Please try again."
            });
            resetLoginButton();
        }
    });

    // Helper function to restore the button state cleanly
    function resetLoginButton() {
        $("#login_btn").html('<i class="fas fa-sign-in-alt me-2"></i>Sign in');
        document.getElementById("login_btn").disabled = false;
    }
</script>

</html>