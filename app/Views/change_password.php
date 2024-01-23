<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Change Password </title>
</head>

<body>
    <?= validation_list_errors() ?>

    <?= form_open(base_url('profile/change_password')) ?>
        <input type="password" placeholder="Current Password" required="required" name="curr_password"><br>
        <input type="password" placeholder="New Password" required="required" name="new_password"><br>
        <input type="password" placeholder="Confirm New Password" required="required" name="confirm_new_pw"><br>
        <button type="submit">Change Password</button><br>
    </form>
</body>

</html>