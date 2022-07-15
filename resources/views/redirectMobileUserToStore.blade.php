<!DOCTYPE html>
<html>
<head>
    <title>Download Royalty app now!</title>
</head>
<body>

<script>
    function getMobileOperatingSystem() {
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;

        if( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) ){
            window.location.href = 'http://bit.ly/RBDIOSAPP';
        }else if( userAgent.match( /Android/i ) ){
            window.location.href = 'http://bit.ly/RBDANDROID';
        }else{
            window.location.href = 'https://royaltybd.com';
        }
    }
    getMobileOperatingSystem();
</script>
</body>
</html>