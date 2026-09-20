<?php
require_once __DIR__.'/config/config.php';

flash('error', 'Login Google dinonaktifkan. Silakan masuk dengan email dan password.');
redirect('login.php');
