<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="{{url('http://1905yangkai.comcto.com/wx/guanlido/'.$code)}}" method="post">
    @csrf
    第一节课: <input type="text" name="a1"><br>
    第二节课: <input type="text" name="a2"><br>
    第三节课: <input type="text" name="a3"><br>
    第四节课: <input type="text" name="a4"><br>
    <input type="submit" value="提交">
</form>
</body>
</html>