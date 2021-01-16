<?php
session_start();
echo $_SESSION['partnerID'];
echo $_SESSION['partnerName'];
echo $_SESSION['mode'];
echo count($_SESSION['QStack']);