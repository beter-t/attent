<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Email Verification</title>
</head>

<body>
    <?= validation_list_errors() ?>

    <?= form_open(base_url() . 'registration/verify') ?>
        <input type="text" placeholder="Verification Code" required="required" name="verif_code">
        <button type="submit">Verify</button><br>
    </form>
</body>

</html>