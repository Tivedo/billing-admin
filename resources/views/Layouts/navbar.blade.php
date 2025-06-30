    <header>
        <nav class="navbar navbar-expand navbar-light navbar-top">
            <div class="container-fluid">
                <a class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>                

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-lg-0">

                    </ul>
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-menu d-flex">
                                <div class="user-name text-end me-3">
                                    <h6 class="mb-0 text-gray-600"></h6>
                                    <p class="mb-0 text-sm text-gray-600"></p>
                                </div>
                                <div class="user-img d-flex align-items-center">
                                    <div class="avatar avatar-md" id="avatar">

                                    </div>
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                            <li>
                                <h6 class="dropdown-header">Hello, Admin!</h6>
                            </li>
                            <li><a class="dropdown-item" href="#" id="logout-btn"><i
                                        class="icon-mid bi bi-box-arrow-left me-2" ></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <script src="{{ asset('assets/assets/compiled/js/app.js') }}"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

            <script type="text/javascript">
                $(document).ready(function () {
                    $('#logout-btn').on('click', function (e) {
                        e.preventDefault();

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You want to logout?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, logout!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const token = '{{ Session::get("jwt_token") }}';

                                axios.post('{{ url("api/auth/logout") }}', {}, {
                                    headers: {
                                        'Content-Type': 'application/json; charset=utf-8',
                                        'Accept': 'application/json',
                                        'Authorization': `Bearer ${token}`,
                                    }
                                }).then(response => {
                                    if (response.status === 200) {
                                        window.location.href = '{{ url("/") }}';
                                    }
                                }).catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to logout. Please try again.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            }
                        });
                    });
                });
            </script>
            <script type="text/javascript">
            </script>
        </nav>
    </header>


