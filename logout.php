<?php
require_once __DIR__.'/config/config.php';
logout();
flash('success', 'Anda telah keluar dari sistem.');
redirect('login.php');
