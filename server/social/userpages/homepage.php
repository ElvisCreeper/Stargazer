<script>
    document.getElementById("path").innerHTML += '<li class="nav - item"><a class="nav-link" href = "javascript:getSubjects()">Universe</a></li>';
    getSubjects();
    function getSubjects() {
        let path = document.getElementById("topicPath");
        if (path != null) {
            path.remove();
        }
        path = document.getElementById("subjectPath");
        if (path != null) {
            path.remove();
        }
        path = document.getElementById("postPath");
        if (path != null) {
            path.remove();
        }
        let button = document.getElementById("createTopicButton");
        if (button != null) {
            button.remove();
        }
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("choices").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "../database/subject.php", true);
        xmlhttp.send();
    }
    function getTopics(subject, id) {
        let path = document.getElementById("topicPath");
        if (path != null) {
            path.remove();
        }
        path = document.getElementById("postPath");
        if (path != null) {
            path.remove();
        }
        path = document.getElementById("subjectPath");
        if (path == null) {
            updatePath("<li class='nav - item' id='subjectPath'><a class='nav-link' href = 'javascript:getTopics(" + '"' + subject + '"' + ", " + id + ")'>" + subject + "</a></li>");
        }
        let button = document.getElementById("createTopicButton");
        if (button == null) {
            updateSearchbar("<a type='button' class='bx bx-plus-circle btn btn-primary rounded-circle p-3 mr-3' href='topicCreation.php' id='createTopicButton'></a>");
        }
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("choices").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "../database/topic.php?subject=" + id + "&subjectName=" + subject, true);
        xmlhttp.send();
    }
    function getTabs(topic, id) {
        let path = document.getElementById("postPath");
        if (path != null) {
            path.remove();
        }
        path = document.getElementById("topicPath");
        if (path == null) {
            updatePath("<li class='nav - item' id='topicPath'><a class='nav-link' href = 'javascript:getTabs(" + '"' + topic + '"' + ", " + id + ")'>" + topic + "</a></li>");
        }
        let button = document.getElementById("createTopicButton");
        if (button != null) {
            button.remove();
        }
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("choices").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "../database/tab.php?topic=" + id + "&topicName=" + topic + "&topicName=" + topic, true);
        xmlhttp.send();
    }
    function getPosts(tab, id) {
        path = document.getElementById("postPath");
        if (path == null) {
            updatePath("<li class='nav - item' id='postPath'><a class='nav-link' href = 'javascript:getPosts(" + '"' + tab + '"' + ", " + id + ")'>" + tab + "</a></li>");
        }
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("choices").innerHTML = this.responseText;
                loadCommentScript();
            }
        };
        xmlhttp.open("GET", "../database/post.php?tab=" + id + "&tabName=" + tab, true);
        xmlhttp.send();
    }
    function updatePath(el) {
        console.log(el);
        document.getElementById("path").innerHTML += el;
    }
    function updateSearchbar(el) {
        console.log(el);
        document.getElementById("searchbar").innerHTML = el + document.getElementById("searchbar").innerHTML;
    }
    

</script>

<?php
//galaxy
$sth = DBHandler::getPDO()->prepare("SELECT * FROM Subject");
$sth->execute();
echo ('<table>');
foreach ($sth->fetchAll() as $row) {
    echo "<tr>";
    echo "<td><a class='btn btn-primary' href='../userpages/subject.php?id={$row["id"]}' role='button'>{$row["Name"]}</a></td>";
    echo '</tr>';
}
echo ('</table>');