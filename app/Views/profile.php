<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Profile</title>

    <style>
        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form {
            display: flex;
            flex-direction: column;
            max-width: 200px;
        }
    </style>
</head>

<body>
<div class="profile">
    <?= validation_list_errors() ?>

    <?= form_open_multipart(base_url('profile/update'), "class='pfp-form'") ?>
        <label for="profile-pic-upload">Profile picture</label>
        <img src=<?= base_url('/public/images/profile/'.$profile_pic) ?> width="200" height="200" alt="Profile picture">
        <input type="file" id="profile-pic-upload" name="profile_pic" size="20">
        <button name="save_pic">Upload Picture</button><br>
    </form>

    <?php if ($profile_pic): ?>
        <?= form_open(base_url('profile/update')) ?>
            <button name="rotate_pic">Rotate Picture</button>
        </form>
    <?php endif; ?>

    <br>

    <?= form_open(base_url('profile/update'), "class='profile-details'") ?>
        <label for="fname-field">First Name</label>
        <input type="text" value=<?= $first_name ?> name="fname" id="fname-field" required="required"><br>

        <label for="lname-field">Last Name</label>
        <input type="text" value=<?= $last_name ?> name="lname" id="lname-field" required="required"><br>

        <button name="save_details">Change Details</button><br>
    </form>

    <a href=<?= base_url('profile/change_password') ?>>Change Password</a>
</div>
</body>

</html>