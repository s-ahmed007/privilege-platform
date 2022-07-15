<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        footer {
            position: absolute;
            bottom: 0;
        }
    </style>
</head>
<body>

<h2>{{$title}}</h2>
<table>
    <tr>
        <th>Emails</th>
    </tr>
    <?php $i = 0;?>
    @foreach($emails as $email)
        <tr>
            @if(count($emails)-1==$i)
                <td>{{$email}}</td>
            @else
                <td>{{$email}},</td>
            @endif

        </tr>
        <?php $i++;?>
    @endforeach


</table>
<footer>Generated from {{$_SERVER['REMOTE_ADDR']}} at {{date('M d, Y h:i A ')}}</footer>

</body>
</html>
