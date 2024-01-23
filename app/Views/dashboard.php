<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Dashboard</title>

    <style>
        form, .subjects {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <?= form_open(base_url('dashboard')) ?>
        <input type="text" placeholder="Subject" name="subject">
        <button><?= $user_type === 'instructor' ? 'Create' : 'Enrol' ?></button>
    </form>

    <div class="subjects">
        <h3>Subjects</h3>
        <?php foreach($enrollments as $id => $subject) : ?>
            <a href=<?= base_url('forum/' . $subject['id']) ?> type="submit" name=<?= $subject['name'] ?>><?= $subject['name'] ?></a>
        <?php endforeach; ?>
    </div>
</body>

</html>