<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/dist/bundle.dist.css">
    <title>Document</title>
</head>
<body>

    <!-- <div id="app"></div> -->

    <form method="POST" id="signin" action="/api/v1/auth/signin">
        <input type="text" name="email">
        <input type="password" name="password">
        <button>submit</button>
    </form>

    <form method="POST" id="signup" action="/api/v1/auth/signup">
        <input type="text" name="email">
        <input type="password" name="password">
        <button>submit</button>
    </form>

    <button id="get">get</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="/dist/bundle.dist.js"></script>

</body>
</html>
