<form action="../database/register.php" method="post">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">

                        <div class="mb-md-5 mt-md-4 pb-5">

                            <h2 class="fw-bold mb-2">REGISTER</h2>
                            <p class="text-white-50 mb-5">Please enter your username and password!</p>

                            <div class="form-outline form-white mb-4">
                                <input type="text" id="username" name="username" class="form-control form-control-lg" />
                                <label class="form-label" for="username">Username</label>
                            </div>

                            <div class="form-outline form-white mb-4">
                                <input type="password" id="password" name="password"
                                    class="form-control form-control-lg" />
                                <label class="form-label" for="password">Password</label>
                            </div>

                            <button class="btn btn-outline-light btn-lg px-5" type="submit">Sign Up</button>

                        </div>

                        <div>
                            <p class="mb-0">Already have an account? <a href="loginform.php"
                                    class="text-white-50 fw-bold">Log In</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>