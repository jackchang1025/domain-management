<?php

namespace App\Services\Integrations\ShortUrl\Pagination;

use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\PagedPaginator;

class ShortUrlPaginator extends PagedPaginator
{
    public const PAGINATION_SELECTOR = 'a.next';

    private const TABLE_ROW_SELECTOR = '.dwz-list .dwz-list-table tr';

    protected function isLastPage(Response $response): bool
    {
        return $response->dom()->filter(self::PAGINATION_SELECTOR)->count() === 0;
    }

    private function parseTableData(Response $response): array
    {
        $data = [];
        $response->dom()->filter(self::TABLE_ROW_SELECTOR)->each(function ($row, $i) use (&$data) {
            if ($i < 2) {
                return;
            }

            $columns = $row->filter('td');
            if ($columns->count() === 4) {
                $editLink = $columns->eq(3)->filter('a[href*="/my_dwz/edit"]')->attr('href');
                parse_str(parse_url($editLink, PHP_URL_QUERY), $params);

                $data[] = [
                    'short_url' => $columns->eq(0)->text(),
                    'long_url' => $columns->eq(1)->text(),
                    'visit_count' => (int)$columns->eq(2)->text(),
                    'code' => $params['code'] ?? null,
                    'edit_link' => $editLink
                ];
            }
        });
        return $data;
    }

    protected function getPageItems(Response $response, Request $request): array
    {
        return $this->parseTableData($response);
    }

    protected function applyPagination(Request $request): Request
    {
        $request->query()->add('p', $this->page);

        if (isset($this->perPageLimit)) {
            $request->query()->add('per_page', $this->perPageLimit);
        }

        return $request;
    }
}
