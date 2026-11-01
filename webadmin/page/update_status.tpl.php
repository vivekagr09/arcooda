<!-- lock.tpl -->

<?php
if ($_GET['status'] == 1) {
?>
<div id="warning" style="text-align: center;color: black;margin: 0px 32px;margin-bottom: 5px;background: red;font-weight: 600;">Page has been updated.</div>
<br /><br />
<?php
}
?>
<script>
    // Get a reference to the warning div and close button
    var warningDiv = document.getElementById("warning");
    var closeButton = document.getElementById("closeButton");

    // Function to hide the warning div after 10 seconds
    function hideWarning() {
        warningDiv.style.display = "none";
    }

    // Set a timeout to hide the warning div after 10 seconds
    var timeoutID = setTimeout(hideWarning, 10000);
</script>
<!-- /lock.tpl -->
