<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Login</title>
    <style>
        body {
            display: flex;
            align-items: center;
            flex-direction: column;
            margin-top: 40px;
        }
        body form {
            display: flex;
            justify-content: space-around;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <?= validation_list_errors() ?>

    <?= form_open(base_url('login')) ?>
        <input type="email" placeholder="Email" required="required" name="email"><br>
        <input type="password" placeholder="Password" required="required" name="password"><br>
        <button type="submit">Log In</button><br>
        <label><input type="checkbox" name="remember">Remember me</label><br>
        <a href=<?= base_url('login/forgot_password') ?>>Forgot Password?</a>
    </form>
    <br>
    <a href=<?= base_url('registration') ?>>Register</a>
</body>

</html>