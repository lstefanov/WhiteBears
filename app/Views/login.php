<?=$this->extend("layouts/login")?>

<?=$this->section("content")?>

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Вход в системата</h1>
                                    </div>
                                    <form class="user" method="post" action="<?= base_url('/login') ?>">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user"
                                                   id="username" name="username"
                                                   placeholder="Потребителско име" required />
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password"
                                                   id="exampleInputPassword" placeholder="Парола" required />
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Вход
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

<?=$this->endSection()?>