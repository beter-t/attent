<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Change Password </title>
</head>

<body>
    <?= validation_list_errors() ?>

    <?= form_open(base_url('change_forgotten_password')) ?>
        <input type="password" placeholder="New Password" required="required" name="new_pw"><br>
        <input type="password" placeholder="Confirm Password" required="required" name="confirm_pw"><br>
        <button type="submit">Change Password</button><br>
    </form>
</body>

</html>