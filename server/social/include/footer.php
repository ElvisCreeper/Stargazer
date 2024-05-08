<?php
// read pages.json into $json which is a string
$json = file_get_contents('../include/pages.json');

// get the name of the current page
$pageName = basename($_SERVER['PHP_SELF']);

$obj = json_decode($json);

if (!in_array($pageName, $obj->AJAXPages) && !in_array($pageName, $obj->APIPages)) {
    echo '
<footer class="footer mt-auto p-4 bg-dark text-white text-center">
        <p>Footer</p>
    </footer>
</body>

</html>';
}