<?php
$postId = $_REQUEST["postId"];
$sth = DBHandler::getPDO()->prepare("CALL getComments(?,?)");
$sth->execute([$_SESSION['user_id'], $postId]);
if ($sth->rowCount() > 0) {
  foreach ($sth->fetchAll() as $comment) {
    echo "
        <div class='card m-5'>
        <div class='card-header align-items-center'>
        <div class='row'>
        <div class='col'>
            <h5 class='m-0'>{$comment['title']}</h5>
            <p class='text-muted m-0'>{$comment['time']}</p>
        </div>"
      .
      (($comment['UserId'] == $_SESSION['user_id']) ? "<div class='col-auto'>
        <div class='dropdown'>
          <button class='btn rounded-circle text-dark' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
            <i class='bx bx-dots-vertical-rounded' ></i>
         </button>
         <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
            <li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center' data-bs-toggle='modal' data-bs-target='#createPostModal' onclick='editModal(this)' data-postId='{$comment['id']}'><i class='bx bxs-edit'></i> Edit</a></li>
            <li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center text-danger' href='../database/deletePost.php?postId={$comment['id']}'><i class='bx bxs-trash'></i> Delete</a></li>
          </ul>
        </div>
      </div> " : '') . "
        <div class='col-auto'>
        <a href='../userpages/user.php?id={$comment['UserId']}'>
          <img src='{$comment['image']}' class='rounded-circle' alt='Profile Picture' width='50' height='50'>
          </a>
        </div>
      </div>
          <div>
            
          </div>
        </div>
        <div class='card-body'>
          <div class='card-text overflow-auto' style='max-height: 200px;'>{$comment['body']}</div>
          <hr>
          <div class='justify-content-between'>
          <input  onclick='like(this, " . '"like"' . ")' type='checkbox' class='btn-check' name='{$comment['id']}' id='{$comment['id']}-like' autocomplete='off' " . ((isset($comment['positive']) && $comment['positive']) ? 'checked' : '') . ">
          <label class='btn btn-outline-success' for='{$comment['id']}-like'><i class='bx bx-like me-1'></i> LIKE</label>
          
          <input  onclick='like(this, " . '"dislike"' . ")' type='checkbox' class='btn-check' name='{$comment['id']}' id='{$comment['id']}-dislike' autocomplete='off' " . ((isset($comment['positive']) && !$comment['positive']) ? 'checked' : '') . ">
          <label class='btn btn-outline-danger' for='{$comment['id']}-dislike'><i class='bx bx-dislike me-1'></i> DISLIKE</label>
            <button type='button' class='btn btn-outline-secondary' onclick='openModal(this)' data-bs-target='#commentModal' data-postId='{$comment['id']}'><i class='bx bxs-comment-add me-1'></i> Comment</button>
          </div>
        </div>
        <div class='card-footer text-muted'>
        <span id='likes-{$comment['id']}'>{$comment['likes']}</span> likes - {$comment['comments']} comments
        </div>
      </div>
        ";
  }
}

