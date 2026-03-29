<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\DashboardRepository;

final class AdminController
{
    public function __construct(private DashboardRepository $dashboard)
    {
    }

    public function dashboard(): void
    {
        view('admin/dashboard', [
            'pageTitle' => 'Dashboard',
            'metaDescription' => 'Vue synthèse du backoffice',
            'counters' => $this->dashboard->counters(),
            'latestArticles' => $this->dashboard->latestArticles(),
        ]);
    }
}
