@extends('../layout/' . $layout)

@section('subhead')
    <title>{{ convertSlug($global['title']) }}</title>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Profile Layout</h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- BEGIN: Profile Menu -->
        <div class="col-span-12 lg:col-span-4 2xl:col-span-3 flex lg:block flex-col-reverse">
            <div class="intro-y box mt-5 lg:mt-0">
                <div class="relative flex items-center p-5">
                    <div class="w-12 h-12 image-fit">
                        @if (Auth::user()->image != null)
                            <img alt="Amore Animal Clinic" class="rounded-full"
                                src="{{ url('/') . '/' . Auth::user()->image }}">
                        @else
                            <img alt="Amore Animal Clinic" class="rounded-full"
                                src="{{ asset('dist/images/amoreboxy.svg') }}">
                        @endif
                    </div>
                    <div class="ml-4 mr-auto">
                        <div class="font-medium text-base">{{ Auth::user()->name }}</div>
                        <div class="text-slate-500">{{ Auth::user()->Role->name }}</div>
                    </div>
                </div>
                <div class="p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                    <a class="flex items-center text-primary mt-5" href="">
                        <i data-lucide="box" class="w-4 h-4 mr-2"></i> Account Settings
                    </a>
                </div>
            </div>
        </div>
        <!-- END: Profile Menu -->
        <div class="col-span-12 lg:col-span-8 2xl:col-span-9">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: Daily Sales -->
                <div class="intro-y box col-span-12 2xl:col-span-6">
                    <div class="flex items-center px-5 py-5 sm:py-3 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Edit Profile</h2>
                        <div class="dropdown ml-auto sm:hidden">
                            <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false"
                                data-tw-toggle="dropdown">
                                <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i>
                            </a>
                            <div class="dropdown-menu w-40">
                                <ul class="dropdown-content">
                                    <li>
                                        <a href="javascript:;" class="dropdown-item">
                                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Excel
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <form class="grid grid-cols-12  gap-4 gap-y-3" enctype="multipart/form-data"
                            action="{{ route('storeProfile') }}" method="POST" id="form-data">
                            @method('POST')
                            @csrf
                            @if ($errors->any())
                                <div class="col-span-12">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li class="text-danger">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="col-span-12">
                                <input type="file" class="dropify text-sm" id="dropify" name="image"
                                    data-allowed-file-extensions="jpeg png jpg">
                            </div>
                            <div class="col-span-12">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control required" id="name"
                                    value="{{ Auth::user()->name }}" name="name" readonly>
                            </div>
                            <div class="col-span-12 md:col-span-6  parent">
                                <label class="form-label">Nama Panggilan</label>
                                <div class="input-group parent">
                                    <div class="input-group-text"><i class="fas fa-user-alt"></i></div>
                                    <input type="text" class="form-control required" id="nama_panggilan"
                                        value="{{ Auth::user()->nama_panggilan }}" name="nama_panggilan">
                                </div>
                            </div>
                            <div class="col-span-12 md:col-span-6  parent">
                                <label class="form-label">Email</label>
                                <div class="input-group parent">
                                    <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                                    <input type="text" class="form-control required" id="email"
                                        value="{{ Auth::user()->email }}" name="email">
                                </div>
                            </div>
                            <div class="col-span-12 md:col-span-6  parent">
                                <label class="form-label">Telpon</label>
                                <div class="input-group parent">
                                    <div class="input-group-text"><i class="fas fa-phone"></i></div>
                                    <input type="text" class="form-control required" id="telpon"
                                        value="{{ Auth::user()->karyawan->telpon }}" name="telpon">
                                </div>
                            </div>
                            <div class="col-span-12 md:col-span-6 parent">
                                <label class="form-label">Password</label>
                                <div class="input-group parent">
                                    <button type="button" class="btn btn-primary input-group-text"
                                        onclick="generatePassword()">
                                        GENERATE
                                    </button>
                                    <input type="text" class="form-control" id="password" name="password"
                                        placeholder="Kosongi jika tidak ingin merubah password"
                                        value="{{ Auth::user()->password_masked }}">
                                </div>
                            </div>
                            <div class="col-span-12 text-right">
                                <button type="button" class="btn btn-primary w-20" id="simpan"
                                    onclick="store()">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- END: Daily Sales -->
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        @if (session('status') == 1)
            ToastNotification('success', 'Berhasil merubah profile');
        @endif
        (function() {
            $('.dropify').dropify();
            Inputmask("999999999999").mask('#telpon');

            var url = "{{ url('/') }}" + '/' + '{{ Auth::user()->image }}';
            var imagenUrl = url;
            var drEvent = $('.dropify').dropify({
                defaultFile: imagenUrl,
            });

            drEvent = drEvent.data('dropify');
            drEvent.resetPreview();
            drEvent.clearElement();
            drEvent.settings.defaultFile = imagenUrl;
            drEvent.destroy();
            drEvent.init();
        })()

        function generatePassword() {
            var length = 8,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";

            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }

            $('#password').val(retVal);
        }

        function store(params) {
            var validation = 0;
            $('#form-data .required').each(function() {
                var par = $(this).parents('.parent');
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).addClass('is-invalid');
                    $(par).find('.select2-container').addClass('is-invalid');
                    validation++
                }
            })

            if (validation != 0) {
                ToastNotification('warning', 'Semua data harus diisi');
                return false;
            }

            var previousWindowKeyDown = window.onkeydown;

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    overlay(true)
                    window.onkeydown = previousWindowKeyDown;
                    $("#form-data").submit();
                }
            })
        }
    </script>
@endsection
