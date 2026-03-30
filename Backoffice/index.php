<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

header('Location: ' . (bo_auth_check() ? '/dashboard' : '/login'));
exit;
