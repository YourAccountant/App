<h1>Home</h1>

<?php
if (isset($data->user)) {
    ?>

    <ul>
        <?php
        foreach ($data->user as $key => $value) {
            echo "<li>$key: $value</li>";
        }
        ?>
    </ul>
    <?php
}

?>
