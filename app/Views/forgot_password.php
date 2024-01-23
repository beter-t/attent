<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Forgot Password </title>
</head>

<body>
    <?= validation_list_errors() ?>

    <?= form_open(base_url('login/forgot_password')) ?>
        <input type="email" placeholder="Email" required="required" name="email"><br>
        <button type="submit">Reset Password</button><br>
    </form>
</body>

</html>