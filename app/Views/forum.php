<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attent | <?= isset($subjectID) ? $subject['name'] : 'Forum' ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/basic.min.css" rel="stylesheet">
    <style>
        #subjects-bar, #posts-bar, #post-area {
            overflow-x: hidden;
            overflow-y: scroll;
            height: 90%;
            position: fixed;
            margin-top: 40px;
        }
        #subjects-bar {
            top: 50px;
            left: 1%;
            width: 25%;
        }
        #posts-bar {
            top: 50px;
            left: 27%;
            width: 30%;
        }
        #post-area {
            top: 50px;
            left: 58%;
            width: 42%;
        }
        #write-post {
            display: block;
        }
        #post-draft {
            display: none;
        }
        .post-title, #bookmark {
            display: inline;
        }
        .comment {
            border-style: groove;
            border-radius: 5px 25px;
        }
        .author {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div id="subjects-bar">
        <h3>Subjects</h3>
        <button id="write-post">New Post</button>
        <ul>
            <?php foreach($enrollments as $id => $enrollment) : ?>
                <li><a href=<?= base_url('forum/'.$enrollment['subject_id']) ?>><?= $enrollment['subject_name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="posts-bar">
        <h3>Posts</h3>
        <?= form_open(base_url('forum/'.$subjectID)) ?>
            <input type="text" id="search-bar" name="search" onkeyup="suggest(this.value)">
            <button name="search_posts">Search</button>
            <button name="cancel_search">Cancel</button>
        </form>
        <ul id="suggestions">
            <?php foreach($allPosts as $key => $post) : ?>
                <li><a href=<?= base_url('forum/'.$subjectID.'/'.$post['id']) ?>><?= $post['title'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="post-area">
        <?= form_open_multipart(base_url('forum/'.$subjectID), 'id="post-draft" class="dropzone"') ?>
            <?= validation_list_errors() ?>

            <input type="radio" id="question_type_btn" name="post_type" value="question" <?= isset($postDraft['post_type']) && $postDraft['post_type'] === 'question' ? 'checked' : '' ?>>
            <label for="question_type_btn">Question</label><br>
            <input type="radio" id="note_type_btn" name="post_type" value="note" <?= isset($postDraft['post_type']) && $postDraft['post_type'] === 'note' ? 'checked' : '' ?> required>
            <label for="note_type_btn">Note</label><br>

            <input type="checkbox" name="anonymous" value="true" <?= isset($postDraft['anonymous']) && $postDraft['anonymous'] ? 'checked' : '' ?>>
            <label for="anonymous">Anonymous</label><br>

            <label for="category-dropdown">Category</label>
            <select name="category" id="category-dropdown" required="required">
                <?php foreach($categories as $key => $categoryItem) : ?>
                    <option value=<?= $categoryItem['name'] ?> <?= isset($postDraft['category']) && $postDraft['category'] ? 'selected' : '' ?>><?= $categoryItem['name'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <input type="text" name="title" placeholder="Title" required="required" value=<?= isset($postDraft['title']) && $postDraft['title'] ?: '' ?>><br>

            <textarea name="body" placeholder="Description" id="post-body" value=<?= isset($postDraft['body']) && $postDraft['body'] ?: '' ?>></textarea><br>

            <!-- <label for="image-selection">Attach images</label>
            <input type="file" name="images[]" multiple id="image-selection"><br> -->

            <button type="reset" id="cancel-post">Cancel</button>
            <button name="submit_post" id="submit-post">Submit</button>
        </form>
        <div id="post">
            <?php if ($postData) : ?>
                <h3 class="post-title"><?= $postData['title'] ?></h3>

                <input type="checkbox" name="like_btn" value="true" id="like-btn" <?= $postData['liked'] ? 'checked' : '' ?>>
                <label for="like-btn">Like</label>

                <input type="checkbox" name="bookmark_btn" value="true" id="bookmark-btn" <?= $postData['bookmarked'] ? 'checked' : '' ?>>
                <label for="bookmark-btn">Bookmark</label>
                
                <p id="likes"><?= $postData['likes'] ?> Like(s)</p>

                <p><?= $postData['body'] ?></p><br>
                <?php foreach ($postImages as $key => $postImg) : ?>
                    <img src=<?= base_url('/public/images/post/'.$postImg['filename']) ?> width="200" height="200" alt="Attached post image">
                <?php endforeach; ?>
                
                <h3>Comments</h3>

                <?php foreach ($comments as $comment) : ?>
                    <section class="comment">
                        <p class="author"><?= $comment['user_fullname'] ?></p>
                        <p><?= $comment['body'] ?></p>
                    </section>
                <?php endforeach; ?>

                <br>

                <?= form_open(base_url('forum/'.$subjectID.'/'.$postData['id'])) ?>
                    <textarea name="comment_draft" placeholder="Comment" id="comment-draft"></textarea><br>
                    <button name="comment_btn">Add Comment</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php
        if (isset($postData)) {
            $postID = $postData['id'];
        }
    ?>

    <script>
        $('#write-post').on('click', () => {
            $('#post').hide();
            $('#post-draft').show();
        })
        $('#cancel-post').on('click', () => {
            $('#post-draft').hide();
            $('#post').show();
        })
        $('#bookmark-btn').on('change', () => {
            let postID = "<?= $postID ?>";

            let req = new XMLHttpRequest();
            req.open('POST', "<?= base_url('forum/bookmark/'.$subjectID.'/') ?>" + postID);
            req.setRequestHeader("X-Requested-With", "XMLHTTPRequest");
            req.send();
            req.onload = () => {
                console.log("Bookmark toggled");
            }
        })
        $('#like-btn').on('change', () => {
            let postID = "<?= $postID ?>";

            let req = new XMLHttpRequest();
            req.open('POST', "<?= base_url('forum/like/'.$subjectID.'/') ?>" + postID);
            req.setRequestHeader("X-Requested-With", "XMLHTTPRequest");
            req.send();
            req.onload = () => {
                console.log("Like toggled");
                let likes = parseInt($('#likes').html().split(' ')[0]);
                let change = $('#like-btn').is(':checked') ? 1 : -1;
                $('#likes').html(likes + change + ' Like(s)');
            }
        })

        Dropzone.options.postDraft = {
            url: "<?= base_url('forum/'.$subjectID) ?>",
            paramName: "images",
            maxFileSize: 2, // MB
            autoProcessQueue: false,
            uploadMultiple: true,
            init: function() {
                dzClosure = this;
                $('#post-draft').on('submit', function(e) {
                    if (dzClosure.getQueuedFiles().length !== 0) {
                        e.preventDefault();
                        e.stopPropagation();
                        dzClosure.processQueue();
                    }
                });
                this.on('sendingmultiple', function(data, xhr, formData) {
                    $("#post-draft").find("input").each(function() {
                        formData.append($(this).attr("name"), $(this).val())
                    });
                    formData.append("category", $("#category-dropdown").val());
                    formData.append("body", $("#post-body").val());
                });
                this.on('success', function(files, response) {
                    location.reload()
                });
            }
        };
        
        function suggest(str) {
            /* if (str.length == 0) {
                $('#suggestions').html('');
                return;
            } */
            let req = new XMLHttpRequest();
            req.open('GET', "<?= base_url('forum/suggest/'.$subjectID.'/') ?>" + str);
            req.setRequestHeader("X-Requested-With", "XMLHTTPRequest");
            req.send();

            req.onreadystatechange = () => {
                if (req.status == 200 && req.readyState == req.DONE) {
                    let data = JSON.parse(req.response);
                    htmlStringPosts = '';
                    for (o of data) {
                        subjectURL = "<?= base_url('forum/'.$subjectID.'/') ?>";
                        htmlStringPosts += "<li><a href=" + subjectURL + o['id'] + ">" + o['title'] + "</a></li>";
                    }
                    $('#suggestions').html(htmlStringPosts);
                }
            }
        }

        $(function() {
            $(window).on('unload', () => {
                var scrollPos = $("#posts-bar").scrollTop();
                localStorage.setItem("scrollPos", scrollPos);
            });
            if(localStorage.scrollPos) {
                $("#posts-bar").scrollTop(localStorage.getItem("scrollPos"));
            }
        });
        
    </script>
</body>

</html>