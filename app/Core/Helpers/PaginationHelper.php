<?php

declare(strict_types=1);

namespace App\Core\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationHelper
{
    /**
     * Tính toán danh sách số trang cần hiển thị, bao gồm dấu "..." khi cần.
     *
     * @param LengthAwarePaginator $paginator
     * @return array<int|string> Mảng chứa số trang (int) hoặc dấu '...' (string)
     */
    public static function calculatePageNumbers(LengthAwarePaginator $paginator): array
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $window = (int) config('crm.pagination.page_window', 2);
        $maxBeforeTruncation = (int) config('crm.pagination.max_pages_before_truncation', 7);

        if ($lastPage <= 1) {
            return [];
        }

        // Nếu tổng số trang nhỏ hơn hoặc bằng ngưỡng → hiển thị tất cả
        if ($lastPage <= $maxBeforeTruncation) {
            return range(1, $lastPage);
        }

        // Ngược lại → hiển thị: [1] ... [window quanh current] ... [last]
        $pages = [];
        $pages[] = 1;

        $start = max(2, $currentPage - $window);
        $end = min($lastPage - 1, $currentPage + $window);

        if ($start > 2) {
            $pages[] = '...';
        }

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }

        if ($end < $lastPage - 1) {
            $pages[] = '...';
        }

        $pages[] = $lastPage;

        return $pages;
    }

    /**
     * Lấy danh sách per_page options từ config.
     *
     * @return array<int>
     */
    public static function getPerPageOptions(): array
    {
        return (array) config('crm.pagination.per_page_options', [20, 50, 100, 500, 1000]);
    }

    /**
     * Lấy giá trị default per_page từ config.
     */
    public static function getDefaultPerPage(): int
    {
        return (int) config('crm.pagination.default_per_page', 20);
    }

    /**
     * Validate và trả về giá trị per_page hợp lệ.
     * Nếu giá trị không nằm trong danh sách options thì trả về default.
     */
    public static function resolvePerPage(?int $requestedPerPage): int
    {
        $options = self::getPerPageOptions();
        $default = self::getDefaultPerPage();

        if ($requestedPerPage === null || !in_array($requestedPerPage, $options, true)) {
            return $default;
        }

        return $requestedPerPage;
    }

    /**
     * Validate sort direction, chỉ cho phép 'asc' hoặc 'desc'.
     */
    public static function resolveSortDirection(?string $direction, string $default = 'desc'): string
    {
        return in_array($direction, ['asc', 'desc'], true) ? $direction : $default;
    }

    /**
     * Validate sort column chống SQL injection - chỉ cho phép cột nằm trong whitelist.
     *
     * @param string|null $column     Tên cột từ request
     * @param array       $allowedColumns  Whitelist các cột được phép sort
     * @return string|null  Trả về tên cột nếu hợp lệ, null nếu không
     */
    public static function resolveSortColumn(?string $column, array $allowedColumns): ?string
    {
        if ($column === null || !in_array($column, $allowedColumns, true)) {
            return null;
        }

        return $column;
    }
}
