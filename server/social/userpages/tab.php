<?php
$sth = DBHandler::getPDO()->prepare("SELECT Subject.Name AS subjectName, Subject.id AS subjectId, tt.topicName, tt.topicId, tt.tabName, tt.tabId FROM (SELECT SubjectId, Topic.Name AS topicName, Topic.Id AS topicId, Tab.Name AS tabName, Tab.Id AS tabId FROM Tab INNER JOIN Topic ON TopicId = Topic.Id) AS tt INNER JOIN SUBJECT ON tt.SubjectId = Subject.id WHERE tt.tabId = ?");
$sth->execute([$_REQUEST["id"]]);
$info = $sth->fetch();
?>
<script>
    let path = document.getElementById("path");
    path.innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/homepage.php">Universe</a></li>';
    path.innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/subject.php?id=' + <?php echo $info["subjectId"]; ?> + '">' + "<?php echo $info["subjectName"] ?>" + '</a></li>';
    path.innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/topic.php?id=' + <?php echo $info["topicId"]; ?> + '">' + "<?php echo $info["topicName"] ?>" + '</a></li>';
    path.innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/tab.php?id=' + <?php echo $info["tabId"]; ?> + '">' + "<?php echo $info["tabName"] ?>" + '</a></li>';
</script>

<script src="../editor.js"></script>
<script>
    function like(button, action) {
        var likeLabel = document.getElementById("likes-" + button.name);

        if (!button.checked) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "../database/like.php", true);
            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.send("postId=" + button.name + "&action=unlike");
            if (action == "like") {
                likeLabel.innerHTML = parseInt(likeLabel.innerHTML) - 1;
            }
            if (action == "dislike") {
                likeLabel.innerHTML = parseInt(likeLabel.innerHTML) + 1;
            }
        }
        else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "../database/like.php", true);
            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.send("postId=" + button.name + "&action=" + action);
            var c = 1;
            if (action == "like") {
                var buttonDislike = document.getElementById(button.name + "-dislike");
                if (buttonDislike.checked) {
                    c = 2;
                    buttonDislike.checked = false;
                }
                likeLabel.innerHTML = parseInt(likeLabel.innerHTML) + c;
            }
            if (action == "dislike") {
                var buttonLike = document.getElementById(button.name + "-like");
                if (buttonLike.checked) {
                    c = 2;
                    buttonLike.checked = false;
                }
                likeLabel.innerHTML = parseInt(likeLabel.innerHTML) - c;
            }

        }
    }
    /*function getEditor() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("choices").innerHTML = this.responseText;
                var scriptSrc = '../editor.js';
                if (!isScriptLoaded(scriptSrc)) {
                    var script = document.createElement('script');
                    script.src = scriptSrc;
                    document.head.appendChild(script);
                }
            }
        };
        xmlhttp.open("GET", "../include/createPost.php", true);
        xmlhttp.send();
    }*/
    var commentModalId = 0;
    function openModal(button) {
        // Duplica il modal
        var modalClone = document.getElementById('commentModal').cloneNode(true);
        // Aggiunge un nuovo id per evitare duplicati
        modalClone.id = 'commentModal_' + commentModalId;
        commentModalId += 1;
        // Aggiunge il modal al corpo del documento
        document.body.appendChild(modalClone);
        // Mostra il modal
        var myModal = new bootstrap.Modal(modalClone);
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var content = this.responseText;
                var modalBody = modalClone.querySelector('.modal-body');
                modalClone.querySelector('#addCommentButton').setAttribute('data-postId', button.getAttribute('data-postId'));
                modalBody.innerHTML = content;
                myModal.show();
            }
        };
        xmlhttp.open('GET', '../database/comment.php?postId=' + button.getAttribute('data-postId'), true);
        xmlhttp.send();
    }

    function createPostModal(button) {
        var modal = document.getElementById('createPostModal');
        var modalBody = modal.querySelector('#content');
        var modalTitle = modal.querySelector('#title');
        var modalLabel = modal.querySelector('#createPostModalLabel');
        var modalSubmit = modal.querySelector('#submitButton');
        modalBody.innerHTML = "Lorem Ipsum";
        modalLabel.innerHTML = "<p>Create a post</p>";
        modalTitle.value = "";
        modalSubmit.innerHTML = "POST";
    }
    let postCommentingId = null;
    function createCommentModal(button) {
        var modal = document.getElementById('createPostModal');
        postCommentingId = button.getAttribute('data-postId');
        var modalBody = modal.querySelector('#content');
        var modalTitle = modal.querySelector('#title');
        var modalLabel = modal.querySelector('#createPostModalLabel');
        var modalSubmit = modal.querySelector('#submitButton');
        modalBody.innerHTML = "";
        modalLabel.innerHTML = "<p>Create a comment</p>";
        modalTitle.value = "";
        modalSubmit.innerHTML = "REPLY";
    }
    function editModal(button) {
        var modal = document.getElementById('createPostModal');
        postCommentingId = button.getAttribute('data-postId');
        var title = document.getElementById(button.getAttribute('data-postId') + '-title').innerHTML;
        var content = document.getElementById(button.getAttribute('data-postId') + '-body').innerHTML;
        var modalBody = modal.querySelector('#content');
        var modalTitle = modal.querySelector('#title');
        var modalLabel = modal.querySelector('#createPostModalLabel');
        var modalSubmit = modal.querySelector('#submitButton');
        modalBody.innerHTML = content;
        modalLabel.innerHTML = "<p>Edit post</p>";
        modalTitle.value = title;
        modalSubmit.innerHTML = "EDIT";
    }
</script>

<div class="solar-system container mt-0" style="background:transparent;">

    <?php
    $tab = $_REQUEST["id"];
    //$_SESSION["tab"] = $tab;
//$_SESSION["tabName"] = $_REQUEST["tabName"];
    $sth = DBHandler::getPDO()->prepare("CALL getPosts(?,?)");
    $sth->execute([$_SESSION["user_id"], $tab]);

    //<div class='container d-flex align-items-center mt-0' style='background:transparent;'>
    $currentPost = $sth->fetch();

    echo "<div id='planet-display' class='ratio ratio-1x1'>


<div id='postCarousel' class='carousel slide d-flex align-items-center' data-interval='false'>
  <div class='carousel-inner'>
    <div class='carousel-item active p-5'>  
      <div class='card m-5'>
        <div class='card-header align-items-center'>
          <div class='row'>
            <div class='col'>
              <h5 class='m-0' id='{$currentPost['id']}-title'>{$currentPost['title']}</h5>
              <p class='text-muted m-0'>{$currentPost['time']}</p>
            </div>" .
        (($currentPost['UserId'] == $_SESSION['user_id']) ? "<div class='col-auto'>
            <div class='dropdown'>
              <button class='btn rounded-circle text-dark' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                <i class='bx bx-dots-vertical-rounded' ></i>
             </button>
             <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                <li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center' data-bs-toggle='modal' data-bs-target='#createPostModal' onclick='editModal(this)' data-postId='{$currentPost['id']}'><i class='bx bxs-edit'></i> Edit</a></li>
                <li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center text-danger' href='../database/deletePost.php?postId={$currentPost['id']}'><i class='bx bxs-trash'></i> Delete</a></li>
              </ul>
            </div>
          </div> " : '') .
        "<div class='col-auto'>
              <a href='../userpages/user.php?id={$currentPost['UserId']}'>
                <img src='{$currentPost['image']}' class='rounded-circle' alt='Profile Picture' width='50' height='50'>
              </a>
            </div>
          </div>
          <div>    
          </div>
        </div>
        <div class='card-body'>
          <div class='card-text overflow-auto' style='max-height: 200px;' id='{$currentPost['id']}-body'>{$currentPost['body']}</div>
          <hr>
          <div class='justify-content-between'>
          <input  onclick='like(this, " . '"like"' . ")' type='checkbox' class='btn-check' name='{$currentPost['id']}' id='{$currentPost['id']}-like' autocomplete='off' " . ((isset($currentPost['positive']) && $currentPost['positive']) ? 'checked' : '') . ">
          <label class='btn btn-outline-success' for='{$currentPost['id']}-like'><i class='bx bx-like me-1'></i> LIKE</label>
          
          <input  onclick='like(this, " . '"dislike"' . ")' type='checkbox' class='btn-check' name='{$currentPost['id']}' id='{$currentPost['id']}-dislike' autocomplete='off' " . ((isset($currentPost['positive']) && !$currentPost['positive']) ? 'checked' : '') . ">
          <label class='btn btn-outline-danger' for='{$currentPost['id']}-dislike'><i class='bx bx-dislike me-1'></i> DISLIKE</label>

            <button type='button' class='btn btn-outline-secondary' data-bs-toggle='modal' data-bs-target='#commentModal' data-postId='{$currentPost['id']}'><i class='bx bxs-comment-add me-1'></i> Comment</button>
          </div>
        </div>
        <div class='card-footer text-muted'>
          <span id='likes-{$currentPost['id']}'>{$currentPost['likes']}</span> likes - {$currentPost['comments']} comments
        </div>
      </div>

    </div>";
    foreach ($sth->fetchAll() as $post)
        echo "
    <div class='carousel-item p-5'>
    <div class='card  m-5'>
    <div class='card-header align-items-center'>
    <div class='row'>
    <div class='col'>
        <h5 class='m-0' id='{$post['id']}-title'>{$post['title']}</h5>
        <p class='text-muted m-0'>{$post['time']}</p>
    </div>"
            .
            (($post['UserId'] == $_SESSION['user_id']) ? "<div class='col-auto'>
            <div class='dropdown'>
              <button class='btn rounded-circle text-dark' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                <i class='bx bx-dots-vertical-rounded' ></i>
             </button>
             <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                <li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center' data-bs-toggle='modal' data-bs-target='#createPostModal' onclick='editModal(this)' data-postId='{$post['id']}'><i class='bx bxs-edit'></i> Edit</a></li>
                <li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center text-danger' href='../database/deletePost.php?postId={$post['id']}'><i class='bx bxs-trash'></i> Delete</a></li>
              </ul>
            </div>
          </div> " : '') . "
    <div class='col-auto'>
    <a href='../userpages/user.php?id={$post['UserId']}'>
      <img src='{$post['image']}' class='rounded-circle' alt='Profile Picture' width='50' height='50'>
      </a>
    </div>
  </div>
    </div>
    <div class='card-body'>
      <div class='card-text overflow-auto' style='max-height: 200px;' id='{$post['id']}-body'>{$post['body']}</div>
      <hr>
      <div class='justify-content-between'>
      <input  onclick='like(this, " . '"like"' . ")' type='checkbox' class='btn-check' name='{$post['id']}' id='{$post['id']}-like' autocomplete='off' " . ((isset($post['positive']) && $post['positive']) ? 'checked' : '') . ">
      <label class='btn btn-outline-success' for='{$post['id']}-like'><i class='bx bx-like me-1'></i> LIKE</label>
      
      <input  onclick='like(this, " . '"dislike"' . ")' type='checkbox' class='btn-check' name='{$post['id']}' id='{$post['id']}-dislike' autocomplete='off' " . ((isset($post['positive']) && !$post['positive']) ? 'checked' : '') . ">
      <label class='btn btn-outline-danger' for='{$post['id']}-dislike'><i class='bx bx-dislike me-1'></i> DISLIKE</label>

      <button type='button' class='btn btn-outline-secondary' data-bs-toggle='modal' data-bs-target='#commentModal' data-postId='{$post['id']}'><i class='bx bxs-comment-add me-1'></i> Comment</button>
      </div>
    </div>
    <div class='card-footer text-muted'>
     <span id='likes-{$post['id']}'>{$post['likes']}</span> likes - {$post['comments']} comments
    </div>
  </div>
    </div>
  ";

    echo "</div>
  <button class='carousel-control-prev' type='button' data-bs-target='#postCarousel' data-bs-slide='prev'>
    <span class='carousel-control-prev-icon' aria-hidden='true'></span>
    <span class='visually-hidden'>Previous</span>
  </button>
  <button class='carousel-control-next' type='button' data-bs-target='#postCarousel' data-bs-slide='next'>
    <span class='carousel-control-next-icon' aria-hidden='true'></span>
    <span class='visually-hidden'>Next</span>
  </button></div></div>";
    ?>

    <button type='button'
        class='bx bx-message-alt-add btn btn-primary position-absolute bottom-0 end-0 rounded-circle m-5 p-3'
        data-bs-toggle='modal' data-bs-target='#createPostModal' onclick='createPostModal(this)'></button>

    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id='commentModalBody'>
                    <!-- Contenuto del modal -->
                </div>
                <div class="modal-footer">
                    <button type="button" id='addCommentButton' class="btn btn-secondary bx bx-message-alt-add"
                        data-bs-toggle='modal' data-bs-target='#createPostModal'
                        onclick="createCommentModal(this)"></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal modal-xl fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel"
        aria-hidden="true" style="z-index:7000;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPostModalLabel">
                        <p>Create a post</p>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <label for='title'>Title</label>
                        <input type="textarea" , id='title' , class="mb-3">
                        <br>
                        <div class="toolbar">
                            <div class="head">
                                <select onchange="formatDoc('formatBlock', this.value)">
                                    <option value="" selected="" hidden="" disabled="">Format</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                    <option value="h4">Heading 4</option>
                                    <option value="h5">Heading 5</option>
                                    <option value="h6">Heading 6</option>
                                    <option value="p">Paragraph</option>
                                </select>
                                <select onchange="formatDoc('fontSize', this.value)">
                                    <option value="" selected="" hidden="" disabled="">Font size</option>
                                    <option value="1">Extra small</option>
                                    <option value="2">Small</option>
                                    <option value="3">Regular</option>
                                    <option value="4">Medium</option>
                                    <option value="5">Large</option>
                                    <option value="6">Extra Large</option>
                                    <option value="7">Big</option>
                                </select>
                                <div class="color">
                                    <span>Color</span>
                                    <input type="color"
                                        oninput="formatDoc('foreColor', this.value); this.value='#000000';">
                                </div>
                                <div class="color">
                                    <span>Background</span>
                                    <input type="color"
                                        oninput="formatDoc('hiliteColor', this.value); this.value='#000000';">
                                </div>
                            </div>
                            <div class="btn-toolbar">
                                <button onclick="formatDoc('undo')"><i class='bx bx-undo'></i></button>
                                <button onclick="formatDoc('redo')"><i class='bx bx-redo'></i></button>
                                <button onclick="formatDoc('bold')"><i class='bx bx-bold'></i></button>
                                <button onclick="formatDoc('underline')"><i class='bx bx-underline'></i></button>
                                <button onclick="formatDoc('italic')"><i class='bx bx-italic'></i></button>
                                <button onclick="formatDoc('strikeThrough')"><i
                                        class='bx bx-strikethrough'></i></button>
                                <button onclick="formatDoc('justifyLeft')"><i class='bx bx-align-left'></i></button>
                                <button onclick="formatDoc('justifyCenter')"><i class='bx bx-align-middle'></i></button>
                                <button onclick="formatDoc('justifyRight')"><i class='bx bx-align-right'></i></button>
                                <button onclick="formatDoc('justifyFull')"><i class='bx bx-align-justify'></i></button>
                                <button onclick="formatDoc('insertOrderedList')"><i class='bx bx-list-ol'></i></button>
                                <button onclick="formatDoc('insertUnorderedList')"><i
                                        class='bx bx-list-ul'></i></button>
                                <button onclick="addLink()"><i class='bx bx-link'></i></button>
                                <button onclick="formatDoc('unlink')"><i class='bx bx-unlink'></i></button>

                                <!-- <button id="show-code" data-active="false">&lt;/&gt;</button> -->
                            </div>
                        </div>
                        <div id="content" contenteditable="true" spellcheck="false">
                            Lorem, ipsum.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="submitButton"
                        onclick="<?php echo 'submit(' . $tab . ',postCommentingId, this.innerHTML)'; ?>">POST</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var myModal = document.getElementById('commentModal');
    myModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var content = this.responseText;
                var modalBody = document.getElementById('commentModalBody');
                modalBody.innerHTML = content;
                document.getElementById('addCommentButton').setAttribute('data-postId', button.getAttribute('data-postId'));
            }
        };
        xmlhttp.open('GET', '../database/comment.php?postId=' + button.getAttribute('data-postId'), true);
        xmlhttp.send();
    });
</script>