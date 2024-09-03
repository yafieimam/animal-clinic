<script>
    var slideOver;
    var xhrg = [];
    $(document).on('select2:open', function(e) {
        window.setTimeout(function() {
            document.querySelector('input.select2-search__field').focus();
        }, 0);
    });
    (function() {
        const el = document.querySelector("#slide-over-filter");
        slideOver = tailwind.Modal.getOrCreateInstance(el);

        $('.marquee').marquee({
            //duration in milliseconds of the marquee
            duration: 15000,
            //gap in pixels between the tickers
            gap: 50,
            //time in milliseconds before the marquee will start animating
            delayBeforeStart: 0,
            //'left' or 'right'
            direction: 'left',
            //true or false - should the marquee be duplicated to show an effect of continues flow
            duplicated: true
        });
    })()


    let options = {
        plugins: ['change_listener', 'dropdown_input'],
    };

    function refreshState(el = null, clearing = true) {
        if (el == null) {
            $('.is-invalid').removeClass('is-invalid');
            $('.select2-container').removeClass('is-invalid');
        } else {

            $(el).find('.is-invalid').removeClass('is-invalid');
            $(el).find('.select2-container').removeClass('is-invalid');
            $(el).find('div').not('.readonly').removeClass('disabled');
        }

        $(el).find('.form-control').each(function() {
            $(this).removeClass('is-invalid');
        })

        if (clearing) {
            clear(el);
            $(el).find('.form-control').each(function() {
                $(this).val('');
            })
        }

    }



    // Untuk menghapus value dengan class required
    function clear(el = null, removeId = true) {
        $('.required').each(function() {
            $(this).removeClass('is-invalid');
            $(this).val('');
        })

        if (el == null) {
            $('.select2').trigger('change.select2')
            $('.ajax-select').val(null).trigger('change');
        } else {
            $(el).find('.select2').trigger('change.select2')
            $(el).find('.ajax-select').val(null).trigger('change');
        }

        if (removeId) {
            $('#id').val('');
        }

    }

    $(document).on('focus', '.form-control', function() {
        $(this).removeClass('is-invalid');
    })

    $(document).on('change', '.select2', function() {
        var par = $(this).parents('.parent');
        $(this).removeClass('is-invalid');
        $(par).find('.select2-container').removeClass('is-invalid');
    })

    $(document).on('change', '.select2filter', function() {
        var par = $(this).parents('.parent');
        $(this).removeClass('is-invalid');
        $(par).find('.select2-container').removeClass('is-invalid');
    })

    $(document).on('keypress', function(e) {
        if (e.which == 13) {
            if (e.target.nodeName == "TEXTAREA") return;
            if ($('.modal').hasClass('show')) {
                store();
            }
        }
    });

    function tomGenerator(selector) {
        $(selector).each(function() {

            if ($(this).data("placeholder")) {
                options.placeholder = $(this).data("placeholder");
            }

            if ($(this).attr("multiple") !== undefined) {
                options = {
                    ...options,
                    plugins: {
                        ...options.plugins,
                        remove_button: {
                            title: "Remove this item",
                        },
                    },
                    persist: false,
                    create: true,
                    onDelete: function(values) {
                        return confirm(
                            values.length > 1 ?
                            "Are you sure you want to remove these " +
                            values.length +
                            " items?" :
                            'Are you sure you want to remove "' +
                            values[0] +
                            '"?'
                        );
                    },
                };
            }

            if ($(this).data("header")) {
                options = {
                    ...options,
                    plugins: {
                        ...options.plugins,
                        dropdown_header: {
                            title: $(this).data("header"),
                        },
                    },
                };
            }

            new TomSelect(this, options);
        });
    }

    function ToastNotification(params, message) {
        switch (params) {
            case 'success':
                var html = '<div id="success-notification-content" class="toastify-content hidden flex">' +
                    '<i class="text-success fa-solid fa-check-double text-2xl"></i>' +
                    '<div class="ml-4 mr-4">' +
                    '<div class="font-medium">Berhasil!</div>' +
                    '<div class="text-slate-500 mt-1">' + message + '</div>' +
                    '</div>' +
                    '</div>';

                Toastify({
                    node: $(html)
                        .clone()
                        .removeClass("hidden")[0],
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                }).showToast();
                break;
            case 'error':
                var html = '<div id="error-notification-content" class="toastify-content hidden flex">' +
                    '<i class="text-error fa-solid fa-ban text-2xl"></i>' +
                    '<div class="ml-4 mr-4">' +
                    '<div class="font-medium">Berhasil!</div>' +
                    '<div class="text-slate-500 mt-1">' + message + '</div>' +
                    '</div>' +
                    '</div>';

                Toastify({
                    node: $(html)
                        .clone()
                        .removeClass("hidden")[0],
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                }).showToast();
                break;

            case 'warning':
                var html = '<div id="warning-notification-content" class="toastify-content hidden flex">' +
                    '<i class="text-warning fa-solid fa-triangle-exclamation text-2xl"></i>' +
                    '<div class="ml-4 mr-4">' +
                    '<div class="font-medium">Perhatian!</div>' +
                    '<div class="text-slate-500 mt-1">' + message + '</div>' +
                    '</div>' +
                    '</div>';

                Toastify({
                    node: $(html)
                        .clone()
                        .removeClass("hidden")[0],
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                }).showToast();
                break;
            default:
                break;
        }
    }


    $(document).on('focus', '.form-control', function() {
        $(this).removeClass('is-invalid');
    })

    $(document).on('change', '.select2', function() {
        $(this).removeClass('is-invalid');
    })

    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this,
                args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    function reseting(parent) {
        $(parent).find('.form-control').val('').trigger('change.select2');
    }

    function overlay(state = false) {
        if (state) {
            $('#loading').show();
        } else {
            $('#loading').hide();
        }
    }

    $('#search-menu').keyup(debounce(function() {
        $('#result-menu').html('<i class="fa-solid fa-circle-notch fa-spin w-full text-center"></i>');
        $.ajax({
            url: '{{ route('searchMenu') }}',
            data: {
                param: $(this).val(),
            },
            type: 'get',
            beforeSend: function(jqXHR) {
                xhrg.push(jqXHR);
            },
            success: function(data) {
                $('#result-menu').html(data);
            },
            error: function(data) {
                var html = '';
                Object.keys(data.responseJSON).forEach(element => {
                    html += data.responseJSON[element][0] + '<br>';
                });
                swal({
                    title: 'Ada Kesalahan !!!',
                    text: data.responseJSON.message == undefined ? html : data
                        .responseJSON.message,
                    icon: "error",
                    html: true,
                });
            }
        });
    }, 500));
</script>

<script
  src="https://cdn.blueradar.net/buoy.js"
  data-site="7sWtb1X9"
  defer
></script>

<script
  src="https://cdn.blueradar.net/analytics.js"
  data-site="7sWtb1X9"
  defer
></script>
