<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #global-nav {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 50px;
        }
        #global-nav a {
            width: 80px;
            margin-left: auto;
            margin-right: auto;
        }
        #logo {
            margin-right: auto;
        }
    </style>
</head>

<body>
    <nav id="global-nav">
        <a id="logo" href=<?= base_url() ?>>attent</a>
        <a href=<?= base_url('bookmarks') ?>>Bookmarks</a>
        <a href=<?= base_url('dashboard') ?>>Dashboard</a>
        <a href=<?= base_url('logout') ?>>Logout</a>
        <a href=<?= base_url('profile') ?>>Profile</a>
    </nav>
</body>

</html>