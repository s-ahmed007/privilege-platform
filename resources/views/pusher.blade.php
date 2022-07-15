<!DOCTYPE html>
<html>
<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>

    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('9541afe7f46663b87a4a', {
            cluster: 'ap2',
            forceTLS: true
        });

        var channel = pusher.subscribe('like');
        channel.bind('like-review', function(data) {
            // alert(JSON.stringify(data));
        });
    </script>
</head>
<body>
<h1>Pusher Test</h1>
<p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
</p>
<script src="{{asset('js/jquery.min.js')}}"></script>

{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>--}}
</body>
</html>