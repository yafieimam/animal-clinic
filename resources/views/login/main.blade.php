@extends('../layout/' . $layout)

@section('head')
    <title>Login - Amore Animal Clinic</title>
@endsection

@section('content')
    <div class="container sm:px-10">
        <div class="block xl:grid grid-cols-2 gap-4">
            <!-- BEGIN: Login Info -->
            <div class="hidden xl:flex flex-col min-h-screen">
                <a href="" class="-intro-x flex items-center pt-5">
                    <img alt="Amore Animal Clinic" class="w-20" src="{{ asset('dist/images/amoreboxy.png') }}">
                    <span class="text-white text-lg ml-3">
                        Amore Animal Clinic
                    </span>
                </a>
                <div class="my-auto">
                    <img alt="Amore Animal Clinic" class="-intro-x w-1/2 -mt-16"
                        src="{{ asset('dist/images/illustration.svg') }}">
                    <div class="-intro-x text-white font-medium text-4xl leading-tight mt-10">A few more clicks to <br> sign
                        in to your account.</div>
                    <div class="-intro-x mt-5 text-lg text-white text-opacity-70 dark:text-slate-400">Manage all your
                        e-commerce accounts in one place</div>
                </div>
            </div>
            <!-- END: Login Info -->
            <!-- BEGIN: Login Form -->
            <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                <form action="{{ route('login.check') }}" method="post" id="login-form"
                    class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                    <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">Sign In</h2>
                    @if ($errors->has('username'))
                        <div class="w-full rounded-md my-3">
                            <h5 class="flex">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li class="font-bold text-xl xl:text-xl text-center xl:text-left text-red-500">
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </h5>
                        </div>
                    @endif
                    <div class="intro-x mt-8">
                        <input id="username" type="text" class="intro-x login__input form-control py-3 px-4 block"
                            placeholder="Masukkan Username" name="username" value="" required
                            oninvalid="this.setCustomValidity('Silakan isi Username Anda')" oninput="setCustomValidity('')"
                            autofocus>


                        {{ csrf_field() }}
                        <div id="error-username" class="login__input-error text-danger mt-2"></div>

                        <input id="password" type="password" class="intro-x login__input form-control py-3 px-4 block mt-4"
                        placeholder="Masukkan Password" name="password" value="" required
                        oninvalid="this.setCustomValidity('Silakan isi Password Anda')" oninput="setCustomValidity('')">
                        <div id="error-password" class="login__input-error text-danger mt-2"></div>
                        <!-- An element to toggle between password visibility -->
                        <div class="intro-x flex text-slate-600 dark:text-slate-500 text-xs sm:text-sm mt-4">
                            <div class="flex items-center mr-auto">
                                <input type="checkbox" class="form-check-input border mr-2" onclick="myFunction()">
                                <label class="cursor-pointer select-none" for="remember-me">Show Password</label>
                            </div>
                            <a href="">Lupa Password Gan?</a>
                        </div>

                        {{-- <input id="password" type="password" class="intro-x login__input form-control py-3 px-4 block mt-4"
                            placeholder="Masukkan Password" name="password" value="" required
                            oninvalid="this.setCustomValidity('Silakan isi Password Anda')" oninput="setCustomValidity('')">
                        <div id="error-password" class="login__input-error text-danger mt-2"></div> --}}
                    </div>
                    {{-- <div class="intro-x flex text-slate-600 dark:text-slate-500 text-xs sm:text-sm mt-4">
                        <div class="flex items-center mr-auto">
                            <input id="remember-me" type="checkbox" class="form-check-input border mr-2">
                            <label class="cursor-pointer select-none" for="remember-me">Ingat saya</label>
                        </div>
                        <a href="">Lupa Password?</a>
                    </div> --}}
                    <div class="intro-x mt-5 xl:mt-8 text-center xl:text-left">
                        <button type="submit" class="btn btn-primary py-3 px-4 w-full xl:w-32 xl:mr-3 align-top">Log
                            In</button>
                    </div>
                </form>
            </div>
            <!-- END: Login Form -->
        </div>
    </div>
@endsection

@section('script')
    <script>
        function myFunction() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        (function() {
            async function login() {
                // Reset state
                $('#login-form').find('.login__input').removeClass('border-danger')
                $('#login-form').find('.login__input-error').html('')

                // Post form
                let username = $('#username').val()
                let password = $('#password').val()

                // Loading state
                $('#btn-login').html('<i data-loading-icon="oval" data-color="white" class="w-5 h-5 mx-auto"></i>')
                tailwind.svgLoader()
                await helper.delay(1500)

                $('login-form').submit();
            }

            $('#login-form').on('keyup', function(e) {
                if (e.keyCode === 13) {
                    login()
                }
            })

            $('#btn-login').on('click', function() {
                login()
            })
        })()
    </script>
@endsection
