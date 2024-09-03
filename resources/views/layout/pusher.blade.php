<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;

    var pusher = new Pusher('9cd8b5ebac6374f2d2db', {
        cluster: 'ap1',
        authEndpoint: "{{ route('broadcastingAuth') }}",
        auth: {
            headers: {
                'X-CSRF-Token': "{{ csrf_token() }}",
            },
        },
    });

    var channel = pusher.subscribe('private-App.Models.User.{{ Auth::user()->id }}');
</script>
