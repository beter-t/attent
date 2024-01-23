<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | Bookmarks</title>
    <style>
        #bookmarks {
            margin-left: 100px;
        }
    </style>
</head>

<body>
    <div id="bookmarks">
        <h2>Bookmarks</h2>
        <?php foreach($enrollments as $id => $subject) : ?>
            <h4><?= $subject['name'] ?></h4>
            <ul>
                <?php foreach($bookmarks[$subject['id']] as $post) : ?>
                    <li><a href=<?= base_url('forum/'.$id.'/'.$post['id']) ?>><?= $post['title'] ?></a></li>
                <?php endforeach; ?>
            </ul>
            
        <?php endforeach; ?>
    </div>
</body>

</html>