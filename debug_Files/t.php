<?php
session_start();
echo $_SESSION['username'];
echo $_SESSION['partnerName'];
if (false) { ?>
<h1>hello</h1>
<?php } ?>

<script>
setInterval(function() {
    var t = new Date();
    console.log(t.getTime());
}, 1000)
</script>