<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Registration</title>
</head>

<body>
    <?= validation_list_errors() ?>

    <?= form_open(base_url('registration')) ?>
        <input type="email" placeholder="Email" required="required" name="email"><br>
        <input type="text" placeholder="Username" required="required" name="username"><br>
        <input type="text" placeholder="First Name" required="required" name="fname"><br>
        <input type="text" placeholder="Last Name" required="required" name="lname"><br>
        <input type="password" placeholder="Password" required="required" name="password"><br>
        <input type="password" placeholder="Confirm Password" required="required" name="confirm_pw"><br>
        <input type="checkbox" name="is_instructor" value="instructor">
        <label for="is_instructor">I am an instructor</label><br>
        <button type="submit">Sign Up</button><br>
    </form>
</body>

</html>