<!DOCTYPE html>
<html>
<head>
    <style>
        table {font-family: arial, sans-serif;border-collapse: collapse;width: 100%;}
        td, th {border: 1px solid #dddddd;text-align: left;padding: 8px;}
        tr:nth-child(even) {background-color: #dddddd;}
        footer{position: absolute; bottom: 0;}
    </style>
</head>
<body>

<h2>Royalty Admin Activity Report</h2>
<h3>From: {{date("M d, Y", strtotime($from))}} - To: {{date("M d, Y", strtotime($to))}}</h3>

<table>
    <tr>
        <th>Action</th>
        <th>Time</th>
    </tr>
    @foreach($notifications as $notification)
        <tr>
            <td>{{$notification->text}}</td>
            <td>{{date("M d, Y h:i A ", strtotime($notification->created_at))}}</td>
        </tr>
    @endforeach
</table>
<footer>Generated from {{$_SERVER['REMOTE_ADDR']}} at {{date('M d, Y h:i A ')}}</footer>

</body>
</html>
