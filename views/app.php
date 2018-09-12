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
    <p>signin</p>
    <form method="POST" id="signin" action="/api/v1/auth/signin">
        <input type="text" name="email" placeholder="email">
        <input type="password" name="password" placeholder="password">
        <button>submit</button>
    </form>

    <p>signup</p>
    <form method="POST" id="signup" action="/api/v1/auth/signup">
        <input type="text" name="email" placeholder="email">
        <input type="password" name="password" placeholder="password">
        <button>submit</button>
    </form>

    <a href="/api/v1/auth/signout" id="signout">signout</a>
    <br><br>
    <p>get user</p>
    <button id="get">get</button>
    <br>
    <br>
    <hr>
    <br>

    <p>create partner</p>
    <form id="create-partner" method="POST" action="/api/v1/oauth/partner">
        <input type="text" name="name" placeholder="name">
        <input type="text" name="desc" placeholder="desc">
        <button>submit</button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="/dist/bundle.dist.js"></script>

</body>
</html>
